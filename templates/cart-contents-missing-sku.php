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
			if(in_array($cart_item_key, $keys, true)){
				continue;
			}
			$_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
			$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

			if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
				$product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
		?>
				<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">

					<td class="product-thumbnail">
						<?php
						$thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);

						$thumbnail = str_ireplace('<img', '<img style="width:50px; height:50px;"', $thumbnail);

						//dd($thumbnail);

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
							echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;');
						} else {
							echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
						}

						// Meta data.
						$formatted_data = wc_get_formatted_cart_item_data($cart_item); // PHPCS: XSS ok.

						/*$style_display_none = ' style="display:none !important;" ';
						$search = array(
							'class="mkl_pc-extra-price"',
							'class="mkl-pc-edit-link--container"',
							'<img',
						);
						$replace = array(
							'class="mkl_pc-extra-price"' . $style_display_none,
							'class="mkl-pc-edit-link--container"' . $style_display_none,
							'<img style="width:25px; height:25px;"',
						);
						$formatted_data = str_ireplace($search, $replace, $formatted_data);*/

						$formatted_data = preg_replace('#<span class="mkl_pc-extra-price"?\b(?:(?R)|(?:(?!<\/?div).))*<\/span>#', '', $formatted_data);

						$formatted_data = preg_replace('#<div class="mkl-pc-edit-link--container"?\b(?:(?R)|(?:(?!<\/?div).))*<\/div>#', '', $formatted_data);
						echo $formatted_data;

						?>
					</td>

					<td class="product-price" data-title="<?php esc_attr_e('Price', 'woocommerce'); ?>">
						<?php
						//echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); // PHPCS: XSS ok.
						
						echo '-';
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
						//echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // PHPCS: XSS ok.
						
						echo '-';
						?>
					</td>
				</tr>
		<?php
			}
		}
		?>
	</tbody>
</table>