<?php

namespace EWA\RCTool;

use League\Csv\Reader;

defined('ABSPATH') || exit;

/**
 * Class Csv
 * @package EWA\RCTool
 */
class Csv extends Singleton
{
	/**
	 * The logger object.
	 *
	 * @var Logger|null $logger
	 */
    private $logger = null;

	/**
	 * Construct the class.
	 */
	public function __construct()
	{
		$this->logger = Logger::get_instance();
	}

	/**
	 * Import data from CSV file.
	 *
	 * @param string $file_path The path to the CSV file.
	 * @return void
	 */
	public function import($file_path)
	{
		$this->logger->log('Creating temp table');
		(Schema::get_instance())->create_temp_table();

		$csv = Reader::createFromPath($file_path, 'r');
		$batch_size = 500; // Number of records to insert in each batch
		$first_batch = true;

		// Initialize an array to store batch data.
		$batch_data = [];

		foreach ($csv as $record) {
			// Add each record to the batch data array.
			$batch_data[] = $record;
			// Check if the batch size has been reached.
			if (count($batch_data) >= $batch_size) {
				if ($first_batch === true) {
					$first_batch = false;
					array_shift($batch_data);
					$this->insert_batch_data($batch_data);
				} else {
					$this->insert_batch_data($batch_data);
				}

				// Clear the batch data array.
				$batch_data = [];
			}
		}

		// Insert any remaining data in the batch.
		if (!empty($batch_data)) {
			if ($first_batch === true) {
				$first_batch = false;
				array_shift($batch_data);
				$this->insert_batch_data($batch_data);
			} else {
				$this->insert_batch_data($batch_data);
			}
		}

		$this->logger->log('Dropping original table');
		(Schema::get_instance())->drop_original_table();
		$this->logger->log('Renaming table');
		(Schema::get_instance())->rename_table();
	}

	/**
	 * Insert batch data into the database.
	 *
	 * @param array $batch_data The batch data to insert.
	 * @return void
	 */
	public function insert_batch_data($batch_data)
	{

		$rows = array();
		$i = 0;
		foreach ($batch_data as $single) {
			$rows[$i][] = esc_sql($single[0]);
			$rows[$i][] = esc_sql($single[1]);
			$rows[$i][] = esc_sql($single[2]);
			$rows[$i][] = esc_sql($single[3]);
			$rows[$i][] = esc_sql($single[4]);
			$rows[$i][] = esc_sql($single[5]);
			$i++;
		}

		global $wpdb;
		$table_name = $wpdb->prefix . Schema::TEMP_RCTOOL_PRICING_TABLE;

		// Prepare the batch insert query to temp table.
		$placeholders = array_fill(0, count($rows[0]), '%s');
		$query = "INSERT INTO $table_name (sku, msrp, level1, level2, level3, level4) VALUES ";
		$value_sets = [];
		foreach ($rows as $row) {
			$escaped_values = array_map(array($wpdb, 'prepare'), $placeholders, $row);
			$value_sets[] = '(' . implode(',', $escaped_values) . ')';
		}
		$query .= implode(',', $value_sets);

		$this->logger->log('Batch Query');
		$this->logger->log($query);
		$wpdb->query($query);
	}
}
