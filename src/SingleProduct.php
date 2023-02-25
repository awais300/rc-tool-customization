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
			echo '<p>' . $lead_time_message . '</p>';
			echo '</div>';
		}
	}
}
