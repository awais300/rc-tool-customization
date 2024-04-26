<?php

namespace EWA\RCTool;

defined('ABSPATH') || exit;

/**
 * Class FileUpload
 * Handles uploading files.
 *
 * @package EWA\RCTool
 */
class FileUpload extends Singleton{
    
    /**
     * @var string|null The filename for the uploaded file
     */
    protected $filename        = null;
    
    /**
     * @var array Allowed MIME types for the uploaded file
     */
    protected $allowed_mimes   = array();

    // Private constructor to prevent direct instantiation
    public function __construct() {
    }

    /**
     * Set the filename for the uploaded file.
     *
     * @param string $filename The filename to set.
     * @return void
     */
    public function set_filename( $filename ) {
        $this->filename = $filename;
    }

    /**
     * Get the filename for the uploaded file.
     *
     * @return string The filename.
     * @throws \Exception If filename is not set.
     */
    public function get_filename() {
        if ( ! empty( $this->filename ) ) {
            return $this->filename;
        } else {
            throw new \Exception( 'File name is mandatory' );
        }
    }

    /**
     * Set the allowed MIME types for the uploaded file.
     *
     * @param array $allowed_mimes Array of allowed MIME types.
     * @return void
     * @throws \Exception If MIME types are not passed as array.
     */
    public function set_allowed_mimes( $allowed_mimes = array() ) {
        if ( is_array( $allowed_mimes ) ) {
            $this->allowed_mimes = $allowed_mimes;
        } else {
            throw new \Exception( 'Mime should be passed as array' );
        }
    }

    /**
     * Get the allowed MIME types for the uploaded file.
     *
     * @return array Allowed MIME types.
     */
    public function get_allowed_mimes() {
        return $this->allowed_mimes;
    }

    /**
     * Upload the file.
     *
     * @return array Uploaded file details.
     */
    public function upload() {
        $file          = isset( $_FILES ) ? $_FILES : array();
        $allowed_mimes = $this->get_allowed_mimes();
        $filename      = $this->get_filename();

        // Handle the file upload using WordPress function wp_handle_sideload.
        $uploaded_file = wp_handle_sideload(
            $file[ $filename ],
            array(
                'test_form' => false,
                'mimes'     => $allowed_mimes,
                'unique_filename_callback' => array( $this, 'rename_uploaded_file' ),
            )
        );

        return $uploaded_file;
    }

    /**
     * Callback function to rename the uploaded file.
     *
     * @param string $dir Directory path.
     * @param string $name Original filename.
     * @param string $ext File extension.
     * @return string Renamed filename.
     */
    public function rename_uploaded_file( $dir, $name, $ext ) {
        return rand() . $name . $ext;
    }

    /**
     * Check if file upload is successful.
     *
     * @param array $uploaded_file Uploaded file details.
     * @return bool True if upload is successful, false otherwise.
     */
    public function is_upload_successful( $uploaded_file ) {
        if ( isset( $uploaded_file['error'] ) ) {
            return false;
        }
        return true;
    }

    /**
     * Get the uploaded file's URL.
     *
     * @param array $uploaded_file Uploaded file details.
     * @return string|null Uploaded file URL.
     */
    public function get_uploaded_file_url( $uploaded_file ) {
        if ( isset( $uploaded_file['url'] ) ) {
            return $uploaded_file['url'];
        }
        return null;
    }

    /**
     * Get the uploaded file's path.
     *
     * @param array $uploaded_file Uploaded file details.
     * @return string|null Uploaded file path.
     */
    public function get_uploaded_file_path( $uploaded_file ) {
        if ( isset( $uploaded_file['file'] ) ) {
            return $uploaded_file['file'];
        }
        return null;
    }

    /**
     * Handle file upload errors gracefully.
     *
     * @param array $uploaded_file Uploaded file details.
     * @return void
     * @throws \Exception If file upload fails.
     */
    public function handle_upload_error( $uploaded_file ) {
        if ( isset( $uploaded_file['error'] ) ) {
            //throw new \Exception( 'File upload failed with error: ' . $uploaded_file['error'] );
            return $uploaded_file['error'];
        }
    }
}
