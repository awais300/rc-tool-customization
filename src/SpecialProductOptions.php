<?php

namespace EWA\RCTool;

use EWA\RCTool\Admin\Product\SpecialProductOptions as SpecialProductOptionsBackend;

defined('ABSPATH') || exit;

/**
 * Class SpecialProductOptions
 * @package EWA\RCTool
 */

class SpecialProductOptions
{
	/**
	 * The Welded-in Shelf checkbox field.
	 * @var WELDED_FIELD
	 **/
	public const WELDED_FIELD = 'rc_special_option_welded';
	public const WELDED_LABEL = 'Welded-in Shelf';

	/**
	 * The Adjustable Shelf checkbox field.
	 * @var ADJUSTABLE_FIELD
	 **/
	public const ADJUSTABLE_FIELD = 'rc_special_option_adjustable';
	public const ADJUSTABLE_LABEL = 'Adjustable Shelf';

	/**
	 * The Custom Size input field.
	 * @var SIZE_FIELD
	 **/
	public const SIZE_FIELD = 'rc_special_option_size';
	public const SIZE_LABEL = 'Custom Size';

	/**
	 * The Custom Notes textarea field.
	 * @var NOTES_FIELD
	 **/
	public const NOTES_FIELD = 'rc_special_option_notes';
	public const NOTES_Label = 'Notes';

	/**
	 * WC session variable.
	 * @var SESS_RC_SPECIAL_PRODUCT
	 **/
	public const SESS_RC_SPECIAL_PRODUCT = 'sess_rc_special_product';

	/**
	 * Construct the plugin.
	 */
	public function __construct()
	{

		add_action('wp_head', array($this, 'reset_session_if_cart_empty'), 1);
		add_action('wp_head', array($this, 'add_script'));

		add_action('woocommerce_before_add_to_cart_button', array($this, 'output_special_options_fields'), 10);
		add_filter('woocommerce_add_cart_item_data', array($this, 'add_special_options_to_cart_item'), 10, 3);
		add_filter('woocommerce_get_item_data', array($this, 'output_special_option_cart_item_data'), 10, 2);
		//add_action('woocommerce_checkout_create_order_line_item', array($this, 'add_special_options_data_to_order'), 10, 4);
		add_action('woocommerce_add_to_cart', array($this, 'set_session_after_add_to_cart'), 1, 6);
		add_filter('woocommerce_add_to_cart_validation', array($this, 'validate_special_product'), 1, 5);

		add_action('woocommerce_thankyou', array($this, 'reset_special_option_product_session'), 10, 1);
		add_action('gform_after_submission_' . Cart::FORM_ID, array($this, 'reset_session_after_form_submit'), 10, 2);
	}

	/**
	 * Add JS Object.
	 *
	 * @return void
	 */
	public function add_script()
	{
		$sess_special_product = WC()->session->get(self::SESS_RC_SPECIAL_PRODUCT);
		$js_val = '';
		if (!empty($sess_special_product)) {
			$js_val = $sess_special_product;
		}
?>

		<script>
			const RCT_OBJ = {
				form_id: '<?php echo Cart::FORM_ID; ?>',
				sess_special_product: '<?php echo $js_val; ?>'
			};
		</script>

	<?php
	}

	/**
	 * Output special option fields.
	 */
	public function output_special_options_fields()
	{
		//var_dump(WC()->session->get(self::SESS_RC_SPECIAL_PRODUCT));

		$helper = Helper::get_instance();
		$helper->is_special_option_product_in_cart();
		global $product;

		if (!is_product() || empty($product)) {
			return;
		}

		if (empty($product->get_meta(SpecialProductOptionsBackend::SPECIAL_PRODUCT_OPTION_FIELD))) {
			return;
		}
	?>
		<div class="special-product-options">
			<div class="rc-field <?php echo self::WELDED_FIELD ?>">
				<label><?php _e(self::WELDED_LABEL . ':', 'rct-customization'); ?></label>
				<input type="checkbox" name="<?php echo self::WELDED_FIELD; ?>" value="Yes" <?php checked($_POST[self::WELDED_FIELD], 'Yes', true); ?>>
			</div>

			<div class="rc-field <?php echo self::ADJUSTABLE_FIELD ?>">
				<label><?php _e(self::ADJUSTABLE_LABEL . ':', 'rct-customization'); ?></label>
				<input type="checkbox" name="<?php echo self::ADJUSTABLE_FIELD; ?>" value="Yes" <?php checked($_POST[self::ADJUSTABLE_FIELD], 'Yes', true); ?>>
			</div>

			<div class="rc-field <?php echo self::SIZE_FIELD ?>">
				<label><?php _e(self::SIZE_LABEL . ':', 'rct-customization'); ?></label>
				<input type="text" name="<?php echo self::SIZE_FIELD ?>" value="<?php echo esc_html($_POST[self::SIZE_FIELD]) ?? '' ?>">
			</div>

			<div class="rc-field <?php echo self::NOTES_FIELD ?>">
				<label><?php _e(self::NOTES_Label . ':', 'rct-customization'); ?></label>
				<textarea name="<?php echo self::NOTES_FIELD ?>"><?php echo esc_html($_POST[self::NOTES_FIELD]) ?? '' ?></textarea>
			</div>
		</div>
<?php
	}

	/**
	 * Add special options data to cart item.
	 *
	 * @param array $cart_item_data
	 * @param int $product_id
	 * @param int $variation_id
	 *
	 * @return array
	 */
	public function add_special_options_to_cart_item($cart_item_data, $product_id, $variation_id)
	{

		if (isset($_POST[self::WELDED_FIELD]) && !empty($_POST[self::WELDED_FIELD])) {
			$cart_item_data[self::WELDED_FIELD] = sanitize_text_field($_POST[self::WELDED_FIELD]);
		}

		if (isset($_POST[self::ADJUSTABLE_FIELD]) && !empty($_POST[self::ADJUSTABLE_FIELD])) {
			$cart_item_data[self::ADJUSTABLE_FIELD] = sanitize_text_field($_POST[self::ADJUSTABLE_FIELD]);
		}

		if (isset($_POST[self::SIZE_FIELD]) && !empty($_POST[self::SIZE_FIELD])) {
			$cart_item_data[self::SIZE_FIELD] = sanitize_text_field($_POST[self::SIZE_FIELD]);
		}

		if (isset($_POST[self::NOTES_FIELD]) && !empty($_POST[self::NOTES_FIELD])) {
			$cart_item_data[self::NOTES_FIELD] = sanitize_text_field($_POST[self::NOTES_FIELD]);
		}

		return $cart_item_data;
	}

	/**
	 * Display special options data in the cart.
	 *
	 * @param array $item_data
	 * @param array $cart_item
	 *
	 * @return array
	 */
	public function output_special_option_cart_item_data($item_data, $cart_item)
	{
		if (isset($cart_item[self::WELDED_FIELD]) && !empty($cart_item[self::WELDED_FIELD])) {
			$item_data[] = array(
				'key'     => self::WELDED_LABEL,
				'value'   => wc_clean($cart_item[self::WELDED_FIELD]),
			);
		}

		if (isset($cart_item[self::ADJUSTABLE_FIELD]) && !empty($cart_item[self::ADJUSTABLE_FIELD])) {
			$item_data[] = array(
				'key'     => self::ADJUSTABLE_LABEL,
				'value'   => wc_clean($cart_item[self::ADJUSTABLE_FIELD]),
			);
		}

		if (isset($cart_item[self::SIZE_FIELD]) && !empty($cart_item[self::SIZE_FIELD])) {
			$item_data[] = array(
				'key'     => self::SIZE_LABEL,
				'value'   => wc_clean($cart_item[self::SIZE_FIELD]),
			);
		}

		if (isset($cart_item[self::NOTES_FIELD]) && !empty($cart_item[self::NOTES_FIELD])) {
			$item_data[] = array(
				'key'     => self::NOTES_Label,
				'value'   => wc_clean($cart_item[self::NOTES_FIELD]),
			);
		}

		return $item_data;
	}

	/**
	 * Add special options data to order.
	 *
	 * @param WC_Order_Item_Product $item
	 * @param string $cart_item_key
	 * @param array $values
	 * @param WC_Order $order
	 */
	public function add_special_options_data_to_order($item, $cart_item_key, $values, $order)
	{
		if (isset($values[self::WELDED_FIELD]) && !empty($values[self::WELDED_FIELD])) {
			$item->add_meta_data(self::WELDED_LABEL, $values[self::WELDED_FIELD]);
		}

		if (isset($values[self::ADJUSTABLE_FIELD]) && !empty($values[self::ADJUSTABLE_FIELD])) {
			$item->add_meta_data(self::ADJUSTABLE_LABEL, $values[self::ADJUSTABLE_FIELD]);
		}

		if (isset($values[self::SIZE_FIELD]) && !empty($values[self::SIZE_FIELD])) {
			$item->add_meta_data(self::SIZE_LABEL, $values[self::SIZE_FIELD]);
		}

		if (isset($values[self::NOTES_FIELD]) && !empty($values[self::NOTES_FIELD])) {
			$item->add_meta_data(self::NOTES_Label, $values[self::NOTES_FIELD]);
		}
	}

	/**
	 * Set session after product is added to cart.
	 * and set value whether a special product is in cart or not.
	 *
	 * @param WC_Order_Item_Product $item
	 * @param string $cart_item_key
	 * @param int $product_id
	 * @param int $quantity
	 * @param int $variation_id
	 * @param array $variation
	 * @param array $cart_item_data
	 */
	public function set_session_after_add_to_cart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data)
	{
		$helper = Helper::get_instance();
		if (!$helper->is_distributor()) {
			return;
		}

		$sess_special_product = WC()->session->get(self::SESS_RC_SPECIAL_PRODUCT);
		if (empty($sess_special_product)) {
			if ($helper->is_special_option_product_in_cart()) {
				WC()->session->set(self::SESS_RC_SPECIAL_PRODUCT, 'yes');
			} else {
				WC()->session->set(self::SESS_RC_SPECIAL_PRODUCT, 'no');
			}
		}
	}

	/**
	 * Validate and give appropriate error notice based
	 * on the product in the cart. e.g. RFQ or non-RFQ
	 *
	 * @param bool $passed
	 */
	public function validate_special_product($passed)
	{
		$sess_special_product = WC()->session->get(self::SESS_RC_SPECIAL_PRODUCT);
		$helper = Helper::get_instance();

		if (!$helper->is_distributor()) {
			return $passed;
		}

		if (!empty($sess_special_product)) {
			$cart_url = '<a href="' . wc_get_cart_url() . '">submit</a>';
			if ($sess_special_product === 'yes' && $helper->is_special_option_product($_POST) === false) {
				wc_add_notice('An RFQ product is detected in the cart. You must ' . $cart_url . ' that request before adding this new item to the cart.', 'notice');
				$passed = false;
			}

			if ($sess_special_product === 'no' && $helper->is_special_option_product($_POST) === true) {
				$checkout_url = '<a href="' . wc_get_checkout_url() . '">checkout</a>';
				wc_add_notice('A non-RFQ product is detected in the cart. You must ' . $checkout_url . ' before adding an RFQ item to the cart.', 'notice');
				$passed = false;
			}
		}


		return $passed;
	}


	/**
	 * Reset session after checkout for special option product.
	 * @param int $order_id
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
	 * Reset session after checkout for special option product.
	 * @param object $entry
	 * @param object $form
	 */
	public function reset_session_after_form_submit($entry, $form)
	{
		// Reset session.
		WC()->session->set(self::SESS_RC_SPECIAL_PRODUCT, null);

		// Clear cart.
		WC()->cart->empty_cart();
	}

	/**
	 * Reset session if cart is empty.
	 */
	public function reset_session_if_cart_empty()
	{
		if ((Helper::get_instance())->is_cart_empty()) {
			// Reset session.
			WC()->session->set(self::SESS_RC_SPECIAL_PRODUCT, null);
		}
	}
}
