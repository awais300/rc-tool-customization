<?php

namespace EWA\RCTool;

defined('ABSPATH') || exit;

/**
 * Class Blog
 * @package EWA\RCTool
 */

class Blog
{
	/**
	 * The Distributor only category ID.
	 * @var DISTRIBUTOR_CATEGORY_ID
	 **/
	public const DISTRIBUTOR_CATEGORY_ID = '154';


	/**
	 * Construct the plugin.
	 */
	public function __construct()
	{
		add_action('pre_get_posts', array($this, 'exclude_category_from_blog'));
		add_action('template_redirect', array($this, 'is_distributor_post_access'));
	}

	/**
	 * Exclude category from blog.
	 * @param object $query data
	 *
	 */
	public function exclude_category_from_blog($query)
	{
		if (!(Helper::get_instance())->is_distributor()) {
			if ($query->is_main_query() && !is_admin()) {
				$query->set('cat', '-' . self::DISTRIBUTOR_CATEGORY_ID);
			}
		}
	}

	/**
	 * Allow direct access of distributor posts to only for distributor users.
	 */
	public function is_distributor_post_access()
	{
		$object = get_queried_object();

		if ($object && get_post_type($object) === 'post' && is_single()) {
			if (!(Helper::get_instance())->is_distributor() && !in_category(self::DISTRIBUTOR_CATEGORY_ID, $object)) {
				return;
			} else if ((Helper::get_instance()->is_distributor())) {
				return;
			} else {
				wp_redirect('/');
				exit;
			}
		}
	}
}
