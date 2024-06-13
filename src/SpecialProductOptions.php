<?php

namespace EWA\RCTool;

defined('ABSPATH') || exit;

/**
 * Class SpecialProductOptions
 * Handles special product options for the RC Tool plugin.
 *
 * @package EWA\RCTool
 */
class SpecialProductOptions
{
	/**
	 * WC session variable for storing special product status.
	 * 
	 * @var string SESS_RC_SPECIAL_PRODUCT
	 **/
	public const SESS_RC_SPECIAL_PRODUCT = 'sess_rc_special_product';

	/**
	 * Product item whether it has special option.
	 * Opting special option makes that product RFQ.
	 * 
	 * @var string IS_SPECIAL_PRODUCT_RFQ
	 **/
	public const IS_SPECIAL_PRODUCT_RFQ = '_is_special_product_rfq';

	/**
	 * Array of layer names.
	 * 
	 * @var array
	 */
	protected $layer_names = array(
		'Custom Size',
		'Custom Notes',
		'Custom Shelf',
	);

	/**
	 * Array of allowed shelf options.
	 * 
	 * @var array
	 */
	protected $allowed_shelf = array(
		'Welded-in Shelf',
		'Adjustable Shelf',
	);

	/**
	 * SpecialProductOptionsOLD constructor.
	 * Initializes actions upon class instantiation.
	 */
	public function __construct()
	{
		add_action('wp_head', array($this, 'add_script'));

		add_action('woocommerce_add_to_cart', array($this, 'set_special_rfq_after_add_to_cart'), 1, 6);
		add_filter('woocommerce_add_to_cart_redirect', array($this, 'custom_add_to_cart_redirect'));
		add_filter('woocommerce_cart_item_permalink', array($this, 'custom_cart_item_permalink'), 40, 3);

		add_filter('template_redirect', array($this, 'redirect_to_product'));
		add_action('gform_after_submission_' . Cart::FORM_ID, array($this, 'reset_wc_session'), 10, 2);

		add_action('wp_login', array($this, 'handle_login_action'), 10, 2);
		add_action('wp_logout', array($this, 'handle_logout_action'));

		add_filter('mkl_pc_item_meta', array($this, 'filter_mkl_pc_item_meta'), 11, 5);
		add_filter('wc_add_to_cart_message', array($this, 'change_notice_text'), 10);

		add_action('woocommerce_cart_contents', array($this, 'show_hide_woocommerce_cart_contents'), 9);
		add_filter('gettext', array($this, 'rename_cart_text'), 20, 3);
	}

	/**
	 * Update text 'Cart' to 'RFQ' on cart page.
	 * 
	 * @param  string $translated_text
	 * @param  string $untranslated_text
	 * @param  string $domain
	 * @return string
	 */
	public function rename_cart_text($translated_text, $untranslated_text, $domain)
	{
		if (is_cart() && (Helper::get_instance())->has_rfq_in_cart()) {
			$translated_text = str_ireplace('cart', 'RFQ', $translated_text);
		}

		return $translated_text;
	}

	/**
	 * Show/Hide cart sections.
	 **/
	public function show_hide_woocommerce_cart_contents()
	{
		if ((Helper::get_instance())->has_rfq_in_cart()) {
?>
			<style>
				div.cart-collaterals {
					display: none !important;
				}
			</style>
		<?php
		} else {
			remove_filter('the_title', array(Cart::get_instance(), 'rename_cart_page_title'), 10);
		?>
			<style>
				div.rc-request {
					display: none !important;
				}

				div.cart-collaterals {
					display: block !important;
				}
			</style>
		<?php
		}
	}

	/**
	 * Change the add to cart message.
	 * 
	 * @param string
	 *
	 * @return string
	 **/
	public function change_notice_text($notice)
	{
		if (!(Helper::get_instance())->has_rfq_in_cart()) {
			return $notice;
		}

		if (strpos($notice, 'has been added to your cart') !== false) {
			$notice = str_ireplace('has been added to your cart', 'has been added to your RFQ Cart. Click the cart to submit your RFQ', $notice);
		}

		return $notice;
	}

	/**
	 * Unset or remove product configurator special options if empty or not selected.
	 *
	 * @param $meta
	 * @param $layer
	 * @param $product
	 * @param $item_key
	 * @param $context
	 *
	 * @return array
	 * 
	 * */
	public function filter_mkl_pc_item_meta($meta, $layer, $product, $item_key, $context)
	{

		$layer_name = $layer->get_layer('name');
		if (in_array($layer_name, $this->layer_names)) {

			if (isset($layer->field_value)) {
				$text_val = $layer->field_value;
				if (empty($text_val)) {
					return array();
				}
			} else {
				$shelf = $layer->get_choice('name');
				if (!in_array($shelf, $this->allowed_shelf)) {
					return array();
				}
			}
		}

		return $meta;
	}

	/**
	 * Perform actions after user logout.
	 *
	 */
	public function handle_logout_action()
	{
		$this->reset_wc_session(null, null);
	}

	/**
	 * Handle custom actions after user login.
	 * Here we are only reset the WC special option session and clear the cart.
	 *
	 * @param string $user_login The user's username.
	 * @param WP_User $user The logged-in user object.
	 */
	public function handle_login_action($user_login, $user)
	{
		$this->reset_wc_session(null, null);
	}

	/**
	 * Remvoe permalink of proudct on cart page.
	 * 
	 * @return string
	 **/
	public function custom_cart_item_permalink($permalink, $cart_item, $cart_item_key)
	{

		return '';
	}

	/**
	 * Redirect wthout query string if specicic query string is found.
	 *
	 * @return void
	 **/
	public function redirect_to_product()
	{
		if (isset($_GET['load_config_from_cart']) && !empty($_GET['load_config_from_cart'])) {
			$actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

			$actual_link = parse_url($actual_link);
			$redirect_link = $actual_link['scheme'] . "://" . $actual_link['host'] . $actual_link['path'];
			wp_redirect($redirect_link);
			exit;
		}
	}

	/**
	 * Redirect to same product page after add to cart is clicked.
	 *
	 * @return void
	 **/
	public function custom_add_to_cart_redirect()
	{

		$product_id = (int) $_REQUEST['add-to-cart'];
		$product_permalink = get_permalink($product_id);
		return $product_permalink;
	}

	/**
	 * Add JavaScript object for use in frontend.
	 *
	 * @return void
	 */
	public function add_script()
	{
		$is_guest_user = 1;
		if ((Helper::get_instance())->is_distributor()) {
			$is_guest_user = 0;
		}
		?>
		<?php if (is_product()) : ?>
			<style>
				.pc-total-price--container,
				p.price {
					display: none !important;
				}
			</style>
		<?php endif; ?>

		<script>
			const RCT_OBJ = {
				form_id: '<?php echo Cart::FORM_ID; ?>',
				sess_special_product: '',
				is_guest_user: <?php echo $is_guest_user; ?>
			};
		</script>

<?php
	}

	/**
	 * Sets session after a product is added to cart and determines if it's a special product.
	 *
	 * @param string $cart_item_key
	 * @param int $product_id
	 * @param int $quantity
	 * @param int $variation_id
	 * @param array $variation
	 * @param array $cart_item_data
	 * @return void
	 */
	public function set_special_rfq_after_add_to_cart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data)
	{
		$helper = Helper::get_instance();
		if (!$helper->is_distributor()) {
			return;
		}

		if ($this->is_current_product_has_special_option($cart_item_data)) {
			$cart_content = WC()->cart->cart_contents;
			$cart_content[$cart_item_key][self::IS_SPECIAL_PRODUCT_RFQ] = 'rfq_yes';
			WC()->cart->set_cart_contents($cart_content);
		}
	}

	/**
	 * Checks if any special option product is in the cart.
	 *
	 * @return bool
	 */
	public function is_special_option_product_in_cart()
	{
		foreach (WC()->cart->get_cart() as $cart_item) {
			$configurator_data = $cart_item['configurator_data'];
			foreach ($configurator_data as $layer) {
				$layer_name = $layer->get_layer('name');

				if (in_array($layer_name, $this->layer_names)) {
					$text_val = $layer->field_value;
					$shelf = $layer->get_choice('name');

					if (in_array($shelf, $this->allowed_shelf)) {
						return true;
					}

					if (!empty($text_val)) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Checks if the current product has a special option.
	 *
	 * @param array $cart_item
	 * @return bool
	 */
	public function is_current_product_has_special_option($cart_item)
	{
		$configurator_data = $cart_item['configurator_data'];
		foreach ($configurator_data as $layer) {
			$layer_name = $layer->get_layer('name');
			if (in_array($layer_name, $this->layer_names)) {
				$text_val = $layer->field_value;
				$shelf = $layer->get_choice('name');

				if (in_array($shelf, $this->allowed_shelf)) {
					return true;
				}

				if (!empty($text_val)) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Resets session and clear the WC cart.
	 * 
	 * @param object $entry
	 * @param object $form
	 * @return void
	 */
	public function reset_wc_session($entry, $form)
	{
		// Clear cart.
		WC()->cart->empty_cart();
	}
}
