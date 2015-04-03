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
		$this->login_method = wpum_get_option( 'login_method' );

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
		add_filter( 'wpum_form_validate_ajax_profile_fields', array( __CLASS__, 'validate_nickname_field' ), 10, 2 );

		// Registration method
		add_action( 'wp_ajax_wpum_register', array( $this, 'registration_form' ) );
		add_action( 'wp_ajax_nopriv_wpum_register', array( $this, 'registration_form' ) );

		// Registration Forms validation methods
		add_filter( 'wpum_form_validate_ajax_register_fields', array( __CLASS__, 'validate_register_email_field' ), 10, 2 );
		if ( !empty( wpum_get_option( 'exclude_usernames' ) ) )
			add_filter( 'wpum_form_validate_ajax_register_fields', array( __CLASS__, 'validate_register_username_field' ), 10, 2 );
		if ( wpum_get_option( 'enable_terms' ) )
			add_filter( 'wpum_form_validate_ajax_register_fields', array( __CLASS__, 'validate_register_terms_field' ), 10, 2 );
		if ( wpum_get_option( 'custom_passwords' ) ) :
			self::$random_password = false;
			add_filter( 'wpum_form_validate_ajax_register_fields', array( __CLASS__, 'validate_register_password_field' ), 10, 2 );
		if ( wpum_get_option( 'login_after_registration' ) )
			add_action( 'wpum_ajax_registration_is_complete', array( __CLASS__, 'do_login' ), 10, 3 );
		endif;
		if ( wpum_get_option( 'enable_honeypot' ) )
			add_filter( 'wpum_form_validate_ajax_register_fields', array( __CLASS__, 'validate_honeypot_register_field' ), 10, 3 );
		if ( wpum_get_option( 'allow_role_select' ) ) :
			add_filter( 'wpum_form_validate_ajax_register_fields', array( __CLASS__, 'validate_role_register_field' ), 10, 3 );
			add_action( 'wpum_ajax_registration_is_complete', array( __CLASS__, 'save_role' ), 10, 10 );
		endif;

		// Avatar removal method
		add_action( 'wp_ajax_wpum_remove_avatar', array( $this, 'remove_user_avatar' ) );
		add_action( 'wp_ajax_nopriv_wpum_remove_avatar', array( $this, 'remove_user_avatar' ) );
		add_action( 'wp_ajax_nopriv_wpum_upload_file', array( $this, 'upload_file' ) );
		add_action( 'wp_ajax_wpum_upload_file', array( $this, 'upload_file' ) );

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
		if ( !is_array( $fields ) || empty( $fields ) ) {
			$return = array(
				'valid'   => false,
				'message' => apply_filters( 'wpum_profile_update_error_message', __( 'Something went wrong.' ) )
			);

			wp_send_json_error( $return );
		}

		// Sanitize the submitted values
		$fields = WPUM_Utils::sanitize_submitted_fields( $fields );

		// Validate Fields
		if ( is_wp_error( ( $return = WPUM_Utils::validate_fields( $fields, 'profile' ) ) ) ) {
			$return = array(
				'valid'   => false,
				'message' => $return->get_error_message()
			);

			wp_send_json_error( $return );
		}

		// Now we can update the profile
		$user_id = intval( $_REQUEST['user_id'] );
		$user_data = array( 'ID' => $user_id );

		foreach ( $fields as $field_key => $field ) {

			switch ( $field_key ) {
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

		do_action( 'wpum_before_ajax_update_user', $user_data, $fields, $user_id );

		$user_id = wp_update_user( $user_data );

		do_action( 'wpum_after_ajax_update_user', $user_data, $fields, $user_id );

		if ( is_wp_error( $user_id ) ) {

			// Show notification message
			$return = array(
				'valid'   => false,
				'message' => apply_filters( 'wpum_profile_update_error_message', __( 'Something went wrong.' ) )
			);

			wp_send_json_error( $return );

		} else {

			// Show notification message
			$return = array(
				'valid'   => true,
				'message' => apply_filters( 'wpum_profile_update_success_message', __( 'Profile successfully updated.' ) )
			);

			wp_send_json_error( $return );

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
		$pwd_strenght = wpum_get_option( 'password_strength' );

		// Do only when the password field is not empty
		if ( !empty( $pwd ) ) {

			if ( $pwd !== $pwd_2 )
				return new WP_Error( 'password-validation-error', __( 'Passwords do not match.' ) );

			$containsLetter  = preg_match( '/[A-Z]/', $pwd );
			$containsDigit   = preg_match( '/\d/', $pwd );
			$containsSpecial = preg_match( '/[^a-zA-Z\d]/', $pwd );

			if ( $pwd_strenght == 'weak' ) {
				if ( strlen( $pwd ) < 8 )
					return new WP_Error( 'password-validation-error', __( 'Password must be at least 8 characters long.' ) );
			}
			if ( $pwd_strenght == 'medium' ) {
				if ( !$containsLetter || !$containsDigit || strlen( $pwd ) < 8 )
					return new WP_Error( 'password-validation-error', __( 'Password must be at least 8 characters long and contain at least 1 number and 1 uppercase letter.' ) );
			}
			if ( $pwd_strenght == 'strong' ) {
				if ( !$containsLetter || !$containsDigit || !$containsSpecial || strlen( $pwd ) < 8 )
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

		if ( wpum_get_option('exclude_usernames') && array_key_exists( $username , wpum_get_disabled_usernames() ) )
			return new WP_Error( 'username-validation-error', __( 'This nickname cannot be used.' ) );

		// Check for nicknames if permalink structure requires unique nicknames.
		if( get_option('wpum_permalink') == 'nickname'  ) :

			$current_user = wp_get_current_user();

			if( $username !== $current_user->user_nicename && wpum_nickname_exists( $username ) )
				return new WP_Error( 'username-validation-error', __( 'This nickname cannot be used.' ) );

		endif;

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

		if ( !is_email( $email ) )
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

		switch ( $field_value ) {
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
	 * Validate email field registration form.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function validate_register_email_field( $passed, $fields ) {

		$email = $fields['user_email'][ 'value' ];

		if ( !is_email( $email ) )
			return new WP_Error( 'email-validation-error', __( 'Please enter a valid email address.' ) );

		if ( email_exists( $email ) )
			return new WP_Error( 'email-validation-error', __( 'Email address already exists.' ) );

		return $passed;

	}

	/**
	 * Validate username field registrations form.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function validate_register_username_field( $passed, $fields ) {

		$username = $fields['username'][ 'value' ];

		if ( array_key_exists( $username , wpum_get_disabled_usernames() ) )
			return new WP_Error( 'username-validation-error', __( 'This username cannot be used.' ) );

		return $passed;

	}

	/**
	 * Validate TOS field registration form.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function validate_register_terms_field( $passed, $fields ) {

		$terms = $fields['terms'][ 'value' ];

		if ( $terms !== "1" )
			return new WP_Error( 'terms-validation-error', __( 'You must agree to the terms & conditions before registering.' ) );

		return $passed;

	}

	/**
	 * Validate Password Registration form.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function validate_register_password_field( $passed, $fields ) {

		$pwd = $fields['password']['value'];
		$pwd_strenght = wpum_get_option( 'password_strength' );

		$containsLetter  = preg_match( '/[A-Z]/', $pwd );
		$containsDigit   = preg_match( '/\d/', $pwd );
		$containsSpecial = preg_match( '/[^a-zA-Z\d]/', $pwd );

		if ( $pwd_strenght == 'weak' ) {
			if ( strlen( $pwd ) < 8 )
				return new WP_Error( 'password-validation-error', __( 'Password must be at least 8 characters long.' ) );
		}
		if ( $pwd_strenght == 'medium' ) {
			if ( !$containsLetter || !$containsDigit || strlen( $pwd ) < 8 )
				return new WP_Error( 'password-validation-error', __( 'Password must be at least 8 characters long and contain at least 1 number and 1 uppercase letter.' ) );
		}
		if ( $pwd_strenght == 'strong' ) {
			if ( !$containsLetter || !$containsDigit || !$containsSpecial || strlen( $pwd ) < 8 )
				return new WP_Error( 'password-validation-error', __( 'Password must be at least 8 characters long and contain at least 1 number and 1 uppercase letter and 1 special character.' ) );
		}

		return $passed;

	}

	/**
	 * Validate honeypot field registration form.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function validate_honeypot_register_field( $passed, $fields ) {

		$fake_field = $fields['comments'][ 'value' ];

		if ( $fake_field )
			return new WP_Error( 'honeypot-validation-error', __( 'Failed Honeypot validation' ) );

		return $passed;

	}

	/**
	 * Validate the role field.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function validate_role_register_field( $passed, $fields ) {

		$role_field = $fields['role'][ 'value' ];
		$selected_roles = array_flip( wpum_get_option( 'register_roles' ) );

		if ( !array_key_exists( $role_field , $selected_roles ) )
			return new WP_Error( 'role-validation-error', __( 'Select a valid role from the list.' ) );

		return $passed;

	}

	/**
	 * Save the role.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function save_role( $user_id, $fields ) {

		$user = new WP_User( $user_id );
		$user->set_role( $fields['role'][ 'value' ] );

	}

	/**
	 * Autologin.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function do_login( $user_id, $fields ) {

		$userdata = get_userdata( $user_id );

		$data = array();
		$data['user_login']    = $userdata->user_login;
		$data['user_password'] = $fields['password']['value'];
		$data['rememberme']    = true;

		$user_login = wp_signon( $data, false );

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
		if ( !is_array( $fields ) || empty( $fields ) ) {
			// Show notification message
			$return = array(
				'valid'   => false,
				'message' => apply_filters( 'wpum_profile_update_error_message', __( 'Something went wrong.' ) )
			);

			wp_send_json_error( $return );
		}

		// Sanitize the submitted values
		$fields = WPUM_Utils::sanitize_submitted_fields( $fields );

		// Validate Fields
		if ( is_wp_error( ( $return = WPUM_Utils::validate_fields( $fields, 'register' ) ) ) ) {
			$return = array(
				'valid'   => false,
				'message' => $return->get_error_message()
			);

			wp_send_json_error( $return );
		}

		// Do Registration
		if ( self::$random_password ) {
			$do_user = register_new_user( $fields['username']['value'], $fields['user_email']['value'] );
		} else {
			$do_user = wp_create_user( $fields['username']['value'], $fields['password']['value'], $fields['user_email']['value'] );
		}

		// Check for errors
		if ( is_wp_error( $do_user ) ) {

			$return = array(
				'valid'   => false,
				'message' => $do_user->get_error_message()
			);

			wp_send_json_error( $return );

		} else {

			// Send notification if password is manually added by the user.
			if ( !self::$random_password ):
				wp_new_user_notification( $do_user, $fields['password']['value'] );
			endif;

			// Add ability to extend registration process.
			$user_id = $do_user;
			do_action( 'wpum_ajax_registration_is_complete', $user_id, $fields );

			// Check for automatic login
			$do_redirect = false;
			if ( wpum_get_option( 'login_after_registration' ) )
				$do_redirect = true;

			// Show notification message
			$return = array(
				'valid'        => true,
				'redirect'     => $do_redirect,
				'redirect_url' => apply_filters( 'wpum_redirect_after_automatic_login', home_url(), $user_id ),
				'message'      => apply_filters( 'wpum_registration_success_message', __( 'Registration complete.' ) )
			);

			wp_send_json_success( $return );

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

}

new WPUM_Ajax_Handler;