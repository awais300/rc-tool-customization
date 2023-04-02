<?php

namespace EWA\RCTool\Admin\Acf;

defined('ABSPATH') || exit;

/**
 * Class UserFields
 * @package EWA\RCTool\Admin\Acf
 */

class UserFields
{
	/**
	 * Label for Payment method for a user.
	 * @var META_USER_PAYMENT_TERM_FIELD
	 **/
	public const META_USER_PAYMENT_TERM_FIELD = 'rc_user_payment_term';

	/**
	 * Construct the plugin.
	 */
	public function __construct()
	{
		add_action('init', array($this, 'init_acf_user_fields'));
	}

	/**
	 * Load ACF fields for WP User.
	 */
	public function init_acf_user_fields()
	{
		if (function_exists('acf_add_local_field_group')) {

			acf_add_local_field_group(array(
				'key' => 'group_6428ad7d040c5',
				'title' => 'User Fields',
				'fields' => array(
					array(
						'key' => 'field_6428ad81c4953',
						'label' => 'Distributor Company Name',
						'name' => 'rctoolbox_company_name',
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
					),
					array(
						'key' => 'field_6428adbfc4954',
						'label' => 'Distributor Payment Term',
						'name' => 'rc_user_payment_term',
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
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'user_form',
							'operator' => '==',
							'value' => 'edit',
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
