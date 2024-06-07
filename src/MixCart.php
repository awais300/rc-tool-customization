<?php

namespace EWA\RCTool;

use EWA\RCTool\SpecialProductOptions as SpecialProductOptionsFrontend;

defined('ABSPATH') || exit;

/**
 * When Cart has RFQ and non-RFQ items in it.
 * 
 * Class MixCart
 * @package EWA\RCTool
 */

class MixCart
{
	/**
	 * The Cart page ID.
	 * @var CART_PAGE_ID
	 **/
	public $cart_page_id = null;

	/**
	 * @var $loader The template loader.
	 **/
	private $loader = null;

	/**
	 * @var FORM_ID The form ID.
	 **/
	public const FORM_ID = 3;

	/**
	 * Construct the plugin.
	 */
	public function __construct()
	{
		if(!(Helper::get_instance())->is_distributor()) {
			return;
		}

		if(WC()->session->get(SpecialProductOptionsFrontend::SESS_RC_SPECIAL_PRODUCT) === 'yes') {
			return;
		}

		$this->loader = TemplateLoader::get_instance();
		$this->cart_page_id = get_option('woocommerce_cart_page_id');

		//add_action('wp_head', array($this, 'remove_sections_on_cart_page'));
		add_action('wp_head', array($this, 'add_css'));

		add_action('woocommerce_cart_collaterals', array($this, 'remove_cart_totals_section'), 9);
		add_action('template_redirect', array($this, 'disable_checkout_page'), 9);
		add_action('woocommerce_after_cart', array($this, 'add_request_form'), 9);

		add_action('gform_pre_submission_' . self::FORM_ID, array($this, 'before_form_submit'));
		add_action('gform_after_submission_' . self::FORM_ID, array($this, 'clear_cart_after_form_submit'), 10, 2);


		add_filter('woocommerce_coupons_enabled', array($this, 'hide_coupon_field_on_cart_page'));
		add_filter('woocommerce_cart_item_price', array($this, 'update_woocommerce_cart_item_price'), 5000, 3);
		add_filter('woocommerce_cart_item_subtotal', array($this, 'update_woocommerce_cart_item_subtotal'), 5000, 3);

		//add_filter('gettext', array($this, 'rename_cart_text'), 10, 3);
		add_filter('the_title', array($this, 'rename_cart_page_title'), 10, 2);
	}

	/**
	 * Disable checkout page.
	 * @return void
	 */
	public function disable_checkout_page()
	{
		if ($this->is_mix_cart() === false) {
			return;
		}

		if (is_checkout()) {
			wp_redirect(wc_get_cart_url());
			exit;
		}
	}

	/**
	 * Add a request form.
	 * @return void
	 */
	public function add_request_form()
	{
		if ($this->is_mix_cart() === false) {
			return;
		}

		if (is_cart() && WC()->cart->get_cart_contents_count() != 0) {
			echo '<div class="rc-request"><h1><span>Submit Your Request</span></h1></div>';
			echo do_shortcode('[gravityform id="' . self::FORM_ID . '" title="false" description="false" ajax="true"]');
		}
	}

	/**
	 * Update cart contents for RFQ before sending email.
	 *
	 * @return void
	 */
	public function before_form_submit($form)
	{
		$data = array();
		$cart_contents = $this->loader->get_template(
			'cart-contents.php',
			$data,
			RCT_CUST_PLUGIN_DIR_PATH . '/templates/',
			false
		);

		$_POST['input_8'] = $cart_contents;
	}

	/**
	 * Hide price.
	 *
	 * @return void
	 */
	public function add_css()
	{
		if ($this->is_mix_cart() === false) {
			return;
		}
?>
		<style>
			/* Hide amount on mini cart icon. */
			ul#menu-main-menu li.wpmenucartli .amount {
				display: none !important;
			}
		</style>

		<?php
		if (is_cart() || is_product()) {
		?>
			<style>
				.mkl_pc-extra-price {
					display: none !important;
				}

				.mkl_pc .mkl_pc_container .extra-cost.show {
					display: none !important;
				}

				.form-cart .pc-total-price {
					display: none !important;
				}

				div.cart-collaterals {
					display: none !important;
				}
			</style>
<?php
		}
	}

	/**
	 * Remove sections on the cart page.
	 */
	public function remove_sections_on_cart_page()
	{
		if ($this->is_mix_cart() === false) {
			return;
		}

		if (is_cart()) {
			remove_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20);
			remove_action('woocommerce_cart_collaterals', 'woocommerce_cross_sell_display');
		}
	}

	/**
	 * Remove cart total section on cart page.
	 */
	public function remove_cart_totals_section()
	{
		if ($this->is_mix_cart() === false) {
			return;
		}

		remove_action('woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10);
	}

	/**
	 * Hide coupon section on cart page.
	 *
	 * @param  bool $enabled
	 * @return bool
	 */
	public function hide_coupon_field_on_cart_page($enabled)
	{
		if ($this->is_mix_cart() === false) {
			return;
		}

		if (is_cart()) {
			$enabled = false;
		}
		return $enabled;
	}

	/**
	 * Replace price with custom message.
	 * 
	 * @param $price
	 * @param $cart_item
	 * @param $cart_item_key
	 *
	 * @return string The custom message.
	 **/
	public function update_woocommerce_cart_item_price($price, $cart_item, $cart_item_key)
	{
		if ($this->is_mix_cart() === false) {
			return $price;
		}

		if (WC()->session->get(SpecialProductOptionsFrontend::SESS_RC_SPECIAL_PRODUCT) === 'yes') {
			return $price;
		}

		$item_price = $cart_item['data']->get_price();
		$custom_message = $price;

		if ($item_price == 0) {
			$custom_message = '<a class="rfq-email popmake-19761" href="javascript:void(0);">Email for RFQ</a>';
		}

		return $custom_message;
	}

	/**
	 * Replace linet item subtotal with custom message.
	 *
	 * @param $subtotal
	 * @param $cart_item
	 * @param $cart_item_key
	 *
	 * @return string The custom message.
	 **/
	public function update_woocommerce_cart_item_subtotal($subtotal, $cart_item, $cart_item_key)
	{
		if ($this->is_mix_cart() === false) {
			return $subtotal;
		}

		if (WC()->session->get(SpecialProductOptionsFrontend::SESS_RC_SPECIAL_PRODUCT) === 'yes') {
			return $subtotal;
		}

		$item_price = $cart_item['data']->get_price();
		$custom_message = $subtotal;

		if ($item_price == 0) {
			$custom_message = '-';
		}

		return $custom_message;
	}

	/**
	 * Update text 'Cart' on cart page.
	 * @param  string $translated_text
	 * @param  string $untranslated_text
	 * @param  string $domain
	 * @return string
	 */
	public function rename_cart_text($translated_text, $untranslated_text, $domain)
	{
		if ($this->is_mix_cart() === false) {
			return;
		}

		if (is_cart()) {
			$translated_text = str_ireplace('cart', 'RFQ', $translated_text);
		}

		return $translated_text;
	}

	/**
	 * Update cart page title.
	 * @param  string $title
	 * @param  int $id
	 * @return string
	 */
	public function rename_cart_page_title($title, $id = null)
	{
		if ($this->is_mix_cart() === false) {
			return;
		}

		if (is_cart() && $id == $this->cart_page_id) {
			return 'RFQ';
		}

		return $title;
	}

	/**
	 * Clear cart.
	 * @param object $entry
	 * @param object $form
	 */
	public function clear_cart_after_form_submit($entry, $form)
	{
		if ($this->is_mix_cart() === false) {
			return;
		}

		// Clear cart.
		WC()->cart->empty_cart();
	}

	/**
	 * Check if cart is mix cart and has RFQ item.
	 * 
	 * In mix cart an RFQ item is the one that didn't have price 
	 * against the SKU or whose SKU don't exist at all.
	 **/
	public function is_mix_cart()
	{
		//return true;
		$cart = WC()->cart;
		foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
			if (isset($cart_item[Pricing::IS_PRODUCT_RFQ]) && $cart_item[Pricing::IS_PRODUCT_RFQ] == 'rfq_yes') {

				return true;
			}
		}
		return false;
	}
}
