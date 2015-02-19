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

		add_action( 'wp_ajax_wpum_ajax_login', array( $this, 'do_ajax_login' ) );
		add_action( 'wp_ajax_nopriv_wpum_ajax_login', array( $this, 'do_ajax_login' ) );

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

}

new WPUM_Ajax_Handler;