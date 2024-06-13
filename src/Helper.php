<?php

namespace EWA\RCTool;

defined('ABSPATH') || exit;

/**
 * Class Helper
 * Provides helper methods for various tasks.
 */
class Helper extends Singleton
{
    /**
     * Check if the current user belongs to the distributor role.
     *
     * @return boolean True if the user is a distributor, false otherwise.
     */
    public function is_distributor()
    {
        if (get_current_user_id() == 0) {
            return false;
        }

        $user = wp_get_current_user();
        $allowed_roles = array('distributor', 'administrator');

        if (array_intersect($allowed_roles, $user->roles)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Check where the cart item has RFQ item in it.
     *
     * @param $cart_item array
     *
     * @return bool true if RFQ found, otherwise false.
     **/
    public function cart_has_rfq($cart_item)
    {
        if (
            (isset($cart_item[Pricing::IS_PRODUCT_RFQ]) && $cart_item[Pricing::IS_PRODUCT_RFQ] == 'rfq_yes') ||
            (isset($cart_item[SpecialProductOptions::IS_SPECIAL_PRODUCT_RFQ]) && $cart_item[SpecialProductOptions::IS_SPECIAL_PRODUCT_RFQ] == 'rfq_yes')
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check If cart has a RFQ item in it.
     * 
     * This is a product whose price couludn't find against SKU or SKU don't exist at all. 
     * OR
     * This is a product who has special option in it.
     *
     * @return bool
     **/
    public function has_rfq_in_cart()
    {
        $cart = WC()->cart;

        if ($cart->is_empty()) {
            return false;
        }

        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            if (isset($cart_item[Pricing::IS_PRODUCT_RFQ]) && $cart_item[Pricing::IS_PRODUCT_RFQ] == 'rfq_yes') {
                return true;
            }

            if(isset($cart_item[SpecialProductOptions::IS_SPECIAL_PRODUCT_RFQ]) && $cart_item[SpecialProductOptions::IS_SPECIAL_PRODUCT_RFQ] == 'rfq_yes') {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the cart is empty.
     *
     * @return boolean True if the cart is empty, false otherwise.
     */
    public function is_cart_empty()
    {
        if (WC()->cart->get_cart_contents_count() == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the historical level based on the given range.
     *
     * @param string $range The range of quantities.
     * @return string The historical level.
     */
    public function get_historical_level($range)
    {
        $level = array(
            '2-9' => 'level1',
            '10-24' => 'level2',
            '25-49' => 'level3',
            '50+' => 'level4',
        );

        return $level[trim($range)];
    }

    /**
     * Get the quantity purchased level based on the given range and quantity.
     *
     * @param array $arr The range array.
     * @param int $qty The quantity.
     * @return string|false The quantity purchased level.
     */
    public function get_qty_purchased_level($arr, $qty)
    {
        array_shift($arr);
        foreach ($arr as $key => $value) {
            // Split the range if it contains a hyphen
            $range = explode('-', $value);

            // If it's a single value and does not contain a plus sign
            if (count($range) == 1 && substr($range[0], -1) != '+' && $qty == $range[0]) {
                return $key;
            }

            // If it's a range
            if (count($range) == 2 && $qty >= $range[0] && $qty <= $range[1]) {
                return $key;
            }

            // If it's with a plus sign
            if (count($range) == 1 && substr($range[0], -1) == '+' && $qty >= $range[0]) {
                return $key;
            }
        }

        // If the number is not in any range
        return false;
    }
}
