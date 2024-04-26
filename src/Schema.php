<?php

namespace EWA\RCTool;

defined('ABSPATH') || exit;

/**
 * Class Schema
 * Handles database schema operations.
 *
 * @package EWA\RCTool
 */
class Schema extends Singleton
{
    /**
     * The table name.
     *
     * @var string RCTOOL_PRICING_TABLE
     */
    public const RCTOOL_PRICING_TABLE = 'rctool_pricing_table';

    /**
     * The temporary table name.
     *
     * @var string TEMP_RCTOOL_PRICING_TABLE
     */
    public const TEMP_RCTOOL_PRICING_TABLE = 'temp_rctool_pricing_table';

    /**
     * Create the pricing table in the database.
     *
     * @return void
     */
    public function create_table()
    {
        global $wpdb;
        $table = $wpdb->prefix . self::RCTOOL_PRICING_TABLE;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table ( 
            `id` INT(11) NOT NULL AUTO_INCREMENT , 
            `sku` VARCHAR(100) NOT NULL , 
            `msrp` VARCHAR(50) NULL , 
            `level1` VARCHAR(50) NULL , 
            `level2` VARCHAR(50) NULL , 
            `level3` VARCHAR(50) NULL , 
            `level4` VARCHAR(50) NULL , 
            PRIMARY KEY (`id`), 
            INDEX `sku_index` (`sku`)) 
        ENGINE = InnoDB; 
        $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Create the temporary pricing table in the database.
     *
     * @return void
     */
    public function create_temp_table()
    {
        global $wpdb;

        $this->drop_temp_table();

        // Create the temporary table now.
        $table = $wpdb->prefix . self::TEMP_RCTOOL_PRICING_TABLE;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table ( 
            `id` INT(11) NOT NULL AUTO_INCREMENT , 
            `sku` VARCHAR(100) NOT NULL , 
            `msrp` VARCHAR(50) NULL , 
            `level1` VARCHAR(50) NULL , 
            `level2` VARCHAR(50) NULL , 
            `level3` VARCHAR(50) NULL , 
            `level4` VARCHAR(50) NULL , 
            PRIMARY KEY (`id`), 
            INDEX `sku_index` (`sku`)) 
        ENGINE = InnoDB; 
        $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Rename the temporary table to the original pricing table.
     *
     * @return void
     */
    public function rename_table()
    {
        global $wpdb;
        $temp_table_name = $wpdb->prefix . self::TEMP_RCTOOL_PRICING_TABLE;
        $new_table_name = $wpdb->prefix . self::RCTOOL_PRICING_TABLE;

        $wpdb->query("RENAME TABLE $temp_table_name TO $new_table_name;");
    }

    /**
     * Drop the original pricing table if it exists.
     *
     * @return void
     */
    public function drop_original_table()
    {
        global $wpdb;
        // Drop table if it already exists.
        $table_name = $wpdb->prefix . self::RCTOOL_PRICING_TABLE;
        $wpdb->query("DROP TABLE IF EXISTS $table_name;");
    }

    /**
     * Drop the temporary pricing table if it exists.
     *
     * @return void
     */
    public function drop_temp_table()
    {
        global $wpdb;
        // Drop table if it already exists.
        $table_name = $wpdb->prefix . self::TEMP_RCTOOL_PRICING_TABLE;
        $wpdb->query("DROP TABLE IF EXISTS $table_name;");
    }
}
