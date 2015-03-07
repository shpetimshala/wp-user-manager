<?php
/**
 * Default Fields Editor
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
class WPUM_Default_Fields_Editor {

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
		
		// Display the table into the settings panel
		add_action( 'wpum_default_fields_editor', array( $this, 'default_fields_editor' ) );

	}

	/**
	 * Display Table with list of default fields.
	 *
	 * @since 1.0.0
	 * @return void
	*/
	public function default_fields_editor() {

		ob_start();

		// Prepare the table for display
		
		echo '<div class="wpum-fields-editor-container">';

		$wpum_fields_table = new WPUM_Default_Fields_List();
	    $wpum_fields_table->prepare_items();
	    $wpum_fields_table->display();

	    echo '<div class="wpum-table-loader"><span id="wpum-spinner" class="spinner wpum-spinner"></span></div></div>';

	    echo '<p class="description">' . __('Click the "Edit Field" button to customize the field.') . '<br/>' . sprintf( __('Click and drag the %s button or the field order number to change the order of the fields.'), '<span class="dashicons dashicons-sort"></span>') . '</p>';

	    echo wp_nonce_field( "wpum_nonce_default_fields_table", "wpum_backend_fields_table" );

		echo ob_get_clean();

	}

}
new WPUM_Default_Fields_Editor;