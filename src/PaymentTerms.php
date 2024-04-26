<?php

namespace EWA\RCTool;

use EWA\RCTool\Admin\Acf\UserFields;

defined('ABSPATH') || exit;

/**
 * Class PaymentTerms
 * Handles customization of payment terms and gateway descriptions.
 *
 * @package EWA\RCTool
 */
class PaymentTerms
{
    /**
     * Construct the PaymentTerms class.
     */
    public function __construct()
    {
        add_filter('woocommerce_gateway_title', array($this, 'change_payment_term'), 25, 2);
        add_filter('woocommerce_gateway_description', array($this, 'change_payment_gateway_description'), 25, 2);
    }

    /**
     * Change payment gateway title based on user's payment term.
     *
     * @param string $title The original gateway title.
     * @param string $gateway_id The gateway ID.
     * @return string The modified gateway title.
     */
    public function change_payment_term($title, $gateway_id)
    {
        $new_title = '';

        if (is_admin()) {
            if (
                isset($_GET['post']) &&
                !empty($_GET['post']) &&
                get_post_type($_GET['post']) === 'shop_order'
            ) {
                $order = wc_get_order($_GET['post']);
                $user_id = $order->get_user_id();
                $new_title = get_user_meta($user_id, UserFields::META_USER_PAYMENT_TERM_FIELD, true);
            }
        } else {
            $user_id = get_current_user_id();
            if ($user_id) {
                $new_title = get_user_meta($user_id, UserFields::META_USER_PAYMENT_TERM_FIELD, true);
            }
        }

        if (!empty($new_title)) {
            if ('cod' === $gateway_id) {
                $title = $new_title;
            }
        }

        return $title;
    }

    /**
     * Change payment gateway description for Cash on Delivery.
     *
     * @param string $description The original gateway description.
     * @param string $gateway_id The gateway ID.
     * @return string The modified gateway description.
     */
    public function change_payment_gateway_description($description, $gateway_id)
    {
        if ('cod' === $gateway_id) {
            $description = '';
        }

        return $description;
    }
}
