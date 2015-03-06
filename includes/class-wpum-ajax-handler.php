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

		$fields = isset( $_REQUEST['items'] ) ? (array) $_REQUEST['items'] : array();

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
		$fields = array();

		$fields[] = array(
            'order'    => 0,
            'meta'     => 'first_name',
            'required' => false,
        );

        $fields[] = array(
            'order'    => 1,
            'meta'     => 'last_name',
            'required' => false,
        );

        $fields[] = array(
            'order'    => 2,
            'meta'     => 'nickname',
            'required' => true,
        );

        $fields[] = array(
            'order'    => 3,
            'meta'     => 'display_name',
            'required' => true,
        );

        $fields[] = array(
            'order'    => 4,
            'meta'     => 'user_email',
            'required' => true,
        );

        $fields[] = array(
            'order'    => 5,
            'meta'     => 'user_url',
            'required' => false,
        );

        $fields[] = array(
            'order'    => 6,
            'meta'     => 'description',
            'required' => false,
        );

        $fields[] = array(
            'order'    => 7,
            'meta'     => 'password',
            'required' => true,
        );

        update_option( 'wpum_default_fields', apply_filters( 'wpum_default_fields_restore', $fields ) );

		echo json_encode( array(
				'message'  => __( 'Default fields successfully restored.' ),
			 ) );

		die();

	}

}

new WPUM_Ajax_Handler;