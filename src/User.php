<?php

namespace EWA\RCTool;

defined('ABSPATH') || exit;

/**
 * Class User
 * @package EWA\RCTool
 */
class User
{
    /**
     * Construct the plugin.
     */
    public function __construct()
    {
        add_filter('login_redirect', array($this, 'my_custom_login_redirect'), 10, 3);
        add_filter('woocommerce_thankyou_order_received_text', array($this, 'custom_thank_you_message'), 10, 2);
        add_filter('woocommerce_account_menu_items', array($this, 'add_custom_menu_item'));
    }

    /**
     * Redirects users after login based on conditions.
     *
     * @param string $redirect_to    The redirect destination URL.
     * @param string $request        The requested redirect destination URL.
     * @param object $user           The user object.
     *
     * @return string                The modified redirect destination URL.
     */
    public function my_custom_login_redirect($redirect_to, $request, $user)
    {
        // Check if the user is not an admin and is logging in from the login page
        if (!is_wp_error($user) && !user_can($user, 'manage_options') && isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'wp-login.php') !== false) {
            // Redirect to the my-account page
            return home_url('/my-account');
        }
        // If user is an admin, or not logging in from the login page, do not change the redirection
        return $redirect_to;
    }

    /**
     * Custom thank you message
     *
     * @param string $thank_you_text The original thank you message
     * @param WC_Order $order The order object
     * @return string The custom thank you message
     */
    public function custom_thank_you_message($thank_you_text, $order)
    {
        $thank_you_text = 'Thank you for your order. A confirmation of this order will be emailed to you within two business days.';
        return $thank_you_text;
    }

    /**
     * Add custom menu item to My Account menu
     *
     * @param array $items Existing menu items
     * @return array Modified menu items
     */
    public function add_custom_menu_item($items)
    {
        // Add new menu item before the logout link
        $logout = $items['customer-logout'];
        unset($items['customer-logout']);

        $items['shop'] = 'Shop & Customise Online';
        $items['customer-logout'] = $logout;

        return $items;
    }
}
