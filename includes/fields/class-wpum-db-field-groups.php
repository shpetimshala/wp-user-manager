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

	/**
	 * Create the table
	 *
	 * @access  public
	 * @since   1.0.0
	*/
	public function create_table() {

		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = "CREATE TABLE " . $this->table_name . " (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		name varchar(150) NOT NULL,
		description mediumtext NOT NULL,
		group_order bigint(20) NOT NULL DEFAULT '0',
		can_delete tinyint(1) NOT NULL,
		PRIMARY KEY  (id),
		KEY can_delete (can_delete)
		) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}
}
