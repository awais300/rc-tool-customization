<?php

namespace EWA\RCTool;

defined('ABSPATH') || exit;

/**
 * Class Logger
 * Handles logging functionality.
 *
 * @package EWA\RvnewsImportExport
 */
class Logger extends Singleton
{
    /**
     * The name of the directory.
     *
     * @var string DIR_NAME
     */
    public const DIR_NAME = 'rc-tool-customization';

    /**
     * Get the directory path for logging.
     *
     * @return string The directory path.
     */
    public function get_directory()
    {
        // Get the uploads directory path
        $upload_dir = wp_upload_dir();
        $new_directory = $upload_dir['basedir'] . '/' . self::DIR_NAME;

        if (!file_exists($new_directory)) {
            wp_mkdir_p($new_directory);
        }

        return $new_directory;
    }

    /**
     * Log data to a file.
     *
     * @param mixed $mix The data to log.
     * @param string|null $log_file The name of the log file.
     * @return void
     */
    public function log($mix, $log_file = null)
    {

        if ($log_file == null) {
            $daily_log = date('Y-m-d');
            $file_name = "rctool_{$daily_log}.log";
        } else {
            $file_name = 'rctool_' . $log_file . $daily_log . '.log';
        }

        $log_file_path = trailingslashit($this->get_directory()) . $file_name;

        $data  = '[' . date('Y-m-d H:i:s') . ']' . PHP_EOL;
        $data .= print_r($mix, true);
        file_put_contents($log_file_path, $data . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
