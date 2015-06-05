<?php
/**
 * Groups DB class
 * This class is for interacting with the fields groups database table
 *
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_DB_Field_Groups Class
 *
 * @since 1.0.0
 */
class WPUM_DB_Field_Groups extends WPUM_DB {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0.0
	*/
	public function __construct() {

		global $wpdb;

		$this->table_name  = $wpdb->prefix . 'wpum_field_groups';
		$this->primary_key = 'id';
		$this->version     = '1.0';

	}

}
