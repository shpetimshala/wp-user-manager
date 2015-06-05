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
	 * Get columns and formats
	 *
	 * @access  public
	 * @since   1.0.0
	*/
	public function get_columns() {
		return array(
			'id'          => '%d',
			'name'        => '%s',
			'description' => '%s',
			'can_delete'  => '%s'
		);
	}

	/**
	 * Get default column values
	 *
	 * @access  public
	 * @since   1.0.0
	*/
	public function get_column_defaults() {
		return array(
			'id'          => 0,
			'name'        => '',
			'description' => '',
			'can_delete'  => true
		);
	}

	/**
	 * Add a group
	 *
	 * @access  public
	 * @since   1.0.0
	*/
	public function add( $args = array() ) {

		$defaults = array(
			'id'          => false,
			'name'        => false,
			'description' => '',
			'can_delete'  => true
		);

		// Parse incoming $args into an array and merge it with $defaults
		$args = wp_parse_args( $args, $defaults );

		// Bail if no group name
		if ( empty( $args['name'] ) ) {
			return false;
		}

		return $this->insert( $args, 'field_group' );

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
