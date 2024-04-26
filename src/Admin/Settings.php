<?php

namespace EWA\RCTool\Admin;

defined('ABSPATH') || exit;

/**
 * Class Settings
 * @package EWA\RCTool\Admin
 */
class Settings
{
    /**
     * Global lead time field.
     * @var string GLOBAL_LEAD_TIME_FIELD
     **/
    public const GLOBAL_LEAD_TIME_FIELD = 'rc_global_lead_time';

    /**
     * Construct the plugin.
     */
    public function __construct()
    {

        add_filter('woocommerce_get_sections_products', array($this, 'add_global_lead_time_tab'));
        add_filter('woocommerce_get_settings_products', array($this, 'lead_time_settings'), 10, 2);
    }


    /**
     * Register custom tab for settings.
     * 
     * @param array $section The array of product sections.
     * @return array The updated array of product sections.
     */
    public function add_global_lead_time_tab($section)
    {
        $section['rc_tool_global_lead_time'] = __('RC Tool', 'rct-customization');
        return $section;
    }

    /**
     * Global settings.
     * 
     * @param array $settings The array of product settings.
     * @param string $current_section The current section being viewed.
     * @return array The updated array of product settings.
     */
    public function lead_time_settings($settings, $current_section)
    {
        $custom_settings = array();

        if ('rc_tool_global_lead_time' == $current_section) {

            $custom_settings = array(

                array(
                    'name' => __('Global Lead Time Settings', 'rct-customization'),
                    'type' => 'title',
                    'id'   => 'rc_title',
                ),

                array(
                    'name'     => __('Enter Global Lead Time Message', 'rct-customization'),
                    'type'     => 'textarea',
                    'desc'     => __('Global Lead Time Message', 'rct-customization'),
                    'desc_tip' => true,
                    'id'       => self::GLOBAL_LEAD_TIME_FIELD,

                ),

                array(
                    'type' => 'sectionend',
                    'id'   => 'rc_tool_global_lead_time',
                ),

            );

            return $custom_settings;
        } else {
            return $settings;
        }
    }
}
