<?php
/**
 * Plugin Name: RC Tool Customization
 * Description: Customization for RC Tool.
 * Author: Muhammad Awais / EffectWebAgency
 * Author URI: https://www.effectwebagency.com/
 * Version: 1.0.0
 */

namespace EWA\RCTool;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! defined( 'RCT_CUST_PLUGIN_FILE' ) ) {
    define( 'RCT_CUST_PLUGIN_FILE', __FILE__ );
}

require_once 'vendor/autoload.php';

Bootstrap::get_instance();