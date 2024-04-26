<?php

namespace EWA\RCTool\Admin;

defined('ABSPATH') || exit;

/**
 * Class Order
 * @package EWA\RCTool\Admin
 */
class Order
{
    /**
     * Expected shipping date field.
     * @var string ORDER_EXPECTED_SHIPPING_DATE_FIELD
     **/
    public const ORDER_EXPECTED_SHIPPING_DATE_FIELD = 'rc_expected_shipping_date';

    /**
     * Construct the plugin.
     */
    public function __construct()
    {
        add_action('woocommerce_admin_order_data_after_shipping_address', array($this, 'add_fields_after_shipping_adddress'));
        add_action('woocommerce_process_shop_order_meta', array($this, 'save_order_details'));

        add_filter('manage_edit-shop_order_columns',  array($this, 'add_shop_order_columns'));
        add_action('manage_shop_order_posts_custom_column', array($this, 'display_column_details'), 10, 2);
    }

    /**
     * Add a new date field on order edit page.
     *
     * @param \WC_Order $order The order object.
     */
    public function add_fields_after_shipping_adddress($order)
    {
        $expected_shipping_date = $order->get_meta(self::ORDER_EXPECTED_SHIPPING_DATE_FIELD);

?>

        <div class="address">
            <strong>Expected Shipping date:</strong><?php echo $expected_shipping_date ?? ''; ?>
        </div>

        <div class="edit_address">
            <?php
            woocommerce_wp_text_input(array(
                'id' => self::ORDER_EXPECTED_SHIPPING_DATE_FIELD,
                'label' => 'Expected Shipping Date:',
                'class' => 'date-picker',
                'value' => $expected_shipping_date ?? '',
                'wrapper_class' => 'form-field-wide'
            ));
            ?>
        </div>

<?php
    }

    /**
     * Save the order fields as order meta.
     *
     * @param int $order_id The ID of the order.
     */
    public function save_order_details($order_id)
    {

        $order = wc_get_order($order_id);
        $order->update_meta_data(self::ORDER_EXPECTED_SHIPPING_DATE_FIELD, wc_clean($_POST[self::ORDER_EXPECTED_SHIPPING_DATE_FIELD]));
        $order->save();
    }

    /**
     * Add a new column header.
     *
     * @param array $columns The existing columns.
     * @return array
     */
    public function add_shop_order_columns($columns)
    {
        $columns[self::ORDER_EXPECTED_SHIPPING_DATE_FIELD] = __('Expected Shipping Date', 'rct-customiztion');
        return $columns;
    }

    /**
     * Add a new column header.
     *
     * @param string $column The column name.
     * @param int $post_id The post ID.
     */
    public function display_column_details($column, $post_id)
    {
        switch ($column) {
            case self::ORDER_EXPECTED_SHIPPING_DATE_FIELD:

                $order = wc_get_order($post_id);
                if (!empty($order)) {
                    echo esc_html($order->get_meta(self::ORDER_EXPECTED_SHIPPING_DATE_FIELD));
                }

                break;
        }
    }
}
