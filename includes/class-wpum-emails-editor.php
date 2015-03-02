<?php
/**
 * Emails Editor
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_Emails_Editor Class
 *
 * @since 1.0.0
 */
class WPUM_Emails_Editor {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		
		if( ! class_exists( 'WP_List_Table' ) ) {
		    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
		}
		add_action( 'wpum_emails_editor', array( $this, 'wpum_option_emails_list' ) );

	}

	/**
	 * Display Table with list of emails.
	 *
	 * @since 1.0.0
	 * @return void
	*/
	public function wpum_option_emails_list() {

		ob_start();

		// Prepare the table for display
		$wpum_emails_table = new WPUM_Emails_List();
	    $wpum_emails_table->prepare_items();
	    $wpum_emails_table->display();

		echo ob_get_clean();

	}

}
new WPUM_Emails_Editor;