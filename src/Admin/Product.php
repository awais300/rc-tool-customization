<?php

namespace EWA\RCTool\Admin;

defined('ABSPATH') || exit;

/**
 * Class Product
 * @package EWA\RCTool\Admin
 */

class Product
{
	/**
	 * Product lead time field.
	 * @var PRODUCT_LEAD_TIME_FIELD
	 **/
	public const PRODUCT_LEAD_TIME_FIELD = 'rc_product_lead_time';

	/**
	 * Construct the plugin.
	 */
	public function __construct()
	{
		add_filter('woocommerce_product_data_tabs', array($this, 'add_custom_product_tab'), 10, 1);
		add_action('woocommerce_product_data_panels', array($this, 'rctool_custom_tab_data'));
		add_action('woocommerce_process_product_meta', array($this, 'custom_tab_save_data'), 10, 1);
	}

	/**
	 * Register custom tab for proudct edit page.
	 * @param array $default_tabs
	 * @return array
	 */
	public function add_custom_product_tab($default_tabs)
	{
		$default_tabs['custom_tab'] = array(
			'label'   =>  __('Product Lead Time', 'rct-customization'),
			'target'  =>  'rctool_custom_tab_data',
			'priority' => 60,
			'class'   => array()
		);
		return $default_tabs;
	}

	/**
	 * Display tab section.
	 **/
	public function rctool_custom_tab_data()
	{ ?>
		<div id="rctool_custom_tab_data" class="panel woocommerce_options_panel">;
			<div class="options_group rct-custom-field">';

				<?php
				$value = esc_textarea(get_post_meta(get_the_ID(), self::PRODUCT_LEAD_TIME_FIELD, true));
				woocommerce_wp_textarea_input(
					array(
						'id'          => self::PRODUCT_LEAD_TIME_FIELD,
						'value'       => $value,
						'label'       => __('Product Lead Time Message: ', 'rct-customization'),
						'placeholder' => '',
						'description' => __('The message here will override the global message.', 'rct-customization'),
						'desc_tip'    => true,
					)
				);
				?>

			</div>;
		</div>;
<?php
	}

	/**
	 * Save custom tab fields.
	 * @param  int $post_id
	 * @return void
	 */
	public function custom_tab_save_data($post_id)
	{
		$product = wc_get_product($post_id);

		$field = sanitize_textarea_field($_POST[self::PRODUCT_LEAD_TIME_FIELD]);
		$product->update_meta_data(self::PRODUCT_LEAD_TIME_FIELD, trim($field));
		$product->save();
	}
}
