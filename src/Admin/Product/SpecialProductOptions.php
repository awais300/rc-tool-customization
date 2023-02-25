<?php

namespace EWA\RCTool\Admin\Product;

defined('ABSPATH') || exit;

/**
 * Class SpecialProductOptionsroductOptions
 * @package EWA\RCTool\Admin\Product
 */

class SpecialProductOptions
{
	/**
	 * Product lead time field.
	 * @var SPECIAL_PRODUCT_OPTION_FIELD
	 **/
	public const SPECIAL_PRODUCT_OPTION_FIELD = 'rc_special_product_option';

	/**
	 * Construct the plugin.
	 */
	public function __construct()
	{
		add_action('woocommerce_product_data_panels', array($this, 'rctool_custom_tab_data'));
		add_action('woocommerce_process_product_meta', array($this, 'custom_tab_save_data'), 10, 1);
	}

	/**
	 * Display tab section.
	 **/
	public function rctool_custom_tab_data()
	{ ?>
		<div id="rctool_custom_tab_data_special_option" class="panel woocommerce_options_panel rctool_custom_tab_data_special_option rctool_custom_tab_section">
			<div class="options_group rct-custom-field special-option">
				<h3><?php echo __('Allow Special Product Options', 'rct-customization'); ?></h3>
				<i><?php echo __('Check below box if you want to allow special options e.g. Welded-in Shelf, Adjustable Shelf, Custom Size and Custom Notes', 'rct-customization'); ?></i>

				<?php
				$value = esc_textarea(get_post_meta(get_the_ID(), self::SPECIAL_PRODUCT_OPTION_FIELD, true));
				woocommerce_wp_checkbox(
					array(
						'id'          => self::SPECIAL_PRODUCT_OPTION_FIELD,
						'value'       => $value,
						'label'       => __('Is Special Product?: ', 'rct-customization'),
						'placeholder' => '',
						'description' => __('Check this box if you want to allow special options e.g. Welded-in Shelf, Adjustable Shelf, Custom Size and Custom Notes', 'rct-customization'),
						'desc_tip'    => true,
					)
				);
				?>
				<hr />
			</div>
		</div>

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

		$field = sanitize_text_field($_POST[self::SPECIAL_PRODUCT_OPTION_FIELD]);
		$product->update_meta_data(self::SPECIAL_PRODUCT_OPTION_FIELD, trim($field));
		$product->save();
	}
}
