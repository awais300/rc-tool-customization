<?php

namespace EWA\RCTool;

use EWA\RCTool\Admin\Product\LeadTime;
use EWA\RCTool\Admin\Settings;
use WC_Product;

defined('ABSPATH') || exit;

/**
 * Class SingleProduct
 * Handles single product display customization.
 *
 * @package EWA\RCTool
 */
class SingleProduct
{
    /**
     * Construct the class.
     */
    public function __construct()
    {
        add_action('woocommerce_before_add_to_cart_form', array($this, 'display_lead_time_message'));
        add_filter('woocommerce_product_single_add_to_cart_text', array($this, 'custom_add_to_cart_text'), 10, 2);
    }

    /**
     * Display the lead time message.
     *
     * @return void
     * @throws \Exception If the product is not a WooCommerce product.
     */
    public function display_lead_time_message()
    {
        global $post;
        if (!is_product() || empty($post)) {
            return;
        }

        $product = wc_get_product(get_the_ID());
        if (!is_a($product, 'WC_Product')) {
            throw new \Exception(__('Not a WooCommerce product', 'rct-customization'));
        }

        $lead_time_message = !empty($product->get_meta(LeadTime::PRODUCT_LEAD_TIME_FIELD)) ? $product->get_meta(LeadTime::PRODUCT_LEAD_TIME_FIELD) : get_option(Settings::GLOBAL_LEAD_TIME_FIELD);

        if (!empty($lead_time_message)) {
            echo '<div class="rct-lead-time-message rct-woocommerce-store-notice">';
            echo '<h5><i class="fa fa-truck" aria-hidden="true"></i> Lead Time</h5>';
            echo '<p>' . $lead_time_message . '</p>';
            echo '</div>';
        }
    }

    /**
     * Add custom text for add to cart button.
     *
     * @param string $add_to_cart_text The original text.
     * @param WC_Product $product The WooCommerce product.
     * @return string The modified text.
     */
    public function custom_add_to_cart_text($add_to_cart_text, $product)
    {
        if (is_admin()) {
            return;
        }

        if (!(Helper::get_instance())->is_distributor()) {
            return __('Add to RFQ', 'rct-customization');
        }

        return $add_to_cart_text;
    }
}
