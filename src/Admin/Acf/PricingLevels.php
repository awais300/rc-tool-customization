<?php

namespace EWA\RCTool\Admin\Acf;

defined('ABSPATH') || exit;

/**
 * Class PricingLevels
 * @package EWA\RCTool\Admin\Acf
 */
class PricingLevels
{
    /**
     * Construct the plugin.
     */
    public function __construct()
    {
        add_action('acf/init', array($this, 'add_pricing_level_page'));
        add_action('acf/include_fields', array($this, 'init_acf_pricing_level_fields'));
    }

    /**
     * Add a page.
     **/
    public function add_pricing_level_page()
    {
        acf_add_options_page(array(
            'page_title' => 'Pricing Levels',
            'menu_slug' => 'pricing-levels',
            'parent_slug' => 'rctool-upload',
            'redirect' => false,
        ));
    }

    /**
     * Load ACF fields for WP User.
     */
    public function init_acf_pricing_level_fields()
    {
        if (function_exists('acf_add_local_field_group')) {

            acf_add_local_field_group(array(
                'key' => 'group_661d891f31a4f',
                'title' => 'Pricing Levels',
                'fields' => array(
                    array(
                        'key' => 'field_661d9fededa80',
                        'label' => 'Important!',
                        'name' => '',
                        'aria-label' => '',
                        'type' => 'message',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'message' => 'Please refrain from changing anything within this section. This area is utilized for development purposes and for creating logical constructs within the code. Modifying the contents below may lead to unforeseen outcomes or errors.',
                        'new_lines' => 'wpautop',
                        'esc_html' => 0,
                    ),
                    array(
                        'key' => 'field_661d8d07d3322',
                        'label' => 'Historical Pricing',
                        'name' => '',
                        'aria-label' => '',
                        'type' => 'tab',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'placement' => 'top',
                        'endpoint' => 0,
                    ),
                    array(
                        'key' => 'field_661d8920a8c4d',
                        'label' => 'Price Break Levels',
                        'name' => 'price_break_levels',
                        'aria-label' => '',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'layout' => 'table',
                        'pagination' => 0,
                        'min' => 0,
                        'max' => 9,
                        'collapsed' => '',
                        'button_label' => 'Add Row',
                        'rows_per_page' => 20,
                        'sub_fields' => array(
                            array(
                                'key' => 'field_661d8c07a8c4e',
                                'label' => 'Series',
                                'name' => 'series',
                                'aria-label' => '',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'maxlength' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'parent_repeater' => 'field_661d8920a8c4d',
                            ),
                            array(
                                'key' => 'field_661d8c19a8c4f',
                                'label' => 'Level1',
                                'name' => 'level1',
                                'aria-label' => '',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'maxlength' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'parent_repeater' => 'field_661d8920a8c4d',
                            ),
                            array(
                                'key' => 'field_661d8c38a8c50',
                                'label' => 'Level2',
                                'name' => 'level2',
                                'aria-label' => '',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'maxlength' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'parent_repeater' => 'field_661d8920a8c4d',
                            ),
                            array(
                                'key' => 'field_661d8c47a8c51',
                                'label' => 'Level3',
                                'name' => 'level3',
                                'aria-label' => '',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'maxlength' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'parent_repeater' => 'field_661d8920a8c4d',
                            ),
                            array(
                                'key' => 'field_661d8c54a8c52',
                                'label' => 'Level4',
                                'name' => 'level4',
                                'aria-label' => '',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'maxlength' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'parent_repeater' => 'field_661d8920a8c4d',
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_661d95502b634',
                        'label' => 'Quantity Pricing',
                        'name' => '',
                        'aria-label' => '',
                        'type' => 'tab',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'placement' => 'top',
                        'endpoint' => 0,
                    ),
                    array(
                        'key' => 'field_661d8d7d2d5e9',
                        'label' => 'Quantity Purchased Levels',
                        'name' => 'qty_purchased_levels',
                        'aria-label' => '',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'layout' => 'table',
                        'pagination' => 0,
                        'min' => 0,
                        'max' => 10,
                        'collapsed' => '',
                        'button_label' => 'Add Row',
                        'rows_per_page' => 20,
                        'sub_fields' => array(
                            array(
                                'key' => 'field_661d8d7d2d5ea',
                                'label' => 'Series',
                                'name' => 'series',
                                'aria-label' => '',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'maxlength' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'parent_repeater' => 'field_661d8d7d2d5e9',
                            ),
                            array(
                                'key' => 'field_661d8d7d2d5eb',
                                'label' => 'Level1',
                                'name' => 'level1',
                                'aria-label' => '',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'maxlength' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'parent_repeater' => 'field_661d8d7d2d5e9',
                            ),
                            array(
                                'key' => 'field_661d8d7d2d5ec',
                                'label' => 'Level2',
                                'name' => 'level2',
                                'aria-label' => '',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'maxlength' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'parent_repeater' => 'field_661d8d7d2d5e9',
                            ),
                            array(
                                'key' => 'field_661d8d7d2d5ed',
                                'label' => 'Level3',
                                'name' => 'level3',
                                'aria-label' => '',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'maxlength' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'parent_repeater' => 'field_661d8d7d2d5e9',
                            ),
                            array(
                                'key' => 'field_661d8d7d2d5ee',
                                'label' => 'Level4',
                                'name' => 'level4',
                                'aria-label' => '',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'maxlength' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'parent_repeater' => 'field_661d8d7d2d5e9',
                            ),
                        ),
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'pricing-levels',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
                'show_in_rest' => 0,
            ));
        }
    }
}
