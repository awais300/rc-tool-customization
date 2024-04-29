<?php

/**
 * Plugin Name: RC Tool Customization
 * Description: Customization for RC Tool.
 * Author: Muhammad Awais / EffectWebAgency
 * Author URI: https://www.effectwebagency.com/
 * Version: 1.0.0
 */

namespace EWA\RCTool;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (!defined('RCT_CUST_PLUGIN_FILE')) {
	define('RCT_CUST_PLUGIN_FILE', __FILE__);
}

require_once 'vendor/autoload.php';

Bootstrap::get_instance();

/**
 * Activate the plugin.
 */
function rctool_on_activate()
{
	(Schema::instance())->create_table();
	SpecialProductOptions::reset_session();
}
register_activation_hook(__FILE__, __NAMESPACE__ . '\\rctool_on_activate');


/**
 * Deactivation hook.
 */
function rctool_on_deactivate()
{
	SpecialProductOptions::reset_session();
}
register_deactivation_hook(__FILE__, __NAMESPACE__ . '\\rctool_on_deactivate');
