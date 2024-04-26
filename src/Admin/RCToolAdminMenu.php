<?php

namespace EWA\RCTool\Admin;

use EWA\RCTool\TemplateLoader;
use EWA\RCTool\FileUpload;
use EWA\RCTool\Csv;

defined('ABSPATH') || exit;

/**
 * Class RCToolAdminMenu
 * @package EWA\RCTool\Admin
 */
class RCToolAdminMenu
{
    /**
     * @var TemplateLoader|null The template loader.
     **/
    private $loader = null;

    /**
     * @var FileUpload|null The file object.
     **/
    private $file = null;

    /**
     * @var array|null The file upload details.
     **/
    private $upload_details = null;

    /**
     * RCTool_Admin_Menu constructor.
     * Hooks into the admin menu action
     */
    public function __construct()
    {
        $this->loader = TemplateLoader::get_instance();
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'upload_file'));
    }

    /**
     * Adds admin menu and submenu
     */
    public function add_admin_menu()
    {
        // Add top-level menu
        add_menu_page(
            __('RCTool', 'rct-customization'),
            __('RCTool', 'rct-customization'),
            'manage_options',
            'rctool-upload',
            array($this, 'upload_pricing_page'),
            'dashicons-admin-settings',
            80
        );

        // Add submenu
        add_submenu_page(
            'rctool-upload',
            __('Upload Pricing', 'rct-customization'),
            __('Upload Pricing', 'rct-customization'),
            'manage_options',
            'rctool-upload'
        );
    }

    /**
     * Callback function for submenu page
     */
    public function upload_pricing_page()
    {
        $data = array();
        $file = $this->file;
        $upload_details = $this->upload_details;

        if ($file && $upload_details) {
            $data['file_obj'] = $file;
            $data['upload_details'] = $upload_details;
        }

        $this->loader->get_template(
            'admin/rc-tool-menu-file-upload.php',
            $data,
            RCT_CUST_PLUGIN_DIR_PATH . '/templates/',
            true
        );
    }

    /**
     * Handles file upload
     */
    public function upload_file()
    {
        if (isset($_POST['submit']) && $_POST['submit'] == 'Import') {
            if (!isset($_POST['import_nonce_field']) || !wp_verify_nonce($_POST['import_nonce_field'], 'import_nonce')) {
                wp_die(__('Unauthorized!'));
                exit;
            }
            $file = new FileUpload();
            $file->set_filename('pricing_file');
            $file->set_allowed_mimes(array('csv' => 'text/csv'));
            $uploaded = $file->upload();

            $this->file = $file;
            $this->upload_details = $uploaded;

            $this->import();
        }
    }

    /**
     * Imports data from uploaded CSV file
     */
    public function import()
    {
        $file = $this->file;
        $upload_details = $this->upload_details;

        if ($file && $upload_details) {
            $csv_path = $file->get_uploaded_file_path($upload_details);
            (Csv::get_instance())->import($csv_path);
        }
    }
}
