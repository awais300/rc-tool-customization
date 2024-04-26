<?php

namespace EWA\RCTool;

use EWA\RCTool\Admin\Order;

defined('ABSPATH') || exit;

/**
 * Class MyAccountOrder
 * Handles customization of the My Account Orders page.
 *
 * @package EWA\RCTool
 */
class MyAccountOrder
{
    /**
     * Construct the MyAccountOrder class.
     */
    public function __construct()
    {
        add_filter('woocommerce_account_orders_columns', array($this, 'add_account_orders_column'), 10, 1);
        add_action('woocommerce_my_account_my_orders_column_' . Order::ORDER_EXPECTED_SHIPPING_DATE_FIELD, array($this, 'display_column_details'));
    }

    /**
     * Add a new column header.
     *
     * @param array $columns The existing columns.
     * @return array The modified columns.
     */
    public function add_account_orders_column($columns)
    {
        $columns[Order::ORDER_EXPECTED_SHIPPING_DATE_FIELD] = __('Expected Shipping Date', 'rct-customization');
        return $columns;
    }

    /**
     * Display the details for the custom column.
     *
     * @param \WC_Order $order The order object.
     * @return void
     */
    public function display_column_details($order)
    {
        if ($value = $order->get_meta(Order::ORDER_EXPECTED_SHIPPING_DATE_FIELD)) {
            echo esc_html($value);
        }
    }
}
