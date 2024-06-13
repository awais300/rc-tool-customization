<?php

namespace EWA\RCTool;

use EWA\RCTool\Admin\Acf\UserFields;

defined('ABSPATH') || exit;

/**
 * Class Pricing
 * Handles pricing customization based on different pricing levels.
 *
 * @package EWA\RCTool
 */
class Pricing extends Singleton
{
    /**
     * The price break levels.
     *
     * @var string ACF_PRICE_BREAK_LEVELS
     */
    public const ACF_PRICE_BREAK_LEVELS = 'price_break_levels';

    /**
     * The quantity purchased levels.
     *
     * @var string ACF_QTY_PURCHASED_LEVELS
     */
    public const ACF_QTY_PURCHASED_LEVELS = 'qty_purchased_levels';

    /**
     * The historical quantity.
     *
     * @var string HISTORICAL
     */
    public const HISTORICAL = 'historical';

    /**
     * The qty purchased.
     *
     * @var string QTY_PURCHASED
     */
    public const QTY_PURCHASED = 'qty_purchased';

    /**
     * @var $loader The template loader.
     **/
    private $loader = null;

    /**
     * @var IS_PRODUCT_RFQ The key to detect if its RFQ.
     **/
    public const IS_PRODUCT_RFQ = '_is_product_rfq';

    /**
     * @var PRODUCT_RFQ_PRICE The RFQ price.
     **/
    public const PRODUCT_NEW_PRICE = '_product_new_price';


    /**
     * Construct the Pricing class.
     */
    public function __construct()
    {
        add_action('woocommerce_before_calculate_totals', array($this, 'update_cart_item_prices'), 2002, 1);
    }

    /**
     * Update the prices of cart items based on pricing levels.
     *
     * @param object $cart The WooCommerce cart object.
     * 
     * @return void
     */
    public function update_cart_item_prices($cart)
    {
        if (!(Helper::get_instance())->is_distributor()) {
            foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
                $cart_content = WC()->cart->cart_contents;
                $cart_content[$cart_item_key][self::IS_PRODUCT_RFQ] = 'rfq_yes';
                WC()->cart->set_cart_contents($cart_content);
            }
            return;
        }

        if (is_admin() && !defined('DOING_AJAX')) {
            return;
        }

        // Avoid hook repetition (when using price calculations for example)
        if (did_action('woocommerce_before_calculate_totals') >= 2) {
            return;
        }

        // Loop through cart items
        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            $price_level = null;
            $item_sku = $this->get_pc_sku($cart_item);

            // For testing purpose only.
            /*$product_id = $cart_item['data']->get_id();
            if ($product_id == '19235') {
                $item_sku = 'JU963624.UGH';
            }*/

            $series_type = $this->detect_series_by_sku($item_sku);
            if ($item_sku && $series_type) {
                if ($series_type === self::HISTORICAL) {
                    $price_level = $this->get_historical_pricing_level();
                }

                if ($series_type === self::QTY_PURCHASED) {
                    $price_level = $this->get_qty_purchased_pricing_level($item_sku, $cart_item['quantity']);
                }
            }

            $row = $this->get_pricing_levels_by_sku($item_sku);
            if (isset($row->{$price_level})) {
                $extra_price = $this->get_extra_price($cart_item);
                $new_price = $row->{$price_level} + $extra_price;
                $cart_item['data']->set_price($new_price);

                $cart_content = WC()->cart->cart_contents;
                $cart_content[$cart_item_key][self::IS_PRODUCT_RFQ] = 'rfq_no';
                $cart_content[$cart_item_key][self::PRODUCT_NEW_PRICE] = $new_price;
                WC()->cart->set_cart_contents($cart_content);
            } else {
                $cart_item['data']->set_price(0);

                $cart_content = WC()->cart->cart_contents;
                $cart_content[$cart_item_key][self::IS_PRODUCT_RFQ] = 'rfq_yes';
                WC()->cart->set_cart_contents($cart_content);
            }
        }
    }

    /**
     * Get extra price if configurable proudct has any.
     *
     * @param array The cart item
     *
     * @return float The extra price.
     */
    function get_extra_price($cart_item)
    {
        $full_extra_price = 0;
        if (mkl_pc_is_configurable($cart_item['product_id']) && isset($cart_item['configurator_data']) && is_array($cart_item['configurator_data'])) {

            foreach ($cart_item['configurator_data'] as $layer) {
                if (!$layer) {
                    continue;
                }

                if (apply_filters('mkl_pc/extra_price/add_extra_price', true, $layer, $cart_item)) {
                    $extra_price = $layer->get_choice('extra_price');
                    $full_extra_price = $full_extra_price + $extra_price;
                }
            }
        }

        return floatval($full_extra_price);
    }

    /**
     * Extract product SKU from the cart item.
     *
     * @param array $cart_item The cart item.
     * @return string|bool The product SKU or false if not found.
     */
    public function get_pc_sku($cart_item)
    {
        $configurator_data = $cart_item['configurator_data'];
        $sku = [];

        foreach ($configurator_data as $layer) {
            if ($layer && $layer->is_choice()) {
                if ($layer->get_choice('sku')) {
                    $sku[] = $layer->get_choice('sku');
                }
            }
        }

        if (count($sku)) {
            $final_sku = implode(mkl_pc('settings')->get('sku_glue', ''), $sku);
            return $final_sku;
        }

        return false;
    }

    /**
     * Get pricing levels for a product SKU.
     *
     * @param string $sku The product SKU.
     * @return object|null Pricing levels or null if not found.
     */
    public function get_pricing_levels_by_sku($sku)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . Schema::RCTOOL_PRICING_TABLE;
        $query = $wpdb->prepare("SELECT * FROM $table_name WHERE sku = %s", $sku);

        $result = $wpdb->get_row($query);
        return $result;
    }

    /**
     * Detect the pricing series type based on SKU.
     *
     * @param string $sku The product SKU.
     * @return string|bool The series type or false if not found.
     */
    public function detect_series_by_sku($sku)
    {
        $historical_based = $this->is_historical_pricing_series($sku);
        $qty_based = $this->is_qty_purchased_series($sku);

        if ($historical_based === true) {
            return self::HISTORICAL;
        }

        if ($qty_based === true) {
            return self::QTY_PURCHASED;
        }

        return false;
    }

    /**
     * Check if a SKU has historical pricing series.
     *
     * @param string $sku The product SKU.
     * @return bool True if historical pricing series exists, false otherwise.
     */
    public function is_historical_pricing_series($sku)
    {
        $series = get_field(self::ACF_PRICE_BREAK_LEVELS, 'option');
        if ($series) {
            foreach ($series as $single_series) {
                foreach ($single_series as $series_name) {
                    if (strncasecmp($series_name, $sku, strlen(trim($series_name))) === 0) {
                        return true;
                    }
                }
            }

            return false;
        } else {
            return false;
        }
    }

    /**
     * Get the historical pricing level for the current user.
     *
     * @return string|null The historical pricing level or null if not found.
     */
    public function get_historical_pricing_level()
    {
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $price_range = get_user_meta($user_id, UserFields::META_HISTORICAL_PRICE_BREAK_LEVEL, true);

            $level = (Helper::get_instance())->get_historical_level($price_range);
            return $level;
        } else {
            return null;
        }
    }

    /**
     * Check if a SKU has quantity purchased pricing series.
     *
     * @param string $sku The product SKU.
     * @return bool True if quantity purchased pricing series exists, false otherwise.
     */
    public function is_qty_purchased_series($sku)
    {
        $series = get_field(self::ACF_QTY_PURCHASED_LEVELS, 'option');
        if ($series) {
            foreach ($series as $single_series) {
                foreach ($single_series as $series_name) {
                    if (strncasecmp($series_name, $sku, strlen(trim($series_name))) === 0) {
                        return true;
                    }
                }
            }

            return false;
        } else {
            return false;
        }
    }

    /**
     * Get the quantity purchased pricing level for a SKU and quantity.
     *
     * @param string $sku The product SKU.
     * @param int $qty The quantity purchased.
     * @return string|null The quantity purchased pricing level or null if not found.
     */
    public function get_qty_purchased_pricing_level($sku, $qty)
    {
        $series = get_field(self::ACF_QTY_PURCHASED_LEVELS, 'option');
        if ($series) {
            foreach ($series as $single_series) {
                foreach ($single_series as $series_name) {
                    if (strncasecmp($series_name, $sku, strlen(trim($series_name))) === 0) {
                        $level = (Helper::get_instance())->get_qty_purchased_level($single_series, $qty);
                        return $level;
                    }
                }
            }

            return null;
        } else {
            return null;
        }
    }
}
