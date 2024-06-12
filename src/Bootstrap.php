<?php

namespace EWA\RCTool;

use EWA\RCTool\Admin\Settings;
use EWA\RCTool\Admin\Product\LeadTime;
use EWA\RCTool\Admin\Acf\UserFields;
use EWA\RCTool\Admin\UserList;
use EWA\RCTool\Admin\Acf\PricingLevels;
//use EWA\RCTool\Admin\Product\SpecialProductOptions;
use EWA\RCTool\SpecialProductOptions as SpecialProductOptionsFrontend;
use EWA\RCTool\Admin\Order;
use EWA\RCTool\Admin\RCToolAdminMenu;

defined('ABSPATH') || exit;

/**
 * Class Bootstrap
 * @package EWA\RCTool
 */
class Bootstrap
{

	private $version = '1.0.0';

	/**
	 * Instance to call certain functions globally within the plugin.
	 *
	 * @var instance
	 */
	protected static $instance = null;

	/**
	 * Construct the plugin.
	 */
	public function __construct()
	{
		add_action('init', array($this, 'load_plugin'), 0);
	}

	/**
	 * Main Bootstrap instance.
	 *
	 * Ensures only one instance is loaded or can be loaded.
	 *
	 * @static
	 * @return self Main instance.
	 */
	public static function get_instance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Determine which plugin to load.
	 */
	public function load_plugin()
	{
		$this->define_constants();
		$this->init_hooks();
	}

	/**
	 * Define WC Constants.
	 */
	private function define_constants()
	{
		// Path related defines
		$this->define('RCT_CUST_PLUGIN_FILE', RCT_CUST_PLUGIN_FILE);
		$this->define('RCT_CUST_PLUGIN_BASENAME', plugin_basename(RCT_CUST_PLUGIN_FILE));
		$this->define('RCT_CUST_PLUGIN_DIR_PATH', untrailingslashit(plugin_dir_path(RCT_CUST_PLUGIN_FILE)));
		$this->define('RCT_CUST_PLUGIN_DIR_URL', untrailingslashit(plugins_url('/', RCT_CUST_PLUGIN_FILE)));
	}

	/**
	 * Collection of hooks.
	 */
	public function init_hooks()
	{
		add_action('init', array($this, 'load_textdomain'));
		add_action('init', array($this, 'init'), 1);
		add_action('wp_loaded', array($this, 'wp_loaded'));

		add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_styles_admin'));
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
	}

	/**
	 * Localization.
	 */
	public function load_textdomain()
	{
		load_plugin_textdomain('rct-customization', false, dirname(plugin_basename(__FILE__)) . '/languages/');
	}

	/**
	 * Initialize the plugin.
	 */
	public function init()
	{
		new User();
		new Pricing();
		new Settings();
		new UserFields();
		new UserList();
		new PricingLevels();
		new RCToolAdminMenu();
		new PaymentTerms();
		new LeadTime();
		new SingleProduct();
		new Order();
		new MyAccountOrder();
		new SpecialProductOptionsFrontend();
		new Blog();
		
		/*if (!is_admin()) {
			if (
				!(Helper::get_instance())->is_distributor() ||
				WC()->session->get(SpecialProductOptionsFrontend::SESS_RC_SPECIAL_PRODUCT) === 'yes' ||
				(Helper::get_instance())->has_rfq_in_cart()
			) {
				new Cart();
			}
		}*/
	}

	/**
	 * WP Loaded.
	 */
	public function wp_loaded()
	{
		if (!is_admin()) {
			if (
				!(Helper::get_instance())->is_distributor() || (Helper::get_instance())->has_rfq_in_cart()
			) {
				new Cart();
			}
		}
	}

	/**
	 * Enqueue all styles.
	 */
	public function enqueue_styles()
	{
		if (is_product() || is_cart() || is_checkout()) {
			wp_enqueue_style('rct-customization-frontend', RCT_CUST_PLUGIN_DIR_URL . '/assets/css/rct-customization-frontend.css', array(), null, 'all');
		}
	}

	/**
	 * Enqueue all styles in admin area.
	 */
	public function enqueue_styles_admin()
	{
		$screen = get_current_screen();
		if ($screen->id === 'rctool_page_pricing-levels' || ($screen->id === 'product' && isset($_GET['action']) && $_GET['action'] === 'edit')) {
			wp_enqueue_style('rct-customization-admin', RCT_CUST_PLUGIN_DIR_URL . '/assets/css/rct-customization-admin.css', array(), null, 'all');

			wp_enqueue_script('rct-customization-admin', RCT_CUST_PLUGIN_DIR_URL . '/assets/js/rct-customization-admin.js', array('jquery'));
		}
	}


	/**
	 * Enqueue all scripts.
	 */
	public function enqueue_scripts()
	{
		if (is_product() || is_cart()) {
			wp_enqueue_script('rct-customization-frontend', RCT_CUST_PLUGIN_DIR_URL . '/assets/js/rct-customization-frontend.js', array('jquery'));
		}
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string $name
	 * @param string|bool $value
	 */
	public function define($name, $value)
	{
		if (!defined($name)) {
			define($name, $value);
		}
	}
}
