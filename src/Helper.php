<?php

namespace EWA\RCTool;

defined('ABSPATH') || exit;

/**
 * Class Helper
 */

class Helper extends Singleton
{
    /**
     * Check if current user belong to distributor role.
     * @return boolean
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
     * Check if a product in cart that has any special option.
     * @return boolean
     */
    public function is_special_option_product_in_cart()
    {
        $found = false;
        foreach (WC()->cart->get_cart() as $cart_item) {
            if (
                (isset($cart_item[SpecialProductOptions::WELDED_FIELD]) && !empty($cart_item[SpecialProductOptions::WELDED_FIELD])) ||
                (isset($cart_item[SpecialProductOptions::ADJUSTABLE_FIELD]) && !empty($cart_item[SpecialProductOptions::ADJUSTABLE_FIELD])) ||
                (isset($cart_item[SpecialProductOptions::SIZE_FIELD]) && !empty($cart_item[SpecialProductOptions::SIZE_FIELD])) ||
                (isset($cart_item[SpecialProductOptions::NOTES_FIELD]) && !empty($cart_item[SpecialProductOptions::NOTES_FIELD]))
            ) {
                $found = true;
                break;
            }
        }

        return $found;
    }

    /**
     * Check if single/current product is special.
     *
     * @param array $post
     * @return boolean
     */
    public function is_special_option_product($post)
    {
        if (
            (isset($post[SpecialProductOptions::WELDED_FIELD]) && !empty($post[SpecialProductOptions::WELDED_FIELD])) ||
            (isset($post[SpecialProductOptions::ADJUSTABLE_FIELD]) && !empty($post[SpecialProductOptions::ADJUSTABLE_FIELD])) ||
            (isset($post[SpecialProductOptions::SIZE_FIELD]) && !empty($post[SpecialProductOptions::SIZE_FIELD])) ||
            (isset($post[SpecialProductOptions::NOTES_FIELD]) && !empty($post[SpecialProductOptions::NOTES_FIELD]))
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if cart is empty.
     *
     * @return boolean
     */
    public function is_cart_empty()
    {
        if (WC()->cart->get_cart_contents_count() == 0) {
            return true;
        } else {
            return false;
        }
    }
}
