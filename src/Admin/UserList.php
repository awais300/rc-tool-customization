<?php

namespace EWA\RCTool\Admin;

use EWA\RCTool\Admin\Acf\UserFields;

defined('ABSPATH') || exit;

/**
 * Class UserList
 * @package EWA\RCTool\Admin
 */
class UserList
{
    /**
     * User column meta name.
     * @var string price_break_level
     **/
    protected const USER_COLUMN_PRICE_BREAK_LEVEL = 'price_break_level';

    /**
     * Construct the plugin.
     */
    public function __construct()
    {
        add_filter('manage_users_columns', array($this, 'add_custom_user_meta_column'));
        add_action('manage_users_custom_column', array($this, 'display_custom_user_meta_column'), 10, 3);
    }

    /**
     * Add custom column to user listing table.
     *
     * @param array $columns Existing columns.
     * @return array Columns with custom column added.
     */
    public function add_custom_user_meta_column($columns)
    {
        // Add your custom meta column
        $columns[self::USER_COLUMN_PRICE_BREAK_LEVEL] = 'Price Break Level';
        return $columns;
    }

    /**
     * Display custom meta data in custom column.
     *
     * @param string $value      The value to display.
     * @param string $column_name The name of the column.
     * @param int    $user_id    The ID of the user.
     * @return string Value to display in the custom column.
     */
    public function display_custom_user_meta_column($value, $column_name, $user_id)
    {
        if (self::USER_COLUMN_PRICE_BREAK_LEVEL == $column_name) {
            $custom_meta = get_user_meta($user_id, UserFields::META_HISTORICAL_PRICE_BREAK_LEVEL, true);
            $value = $custom_meta ? $custom_meta : 'N/A';
        }

        return $value;
    }
}
