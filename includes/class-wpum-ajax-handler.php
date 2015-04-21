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

		// Store the default fields order
		add_action( 'wp_ajax_wpum_store_default_fields_order', array( $this, 'store_default_fields_order' ) );
		add_action( 'wp_ajax_nopriv_wpum_store_default_fields_order', array( $this, 'store_default_fields_order' ) );

		// Restore Default Fields
		add_action( 'wp_ajax_wpum_restore_default_fields', array( $this, 'restore_default_fields' ) );

		// Restore Default Fields
		add_action( 'wp_ajax_wpum_update_single_default_field', array( $this, 'update_single_default_fields' ) );

		// Avatar removal method
		add_action( 'wp_ajax_wpum_remove_avatar', array( $this, 'remove_user_avatar' ) );
		add_action( 'wp_ajax_nopriv_wpum_remove_avatar', array( $this, 'remove_user_avatar' ) );

		// Update Custom Fields
		add_action( 'wp_ajax_wpum_load_field_editor', array( $this, 'load_field_editor' ) );

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

			if ( method_exists( 'WPUM_Emails', "default_{$email['id']}_mail_subject" ) && method_exists( 'WPUM_Emails', "default_{$email['id']}_mail_message" ) ) {

				$default_emails[ $email['id'] ] = array(
					'subject' => call_user_func( "WPUM_Emails::default_{$email['id']}_mail_subject" ),
					'message' => call_user_func( "WPUM_Emails::default_{$email['id']}_mail_message" ),
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
	 * Store default fields order.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function store_default_fields_order() {

		// Check our nonce and make sure it's correct.
		check_ajax_referer( 'wpum_nonce_default_fields_table', 'wpum_backend_fields_table' );

		// Abort if something isn't right.
		if ( !is_admin() || !current_user_can( 'manage_options' ) ) {
			$return = array(
				'message' => __( 'Error.' ),
			);

			wp_send_json_error( $return );
		}

		$fields = array();

		if ( isset( $_REQUEST['items'] ) && is_array( $_REQUEST['items'] ) ) {
			foreach ( $_REQUEST['items'] as $field ) {
				$fields[ $field['meta'] ]['order'] = $field['order'];
				$fields[ $field['meta'] ]['required'] = $field['required'];
				$fields[ $field['meta'] ]['show_on_signup'] = $field['show_on_signup'];
				$fields[ $field['meta'] ]['meta'] = $field['meta'];
			}
		}

		update_option( 'wpum_default_fields', $fields );

		$return = array(
			'completed' => true,
			'message'   => __( 'Fields order successfully updated.' ),
		);

		wp_send_json_success( $return );

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
		delete_option( 'wpum_default_fields' );

		// Declare fields
		$fields = wpum_default_user_fields_list();

		update_option( 'wpum_default_fields', apply_filters( 'wpum_default_fields_restore', $fields ) );

		$return = array(
			'message' => __( 'Default fields successfully restored.' ),
		);

		wp_send_json_success( $return );

	}

	/**
	 * Update single default field settings
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function update_single_default_fields() {

		// Check our nonce and make sure it's correct.
		$field = $_REQUEST['field'];
		check_ajax_referer( esc_attr( $_REQUEST['field'] ), 'update_nonce' );

		// Abort if something isn't right.
		if ( !is_admin() || !current_user_can( 'manage_options' ) ) {
			$return = array(
				'message' => __( 'Error.' ),
			);

			wp_send_json_error( $return );
		}

		// Validate it exists
		if ( array_key_exists( $field , wpum_default_user_fields_list() ) ) {

			$get_fields = get_option( 'wpum_default_fields' );
			$get_fields[ $field ]['required'] = esc_attr( $_REQUEST['required'] );
			$get_fields[ $field ]['show_on_signup'] = esc_attr( $_REQUEST['show_on_signup'] );

			update_option( 'wpum_default_fields', $get_fields );

			$return = array(
				'valid'   => true,
				'message' => __( 'Field successfully updated.' ),
			);

			wp_send_json_success( $return );

		} else {

			$return = array(
				'valid'   => false,
				'message' => __( 'Something went wrong.' ),
			);

			wp_send_json_error( $return );

		}

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
		echo json_encode( wpum_display_fields_editor( $field_meta ) );

		die();

	}

}

new WPUM_Ajax_Handler;