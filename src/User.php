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
    public function __construct() {
        add_filter( 'login_redirect', array( $this, 'my_custom_login_redirect' ), 10, 3 );
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
    public function my_custom_login_redirect( $redirect_to, $request, $user ) {
        // Check if the user is not an admin and is logging in from the login page
        if ( ! is_wp_error( $user ) && ! user_can( $user, 'manage_options' ) && isset( $_SERVER['HTTP_REFERER'] ) && strpos( $_SERVER['HTTP_REFERER'], 'wp-login.php' ) !== false ) {
            // Redirect to the my-account page
            return home_url( '/my-account' );
        }
        // If user is an admin, or not logging in from the login page, do not change the redirection
        return $redirect_to;
    }
}
