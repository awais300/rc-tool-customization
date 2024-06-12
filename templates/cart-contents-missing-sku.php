<?php

use EWA\RCTool\Helper;
use EWA\RCTool\Pricing;
?>
<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
	<thead>
		<tr>
			<th class="product-thumbnail"><span class="screen-reader-text"><?php esc_html_e('Thumbnail image', 'woocommerce'); ?></span></th>
			<th class="product-name"><?php esc_html_e('Product', 'woocommerce'); ?></th>
			<th class="product-price"><?php esc_html_e('Price', 'woocommerce'); ?></th>
			<th class="product-quantity"><?php esc_html_e('Quantity', 'woocommerce'); ?></th>
			<th class="product-subtotal"><?php esc_html_e('Subtotal', 'woocommerce'); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php
		foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
			$_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
			$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
			/**
			 * Filter the product name.
			 *
			 * @since 2.1.0
			 * @param string $product_name Name of the product in the cart.
			 * @param array $cart_item The product in the cart.
			 * @param string $cart_item_key Key for the product in the cart.
			 */
			$product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);

			if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
				$product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
		?>
				<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">


					<td class="product-thumbnail">
						<?php
						$thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);

						if (!$product_permalink) {
							echo $thumbnail; // PHPCS: XSS ok.
						} else {
							printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail); // PHPCS: XSS ok.
						}
						?>
					</td>

					<td class="product-name" data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
						<?php
						if (!$product_permalink) {
							echo wp_kses_post($product_name . '&nbsp;');
						} else {
							/**
							 * This filter is documented above.
							 *
							 * @since 2.1.0
							 */
							echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
						}

						do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);

						// Meta data.
						echo wc_get_formatted_cart_item_data($cart_item); // PHPCS: XSS ok.

						// Backorder notification.
						if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
							echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woocommerce') . '</p>', $product_id));
						}
						?>
					</td>

					<td class="product-price" data-title="<?php esc_attr_e('Price', 'woocommerce'); ?>">
						<?php
						if ((Helper::get_instance()->cart_has_rfq($cart_item))) {
							echo "-";
						} else {
							echo $cart_item[Pricing::PRODUCT_NEW_PRICE];
						}
						?>
					</td>

					<td class="product-quantity" data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
						<?php
						$product_quantity =  $cart_item['quantity'];
						echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item); // PHPCS: XSS ok.
						?>
					</td>

					<td class="product-subtotal" data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>">
						<?php
						if ((Helper::get_instance()->cart_has_rfq($cart_item))) {
							echo "-";
						} else {
							echo $cart_item['line_subtotal'];
						}
						?>
					</td>
				</tr>
		<?php
			}
		}
		?>
	</tbody>
</table>
</form>