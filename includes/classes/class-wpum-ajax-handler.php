<?php
/**
 * Ajax Handler
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_Ajax_Handler Class
 * Handles all the ajax functionalities of the plugin.
 *
 * @since 1.0.0
 */
class WPUM_Ajax_Handler {

	/**
	 * Store login method
	 *
	 * @var login_method.
	 * @since 1.0.0
	 */
	var $login_method;

	/**
	 * Store password method
	 *
	 * @var random_password.
	 * @since 1.0.0
	 */
	public static $random_password = true;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		// retrieve login method
		$this->login_method = wpum_get_option( 'login_method', 'username' );

		// Login
		add_action( 'wp_ajax_wpum_ajax_login', array( $this, 'do_ajax_login' ) );
		add_action( 'wp_ajax_nopriv_wpum_ajax_login', array( $this, 'do_ajax_login' ) );

		// Restore Email
		add_action( 'wp_ajax_wpum_restore_emails', array( $this, 'restore_emails' ) );

		// Password Recovery
		add_action( 'wp_ajax_wpum_ajax_psw_recovery', array( $this, 'password_recovery' ) );
		add_action( 'wp_ajax_nopriv_wpum_ajax_psw_recovery', array( $this, 'password_recovery' ) );

		// Password Reset
		add_action( 'wp_ajax_wpum_ajax_psw_reset', array( $this, 'password_reset' ) );
		add_action( 'wp_ajax_nopriv_wpum_ajax_psw_reset', array( $this, 'password_reset' ) );

		// Avatar removal method
		add_action( 'wp_ajax_wpum_remove_avatar', array( $this, 'remove_user_avatar' ) );
		add_action( 'wp_ajax_nopriv_wpum_remove_avatar', array( $this, 'remove_user_avatar' ) );

		// Restore Default Fields
		add_action( 'wp_ajax_wpum_restore_default_fields', array( $this, 'restore_default_fields' ) );

		// Show field editor
		add_action( 'wp_ajax_wpum_load_field_editor', array( $this, 'load_field_editor' ) );

		// Update custom fields order
		add_action( 'wp_ajax_wpum_update_fields_order', array( $this, 'update_fields_order' ) );

		// Update single custom field
		add_action( 'wp_ajax_wpum_update_single_field', array( $this, 'update_single_field' ) );

	}

	/**
	 * Execute ajax login process.
	 * Check the login method selected and perform login according to it.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function do_ajax_login() {

		// Check our nonce and make sure it's correct.
		check_ajax_referer( 'wpum_nonce_login_form', 'wpum_nonce_login_security' );

		// Get our form data.
		$data = array();

		// Login via email only method
		if ( $this->login_method == 'email' ) {

			$get_user_email = $_REQUEST['username'];

			if ( is_email( $get_user_email ) ) :
				$user = get_user_by( 'email', $get_user_email );
			$data['user_login'] = $user->user_login;
			endif;

			// Login via email or username
		} elseif ( $this->login_method == 'username_email' ) {

			$get_username = sanitize_user( $_REQUEST['username'] );

			if ( is_email( $get_username ) ) :
				$user = get_user_by( 'email', $get_username );
			if ( $user !== false ) :
				$data['user_login'] = $user->user_login;
			endif;
			else :
				$data['user_login'] = $get_username;
			endif;

			// Default login method via username only
		} else {

			$data['user_login']    = sanitize_user( $_REQUEST['username'] );

		}

		$data['user_password'] = sanitize_text_field( $_REQUEST['password'] );
		$data['rememberme']    = sanitize_text_field( $_REQUEST['rememberme'] );
		$user_login = wp_signon( $data, false );

		// Check the results of our login and provide the needed feedback
		if ( is_wp_error( $user_login ) ) {
			
			$return = array(
				'loggedin' => false,
				'message'  => __( 'Wrong username or password.' ),
			);

			wp_send_json_error( $return );

		} else {

			$return = array(
				'loggedin' => true,
				'message'  => __( 'Login successful.' ),
			);

			wp_send_json_success( $return );

		}

	}

	/**
	 * Restore email into the backend.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function restore_emails() {

		// Check our nonce and make sure it's correct.
		check_ajax_referer( 'wpum_nonce_login_form', 'wpum_backend_security' );

		// Abort if something isn't right.
		if ( !is_admin() || !current_user_can( 'manage_options' ) ) {
			$return = array(
				'message' => __( 'Error.' ),
			);

			wp_send_json_error( $return );
		}

		// Default emails array
		$default_emails = array();

		// Delete the option
		delete_option( 'wpum_emails' );

		// Get all registered emails
		$emails = WPUM_Emails_Editor::get_emails_list();

		// Cycle through the emails and build the list
		foreach ( $emails as $email ) {

			if ( function_exists( "wpum_default_{$email['id']}_mail_subject" ) && function_exists( "wpum_default_{$email['id']}_mail_message" ) ) {

				$default_emails[ $email['id'] ] = array(
					'subject' => call_user_func( "wpum_default_{$email['id']}_mail_subject" ),
					'message' => call_user_func( "wpum_default_{$email['id']}_mail_message" ),
				);

			}

		}

		update_option( 'wpum_emails', $default_emails );

		$return = array(
			'message' => __( 'Emails successfully restored.' ),
		);

		wp_send_json_success( $return );

	}

	/**
	 * Execute ajax psw recovery process.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function password_recovery() {

		// Check our nonce and make sure it's correct.
		check_ajax_referer( 'password', 'wpum_nonce_psw_security' );

		if ( !isset( $_REQUEST['form_status'] ) || isset( $_REQUEST['form_status'] ) && $_REQUEST['form_status'] !== 'recover' )
			die();

		$username = $_REQUEST['username'];

		// Validate the username
		if ( is_email( $username ) && !email_exists( $username ) || !is_email( $username ) && !username_exists( $username ) ) {
			
			$return = array(
				'valid'   => false,
				'message' => __( 'This user could not be found.' ),
			);

			wp_send_json_error( $return );

		}

		// Load the form class and use it's method to retrieve the password recovery mail
		$get_form = WPUM()->forms->load_form_class( 'password' );
		$send = $get_form::retrieve_password( $username );

		// Verify is recovery was successful
		if ( $send ) :

			$return = array(
				'valid'   => true,
				'message' => __( 'Check your e-mail for the confirmation link.' ),
			);

			wp_send_json_success( $return );

		else :

			$return = array(
				'valid'   => false,
				'message' => __( 'Something went wrong.' ),
			);

			wp_send_json_error( $return );

		endif;

	}

	/**
	 * Execute ajax psw reset process.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function password_reset() {

		// Check our nonce and make sure it's correct.
		check_ajax_referer( 'password', 'wpum_nonce_psw_security' );

		if ( !isset( $_REQUEST['form_status'] ) || isset( $_REQUEST['form_status'] ) && $_REQUEST['form_status'] !== 'reset' )
			die();

		$password_1 = $_REQUEST['password_1'];
		$password_2 = $_REQUEST['password_2'];
		$key        = $_REQUEST['key'];
		$login      = $_REQUEST['login'];

		// Validate passwords
		if ( empty( $password_1 ) || empty( $password_2 ) ) {
			
			$return = array(
				'completed' => false,
				'message'   => __( 'Please enter your password.' ),
			);

			wp_send_json_error( $return );

		}

		// Check if they match
		if ( $password_1 !== $password_2 ) {

			$return = array(
				'completed' => false,
				'message'   => __( 'Passwords do not match.' ),
			);

			wp_send_json_error( $return );

		}

		// Load the form class and use it's method to retrieve the password reset function
		$get_form = WPUM()->forms->load_form_class( 'password' );
		$user = $get_form::check_password_reset_key( $key, $login );

		if ( $user instanceof WP_User ) {

			$reset = $get_form::change_password( $user, $password_1 );
			do_action( 'wpum_user_reset_password', $user );

			$return = array(
				'completed' => true,
				'message'	=> __( 'Your password has been reset.' ),
			);

			wp_send_json_success( $return );

		} else {

			$return = array(
				'completed' => false,
				'message'	=> __( 'Something went wrong.' ),
			);

			wp_send_json_error( $return );

		}

		die();

	}

	/**
	 * Restore email into the backend.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function restore_default_fields() {

		// Check our nonce and make sure it's correct.
		check_ajax_referer( 'wpum_nonce_default_fields_restore', 'wpum_backend_fields_restore' );

		// Abort if something isn't right.
		if ( !is_admin() || !current_user_can( 'manage_options' ) ) {
			$return = array(
				'message' => __( 'Error.' ),
			);

			wp_send_json_error( $return );
		}

		// Delete previously saved option
		delete_option( 'wpum_custom_fields' );

		// Declare fields
		$fields = wpum_default_fields_list();

		update_option( 'wpum_custom_fields', apply_filters( 'wpum_default_fields_restore', $fields ) );

		$return = array(
			'message' => __( 'Default fields successfully restored.' ),
		);

		wp_send_json_success( $return );

	}

	/**
	 * Remove the avatar of a user.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function remove_user_avatar() {

		// Check our nonce and make sure it's correct.
		check_ajax_referer( 'profile', 'wpum_removal_nonce' );

		$field_id = $_REQUEST['field_id'];
		$user_id = get_current_user_id();

		if( $field_id && is_user_logged_in() ) {

			delete_user_meta( $user_id, "current_{$field_id}" );

			// Deletes previously selected avatar.
			$previous_avatar = get_user_meta( $user_id, "_current_{$field_id}_path", true );
			if( $previous_avatar )
				unlink( $previous_avatar );

			delete_user_meta( $user_id, "_current_{$field_id}_path" );

			$return = array(
				'valid'   => true,
				'message' => apply_filters( 'wpum_avatar_deleted_success_message', __( 'Your profile picture has been deleted.' ) )
			);

			wp_send_json_success( $return );

		} else {

			$return = array(
				'valid'   => false,
				'message' => __( 'Something went wrong.' )
			);

			wp_send_json_error( $return );

		}

	}

	/**
	 * Updates custom field
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_field_editor() {

		// Grab details
		$field_meta = esc_attr( $_POST['field_meta'] );

		// Check our nonce and make sure it's correct.
		check_ajax_referer( $field_meta, 'field_nonce' );
		
		// Display the editor
		echo wp_json_encode( WPUM_Custom_Fields_Editor::display_fields_editor( $field_meta ) );

		die();

	}

	/**
	 * Updates custom fields order.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function update_fields_order() {

		// Check our nonce and make sure it's correct.
		check_ajax_referer( 'wpum_fields_editor', 'wpum_editor_nonce' );

		// Abort if something isn't right.
		if ( !is_admin() || !current_user_can( 'manage_options' ) ) {
			$return = array(
				'message' => __( 'Error.' ),
			);
			wp_send_json_error( $return );
		}

		// Prepare the array.
		$fields = array();

		// loop through each field from the table and set the array
		if ( isset( $_POST['items'] ) && is_array( $_POST['items'] ) ) {
			foreach ( $_REQUEST['items'] as $field ) {

				$fields[ $field['meta'] ] = array(
					'priority'       => $field['priority'],
					'required'       => $field['required'],
					'show_on_signup' => $field['show_on_signup'],
				);

			}
		}

		// Update the option into the database
		update_option( 'wpum_custom_fields', $fields );

		// Send message
		$return = array(
			'message'   => __( 'Fields order successfully updated.' ),
		);

		wp_send_json_success( $return );

	}

	/**
	 * Update single field.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function update_single_field() {

		// Check our nonce and make sure it's correct.
		check_ajax_referer( 'wpum_single_field', 'update_nonce' );

		// Get the field
		$field = esc_attr( $_POST['field'] );

		// Get the submitted options
		$field_options = $_POST['options'];

		// remove what's not needed
		unset( $field_options['_wpnonce'] );
		unset( $field_options['_wp_http_referer'] );

		// Validate it exists
		if ( array_key_exists( $field , wpum_default_fields_list() ) ) {

			// Get custom fields options
			$get_fields = get_option( 'wpum_custom_fields' );

			foreach ($field_options as $key => $value ) {
				
				switch ( $key ) {
					case 'is_required':
						$get_fields[ $field ]['required'] = ( $value !== 'no' ? true : false );
						break;
					case 'show_on_signup':
						$get_fields[ $field ]['show_on_signup'] = ( $value !== 'no' ? true : false );
						break;
					default:
						$get_fields[ $field ][ $key ] = apply_filters( "wpum/field_editor/$key/save", $value, $field );
						break;
				}

			}

			update_option( 'wpum_custom_fields', $get_fields );

			$return = array(
				'status'  => 'updated',
				'message' => __( 'Field successfully updated.' ),
			);

			wp_send_json_success( $return );

		} else {

			$return = array(
				'status'  => 'error',
				'message' => __( 'Something went wrong.' ),
			);

			wp_send_json_error( $return );

		}

	}

}

new WPUM_Ajax_Handler;