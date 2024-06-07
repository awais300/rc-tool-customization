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
	 * @var string SESS_RC_SPECIAL_PRODUCT
	 **/
	public const SESS_RC_SPECIAL_PRODUCT = 'sess_rc_special_product';

	/**
	 * Array of layer names.
	 * @var array
	 */
	protected $layer_names = array(
		'Custom Size',
		'Custom Notes',
		'Custom Shelf',
	);

	/**
	 * Array of allowed shelf options.
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
		add_action('wp_head', array($this, 'reset_session_if_cart_empty'), 1);
		add_action('wp_head', array($this, 'add_script'));

		add_action('woocommerce_add_to_cart', array($this, 'set_session_after_add_to_cart'), 1, 6);
		add_filter('woocommerce_add_to_cart_redirect', array($this, 'custom_add_to_cart_redirect'));

		add_filter('template_redirect', array($this, 'hide_unwanted_notice'));
		add_filter('template_redirect', array($this, 'redirect_to_product'));

		add_action('woocommerce_thankyou', array($this, 'reset_special_option_product_session'), 10, 1);
		add_action('gform_after_submission_' . Cart::FORM_ID, array($this, 'reset_wc_session'), 10, 2);

		add_filter('woocommerce_cart_item_permalink', array($this, 'custom_cart_item_permalink'), 40, 3);
		add_action('wp_login', array($this, 'handle_login_action'), 10, 2);
		add_action('wp_logout', array($this, 'handle_logout_action'));

		add_filter('mkl_pc_item_meta', array($this, 'filter_mkl_pc_item_meta'), 11, 5);

		add_filter('wc_add_to_cart_message', array($this, 'change_notice_text'), 10);
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
		if((Helper::get_instance())->is_distributor()) {
			return $notice;
		}

		if (strpos($notice, 'has been added to your cart') !== false) {
			$notice = str_ireplace('has been added to your cart', 'has been added to your RFQ Cart.Click the cart to submit your RFQ', $notice);
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
	 * Hide notice 'Product is added to cart' if RFQ or non-RFQ notice is detected.
	 * We are doing this because we changed the way we handle "Special options". We now
	 * use special options within Product Configurator.
	 **/
	public function hide_unwanted_notice()
	{
		$notices = WC()->session->get('wc_notices', array());
		$note = $notices['success'][0]['notice'];

		if (str_contains($note, 'has been added to your cart')) {
			//$notices['success'][0]['notice'] = str_ireplace('has been added to your cart', 'has been added to your RFQ Cart. Please click the cart to submit your RFQ.', $notices['success'][0]['notice']);
		}

		$needle = 'RFQ product is detected in the cart. You must';

		$found = false;
		if (!empty($notices)) {
			foreach ($notices as $all_notices) {
				foreach ($all_notices as $notice_array) {
					if (isset($notice_array['notice'])) {
						$haystack = $notice_array['notice'];
						if (stripos($haystack, $needle) !== false) {
							$found = true;
							break 2;
						}
					}
				}
			}
		}

		if ($found) {
?>
			<style>
				div.woocommerce-notices-wrapper .woocommerce-message {
					display: none !important;
				}
			</style>
		<?php
		}
	}

	/**
	 * Add JavaScript object for use in frontend.
	 *
	 * @return void
	 */
	public function add_script()
	{
		//WC()->session->set(self::SESS_RC_SPECIAL_PRODUCT, null);
		$sess_special_product = WC()->session->get(self::SESS_RC_SPECIAL_PRODUCT);
		$js_val = '';
		if (!empty($sess_special_product)) {
			$js_val = $sess_special_product;
		}

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
				sess_special_product: '<?php echo $js_val; ?>',
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
	public function set_session_after_add_to_cart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data)
	{
		$helper = Helper::get_instance();
		if (!$helper->is_distributor()) {
			return;
		}

		$sess_special_product = WC()->session->get(self::SESS_RC_SPECIAL_PRODUCT);
		if (empty($sess_special_product)) {
			if ($this->is_special_option_product_in_cart()) {
				WC()->session->set(self::SESS_RC_SPECIAL_PRODUCT, 'yes');
			} else {
				WC()->session->set(self::SESS_RC_SPECIAL_PRODUCT, 'no');
			}
		}

		// Further actions based on special product status.
		if (!empty($sess_special_product)) {
			$is_current_special_product = $this->is_current_product_has_special_option($cart_item_data);

			if ($is_current_special_product === true && $sess_special_product === 'yes') {
				// Do nothing. Execute normally.
			} else if ($is_current_special_product === false && $sess_special_product === 'no') {
				// Do nothing. Execute normally.
			} else if ($is_current_special_product === true && $sess_special_product === 'no') {
				// Non-RFQ product detected in the cart.
				WC()->cart->remove_cart_item($cart_item_key);
				$checkout_url = '<a href="' . wc_get_checkout_url() . '">checkout</a>';
				wc_clear_notices();
				wc_add_notice('A non-RFQ product is detected in the cart. You must ' . $checkout_url . ' before adding an RFQ item to the cart.', 'notice');
			} else if ($is_current_special_product === false && $sess_special_product === 'yes') {
				// RFQ product detected in the cart.
				WC()->cart->remove_cart_item($cart_item_key);
				$cart_url = '<a href="' . wc_get_cart_url() . '">submit</a>';
				wc_clear_notices();
				wc_add_notice('An RFQ product is detected in the cart. You must ' . $cart_url . ' that request before adding this new item to the cart.', 'notice');
			}
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
	 * Resets session after checkout for special option product.
	 *
	 * @param int $order_id
	 * @return void
	 */
	public function reset_special_option_product_session($order_id)
	{
		$helper = Helper::get_instance();
		if (!$helper->is_distributor()) {
			return;
		}

		if (!$order_id) {
			return;
		}

		$order = wc_get_order($order_id);
		$val = $order->get_meta('_thankyou_action_done');

		// Allow code execution only once.
		if (!$val) {
			// Reset session.
			WC()->session->set(self::SESS_RC_SPECIAL_PRODUCT, null);

			// Flag the action as done (to avoid repetitions).
			$order->update_meta_data('_thankyou_action_done', true);
			$order->save();
		}
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
		// Reset session.
		WC()->session->set(self::SESS_RC_SPECIAL_PRODUCT, null);

		// Clear cart.
		WC()->cart->empty_cart();
	}

	/**
	 * Resets session if cart is empty.
	 *
	 * @return void
	 */
	public function reset_session_if_cart_empty()
	{
		if ((Helper::get_instance())->is_cart_empty()) {
			// Reset session.
			WC()->session->set(self::SESS_RC_SPECIAL_PRODUCT, null);
		}
	}
}
