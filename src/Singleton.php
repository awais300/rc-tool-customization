<?php

namespace EWA\RCTool;

defined('ABSPATH') || exit;

/**
 * Base class for singleton objects.
 * Class Singleton.
 */
abstract class Singleton
{

    private static $instances = array();

    protected function __construct()
    {
    }

    protected function __clone()
    {
    }

    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize singleton');
    }

    /**
     * Get instance object.
     *
     * @return static
     */
    public static function get_instance()
    {
        $cls = get_called_class(); // late-static-bound class name.
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static;
            (self::$instances[$cls])->on_construct();
        }
        return self::$instances[$cls];
    }

    /**
     * Method called on object construction.
     *
     * @return void
     */
    protected function on_construct()
    {
    }

    /**
     * Returns the singleton instance.
     *
     * @return static
     */
    public static function instance()
    {
        return static::get_instance();
    }
}
