<?php

namespace EWA\RCTool;

use EWA\RCTool\Admin\Product\LeadTime;
use EWA\RCTool\Admin\Settings;

defined('ABSPATH') || exit;

/**
 * Class SingleProduct
 * @package EWA\RCTool
 */

class SingleProduct
{
	/**
	 * Construct the plugin.
	 */
	public function __construct()
	{
		add_action('woocommerce_before_add_to_cart_form', array($this, 'display_lead_time_message'));
		add_filter('woocommerce_product_single_add_to_cart_text', array($this, 'custom_add_to_cart_text'), 10, 2);
		//add_filter('woocommerce_product_add_to_cart_text', array($this, 'custom_add_to_cart_text'), 10, 2);

		//add_action('init', array($this, 'action_init'));
	}

	public function action_init() {
		if(is_admin()) {
			return;
		}

		dd($_POST);
		$data = wp_unslash($_POST['pc_configurator_data']);

		dd($data);
		$arr = json_decode($data, true);

		dd($arr);
	}

	/**
	 * Display the lead time message.
	 **/
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
	 * @param  string $add_to_cart_text
	 * @param  WC_PRoduct $product
	 * @return string     
	 */
	public function custom_add_to_cart_text($add_to_cart_text, $product)
	{
		if (is_admin()) {
			return;
		}

		//if (!(Helper::get_instance())->is_distributor()) {
			return __('Add to RFQ', 'rct-customization');
		//}

		return $add_to_cart_text;
	}
}
