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
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		
		// retrieve login method
		$this->login_method = wpum_get_option('login_method');

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

		// Profile Update Method
		add_action( 'wp_ajax_wpum_update_profile', array( $this, 'update_profile' ) );
		add_action( 'wp_ajax_nopriv_wpum_update_profile', array( $this, 'update_profile' ) );

		// Validate Password Field on profile form
		add_filter( 'wpum_form_validate_ajax_profile_fields', array( __CLASS__, 'validate_password_field' ), 10, 2 );
		add_filter( 'wpum_form_validate_ajax_profile_fields', array( __CLASS__, 'validate_email_field' ), 10, 2 );
		if(wpum_get_option('exclude_usernames'))
			add_filter( 'wpum_form_validate_ajax_profile_fields', array( __CLASS__, 'validate_nickname_field' ), 10, 2 );

		// Registration method
		add_action( 'wp_ajax_wpum_register', array( $this, 'registration_form' ) );
		add_action( 'wp_ajax_nopriv_wpum_register', array( $this, 'registration_form' ) );

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
		if( $this->login_method == 'email' ) {

			$get_user_email = $_REQUEST['username'];
			
			if( is_email( $get_user_email ) ) :
				$user = get_user_by( 'email', $get_user_email );
				$data['user_login'] = $user->user_login;
			endif;

		// Login via email or username
		} elseif ($this->login_method == 'username_email') {

			$get_username = sanitize_user( $_REQUEST['username'] );

			if( is_email( $get_username ) ) :
				$user = get_user_by( 'email', $get_username );
				if($user !== false) :
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
			echo json_encode( array(
				'loggedin' => false,
				'message'  => __( 'Wrong username or password.' ),
			) );
		} else {
			echo json_encode( array(
				'loggedin' => true,
				'message'  => __( 'Login successful.' ),
			) );
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
	public function restore_emails() {

		// Check our nonce and make sure it's correct.
		check_ajax_referer( 'wpum_nonce_login_form', 'wpum_backend_security' );

		// Abort if something isn't right.
		if( !is_admin() || !current_user_can( 'manage_options' ) ) {
			echo json_encode( array(
				'message'  => __( 'Error.' ),
			 ) );
			return;
		}

		// Default emails array
		$default_emails = array();

		// Delete the option
		delete_option('wpum_emails');

		// Get all registered emails
		$emails = WPUM_Emails_Editor::get_emails_list();	

		// Cycle through the emails and build the list
		foreach ($emails as $email) {

			if ( method_exists( 'WPUM_Emails', "default_{$email['id']}_mail_subject" ) && method_exists( 'WPUM_Emails', "default_{$email['id']}_mail_message" ) ) {

				$default_emails[ $email['id'] ] = array(
		            'subject' => call_user_func( "WPUM_Emails::default_{$email['id']}_mail_subject" ),
		            'message' => call_user_func( "WPUM_Emails::default_{$email['id']}_mail_message" ),
		        );

			}

		}

		update_option( 'wpum_emails', $default_emails );

		echo json_encode( array(
				'message'  => __( 'Emails successfully restored.' ),
			 ) );

		die();

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

		if( !isset( $_REQUEST['form_status'] ) || isset( $_REQUEST['form_status'] ) && $_REQUEST['form_status'] !== 'recover' )
			die();

		$username = $_REQUEST['username'];

		// Validate the username
		if( is_email( $username ) && !email_exists( $username ) || !is_email( $username ) && !username_exists( $username ) ) {
			echo json_encode( array(
				'valid' => false,
				'message'  => __( 'This user could not be found.' ),
			) );
			die();
		}

		// Load the form class and use it's method to retrieve the password recovery mail
		$get_form = WPUM()->forms->load_form_class( 'password' );
		$send = $get_form::retrieve_password( $username );

		// Verify is recovery was successful
		if ( $send ) :
			echo json_encode( array(
					'valid' => true,
					'message'  => __( 'Check your e-mail for the confirmation link.' ),
				 ) );
		else :
			echo json_encode( array(
					'valid' => false,
					'message'  => __( 'Something went wrong.' ),
				 ) );
		endif;

		die();

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

		if( !isset( $_REQUEST['form_status'] ) || isset( $_REQUEST['form_status'] ) && $_REQUEST['form_status'] !== 'reset' )
			die();

		$password_1 = $_REQUEST['password_1'];
		$password_2 = $_REQUEST['password_2'];
		$key        = $_REQUEST['key'];
		$login      = $_REQUEST['login'];
 
		// Validate passwords
		if ( empty( $password_1 ) || empty( $password_2 ) ) {
			echo json_encode( array(
					'completed' => false,
					'message'  => __( 'Please enter your password.' ),
				 ) );
			die();
		}

		// Check if they match
		if ( $password_1 !== $password_2 ) {
			echo json_encode( array(
					'completed' => false,
					'message'  => __( 'Passwords do not match.' ),
				 ) );
			die();
		}
		
		// Load the form class and use it's method to retrieve the password reset function
		$get_form = WPUM()->forms->load_form_class( 'password' );
		$user = $get_form::check_password_reset_key( $key, $login );

		if ( $user instanceof WP_User ) {

			$reset = $get_form::change_password( $user, $password_1 );
			do_action( 'wpum_user_reset_password', $user );

			echo json_encode( array(
					'completed' => true,
					'message'  => __( 'Your password has been reset.' ),
				 ) );

		} else {

			echo json_encode( array(
					'completed' => false,
					'message'  => __( 'Something went wrong.' ),
				 ) );
			
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
		if( !is_admin() || !current_user_can( 'manage_options' ) ) {
			echo json_encode( array(
				'message'  => __( 'Error.' ),
			 ) );
			return;
			die();
		}

		$fields = array();

		if( isset( $_REQUEST['items'] ) && is_array($_REQUEST['items']) ) {
			foreach ($_REQUEST['items'] as $field) {
				$fields[ $field['meta'] ]['order'] = $field['order'];
				$fields[ $field['meta'] ]['required'] = $field['required'];
				$fields[ $field['meta'] ]['show_on_signup'] = $field['show_on_signup'];
				$fields[ $field['meta'] ]['meta'] = $field['meta'];
			}
		}

		update_option( 'wpum_default_fields', $fields );

		echo json_encode( array(
					'completed' => true,
					'message'  => __( 'Fields order successfully updated.' ),
				 ) );

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
		if( !is_admin() || !current_user_can( 'manage_options' ) ) {
			echo json_encode( array(
				'message'  => __( 'Error.' ),
			 ) );
			return;
			die();
		}

		// Delete previously saved option
		delete_option( 'wpum_default_fields' );

		// Declare fields
		$fields = wpum_default_user_fields_list();

        update_option( 'wpum_default_fields', apply_filters( 'wpum_default_fields_restore', $fields ) );

		echo json_encode( array(
				'message'  => __( 'Default fields successfully restored.' ),
			 ) );

		die();

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
		check_ajax_referer( esc_attr($_REQUEST['field']), 'update_nonce' );

		// Abort if something isn't right.
		if( !is_admin() || !current_user_can( 'manage_options' ) ) {
			echo json_encode( array(
				'message'  => __( 'Error.' ),
			 ) );
			return;
			die();
		}

		// Validate it exists
		if( array_key_exists( $field , wpum_default_user_fields_list() ) ) {
			
			$get_fields = get_option( 'wpum_default_fields' );
			$get_fields[ $field ]['required'] = esc_attr( $_REQUEST['required'] );
			$get_fields[ $field ]['show_on_signup'] = esc_attr( $_REQUEST['show_on_signup'] );

			update_option('wpum_default_fields', $get_fields );

			echo json_encode( array(
				'valid'   => true,
				'message' => __( 'Field successfully updated.' ),
			) );

		} else {

			echo json_encode( array(
				'valid'   => false,
				'message' => __( 'Something went wrong.' ),
			) );

		}

		die();

	}

	/**
	 * Update profile on the frontend.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function update_profile() {

		// Check our nonce and make sure it's correct.
		check_ajax_referer( 'profile', 'wpum_profile_nonce' );

		// Get the fields
		$fields = $_REQUEST['fields'];
		
		// Abort if empty
		if( !is_array($fields) || empty($fields) ) {
			echo json_encode( array(
				'valid'   => false,
				'message' => apply_filters( 'wpum_profile_update_error_message', __( 'Something went wrong.' ) )
			) );
			die();
		}
		
		// Sanitize the submitted values
		$fields = WPUM_Utils::sanitize_submitted_fields( $fields );

		// Validate Fields
		if ( is_wp_error( ( $return = WPUM_Utils::validate_fields( $fields, 'profile' ) ) ) ) {
			echo json_encode( array(
				'valid'   => false,
				'message' => $return->get_error_message(),
			) );
			die();
		}

		// Now we can update the profile
		$user_id = intval($_REQUEST['user_id']);
		$user_data = array( 'ID' => $user_id );

		foreach ( $fields as $field_key => $field ) {
			
			switch ($field_key) {
				case 'password':
					if ( !empty( $field['value'] ) ) {
						$user_data += array( 'user_pass' => $field['value'] );
					}
					break;
				case 'password_repeat':
					// do nothing
				break;
				case 'display_name':
					$user_data += array( 'display_name' => self::get_display_name( $fields, $field['value'], $user_id ) );
				break;
				case 'nickname':
					$user_data += array( 'user_nicename' => $field['value'] );
					$user_data += array( 'nickname' => $field['value'] );
				break;
				default:
					$user_data += array( $field_key => $field['value'] );
					break;
			}

		}

		do_action('wpum_before_ajax_update_user', $user_data, $fields, $user_id );

		$user_id = wp_update_user( $user_data );

		do_action('wpum_after_ajax_update_user', $user_data, $fields, $user_id );

		if ( is_wp_error( $user_id ) ) {

			// Show notification message
			echo json_encode( array(
				'valid'   => false,
				'message' => apply_filters( 'wpum_profile_update_error_message', __( 'Something went wrong.' ) )
			) );

			die();

		} else {

			// Show notification message
			echo json_encode( array(
				'valid'   => true,
				'message' => apply_filters( 'wpum_profile_update_success_message', __( 'Profile successfully updated.' ) )
			) );

			die();

		}

	}

	/**
	 * Validate the password field.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function validate_password_field( $passed, $fields ) {

		$pwd = $fields['password']['value'];
		$pwd_2 = $fields['password_repeat']['value'];
		$pwd_strenght = wpum_get_option('password_strength');

		// Do only when the password field is not empty
		if( !empty($pwd) ) {

			if( $pwd !== $pwd_2 )
				return new WP_Error( 'password-validation-error', __( 'Passwords do not match.' ) );

			$containsLetter  = preg_match('/[A-Z]/', $pwd);
			$containsDigit   = preg_match('/\d/', $pwd);
			$containsSpecial = preg_match('/[^a-zA-Z\d]/', $pwd);

			if($pwd_strenght == 'weak') {
				if(strlen($pwd) < 8)
					return new WP_Error( 'password-validation-error', __( 'Password must be at least 8 characters long.' ) );
			}
			if($pwd_strenght == 'medium') {
				if( !$containsLetter || !$containsDigit || strlen($pwd) < 8 )
					return new WP_Error( 'password-validation-error', __( 'Password must be at least 8 characters long and contain at least 1 number and 1 uppercase letter.' ) );
			}
			if($pwd_strenght == 'strong') {
				if( !$containsLetter || !$containsDigit || !$containsSpecial || strlen($pwd) < 8 )
					return new WP_Error( 'password-validation-error', __( 'Password must be at least 8 characters long and contain at least 1 number and 1 uppercase letter and 1 special character.' ) );
			}

		}

		return $passed;

	}

	/**
	 * Validate nickname field.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function validate_nickname_field( $passed, $fields ) {

		$username = $fields['nickname'][ 'value' ];

		if( array_key_exists( $username , wpum_get_disabled_usernames() ) )
			return new WP_Error( 'username-validation-error', __( 'This nickname cannot be used.' ) );

		return $passed;

	}

	/**
	 * Validate email field.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function validate_email_field( $passed, $fields ) {

		$email = $fields['user_email'][ 'value' ];

		if( !is_email( $email ) )
			return new WP_Error( 'email-validation-error', __( 'Please enter a valid email address.' ) );

		return $passed;

	}

	/**
	 * Decides which option should be stored into the database.
	 * This avoids the "display_name" option into the profile form to
	 * save the select field option value into the database.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function get_display_name( $fields, $field_value, $user_id ) {

		$user = get_userdata( $user_id );

		$name = $user->user_login;

		switch ($field_value) {
			case 'display_nickname':
				$name = $fields['nickname']['value'];
				break;
			case 'display_firstname':
				$name = $fields['first_name']['value'];
				break;
			case 'display_lastname':
				$name = $fields['last_name']['value'];
				break;
			case 'display_firstlast':
				$name = $fields['first_name']['value'] . ' ' . $fields['last_name']['value'];
				break;
			case 'display_lastfirst':
				$name = $fields['last_name']['value'] . ' ' . $fields['first_name']['value'];
				break;
			
			default:
				$name = $user->user_login;
				break;
		}

		return $name;

	}

	/**
	 * Triggers ajax registration.
	 * 
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function registration_form() {

		// Check our nonce and make sure it's correct.
		check_ajax_referer( 'register', 'wpum_register_nonce' );

		// Get the fields
		$fields = $_REQUEST['fields'];
		
		// Abort if empty
		if( !is_array($fields) || empty($fields) ) {
			echo json_encode( array(
				'valid'   => false,
				'message' => apply_filters( 'wpum_profile_update_error_message', __( 'Something went wrong.' ) )
			) );
			die();
		}

		// Sanitize the submitted values
		$fields = WPUM_Utils::sanitize_submitted_fields( $fields );

		// Validate Fields
		if ( is_wp_error( ( $return = WPUM_Utils::validate_fields( $fields, 'register' ) ) ) ) {
			echo json_encode( array(
				'valid'   => false,
				'message' => $return->get_error_message(),
			) );
			die();
		}

		// Show notification message
		echo json_encode( array(
			'valid'   => true,
			'message' => apply_filters( 'wpum_profile_update_success_message', __( 'Profile successfully updated.' ) )
		) );

		die();

	}

}

new WPUM_Ajax_Handler;