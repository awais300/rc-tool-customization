<?php

namespace EWA\RCTool;

defined('ABSPATH') || exit;

/**
 * Class Cart
 * @package EWA\RCTool
 */

class Cart
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
		$this->loader = TemplateLoader::get_instance();
		$this->cart_page_id = get_option('woocommerce_cart_page_id');

		add_action('wp_head', array($this, 'remove_sections_on_cart_page'));
		add_action('wp_head', array($this, 'add_css'));

		add_action('woocommerce_cart_collaterals', array($this, 'remove_cart_totals_section'), 9);
		add_action('template_redirect', array($this, 'disable_checkout_page'), 9);
		add_action('woocommerce_after_cart', array($this, 'add_request_form'), 9);

		add_action('gform_pre_submission_' . self::FORM_ID, array($this, 'before_form_submit'));
		add_action('gform_after_submission_' . self::FORM_ID, array($this, 'clear_cart_after_form_submit'), 10, 2);


		add_filter('woocommerce_coupons_enabled', array($this, 'hide_coupon_field_on_cart_page'));
		add_filter('woocommerce_cart_item_price', array($this, 'update_woocommerce_cart_item_price'), 10, 3);
		add_filter('woocommerce_cart_item_subtotal', array($this, 'update_woocommerce_cart_item_subtotal'), 10, 3);
		add_filter('gettext', array($this, 'rename_cart_text'), 10, 3);
		add_filter('the_title', array($this, 'rename_cart_page_title'), 10, 2);
	}

	/**
	 * Disable checkout page.
	 * @return void
	 */
	public function disable_checkout_page()
	{
		if (is_checkout()) {
			wp_redirect(wc_get_page_permalink('shop'));
			exit;
		}
	}

	/**
	 * Add a request form.
	 * @return void
	 */
	public function add_request_form()
	{
		if (is_cart() && WC()->cart->get_cart_contents_count() != 0) {
			echo '<div class="rc-request"><h1><span>Submit Your Request</span></h1></div>';
			echo do_shortcode('[gravityform id="3" title="false" description="false" ajax="true"]');
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
			</style>
<?php
		}
	}

	/**
	 * Remove sections on the cart page.
	 */
	public function remove_sections_on_cart_page()
	{
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
		if (is_cart()) {
			$enabled = false;
		}
		return $enabled;
	}

	/**
	 * Replace the cart price column on cart page.
	 *
	 * @param  String $default
	 * @param  Array $cart_item
	 * @param  String $cart_item_key
	 * @return String
	 */
	public function update_woocommerce_cart_item_price($default, $cart_item, $cart_item_key)
	{
		if (is_cart()) {
			return '-';
		}
	}

	/**
	 * Replace the cart subtotal price on cart page.
	 * @param  String $default
	 * @param  Boolean $compound_bool
	 * @param  WC_Cart $cart_obj
	 * @return String
	 */
	public function update_woocommerce_cart_item_subtotal($default, $compound_bool, $cart_obj)
	{
		if (is_cart()) {
			return '-';
		}
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
		// Clear cart.
		WC()->cart->empty_cart();
	}
}
