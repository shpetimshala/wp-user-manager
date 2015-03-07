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
	 * Defines the list of default fields
	 *
	 * @since 1.0.0
	 * @return void
	*/
	public static function default_user_fields_list() {

		$fields = array();

        $fields['first_name'] = array(
            'order'    => 0,
            'title'    => __('First Name'),
            'type'     => 'text',
            'meta'     => 'first_name',
            'required' => false,
        );

        $fields['last_name'] = array(
            'order'    => 1,
            'title'    => __('Last Name'),
            'type'     => 'text',
            'meta'     => 'last_name',
            'required' => false,
        );

        $fields['nickname'] = array(
            'order'    => 2,
            'title'    => __('Nickname'),
            'type'     => 'text',
            'meta'     => 'nickname',
            'required' => true,
        );

        $fields['display_name'] = array(
            'order'    => 3,
            'title'    => __('Display Name'),
            'type'     => 'select',
            'meta'     => 'display_name',
            'required' => true,
        );

        $fields['user_email'] = array(
            'order'    => 4,
            'title'    => __('Email'),
            'type'     => 'email',
            'meta'     => 'user_email',
            'required' => true,
        );

        $fields['user_url'] = array(
            'order'    => 5,
            'title'    => __('Website'),
            'type'     => 'text',
            'meta'     => 'user_url',
            'required' => false,
        );

        $fields['description'] = array(
            'order'    => 6,
            'title'    => __('Description'),
            'type'     => 'textarea',
            'meta'     => 'description',
            'required' => false,
        );

        $fields['password'] = array(
            'order'    => 7,
            'title'    => __('Password'),
            'type'     => 'password',
            'meta'     => 'password',
            'required' => true,
        );

        $fields = apply_filters( 'wpum_default_fields_list', $fields );

        return $fields;

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