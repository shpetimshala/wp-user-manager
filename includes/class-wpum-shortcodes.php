<?php
/**
 * Shortcodes
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_Shortcodes Class
 * Registers shortcodes together with a shortcodes editor.
 *
 * @since 1.0.0
 */
class WPUM_Shortcodes {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		
		add_filter( 'widget_text', 'do_shortcode' );
		add_shortcode( 'wpum_login_form', array( $this, 'wpum_login_form' ) );

	}

	/**
	 * Login Form Shortcode
	 *
	 * @access public
	 * @since  1.0.0
	 * @return $output shortcode output
	 */
	public function wpum_login_form( $atts, $content=null ) {

		extract( shortcode_atts( array(
			'id'             => '',
			'redirect'       => '',
			'label_username' => '',
			'label_password' => '',
			'label_remember' => '',
			'label_log_in'   => ''
		), $atts ) );

		// Set default values if options missing
		if(empty($id))
			$id = 'wpum_loginform';
		if(empty($redirect))
			$redirect = site_url( $_SERVER['REQUEST_URI'] );
		if(empty($label_username))
			$label_username = __('Username');
		if(empty($label_password))
			$label_password = __('Password');
		if(empty($label_remember))
			$label_remember = __('Remember Me');
		if(empty($label_log_in))
			$label_log_in = __('Login');

		$args = array(
			'echo'           => false,
			'redirect'       => $redirect, 
			'form_id'        => $id,
			'label_username' => $label_username,
			'label_password' => $label_password,
			'label_remember' => $label_remember,
			'label_log_in'   => $label_log_in,
			'id_username'    => $id.'user_login',
			'id_password'    => $id.'user_pass',
			'id_remember'    => $id.'rememberme',
			'id_submit'      => $id.'wp-submit',
		);

		$output = wp_login_form( apply_filters( 'wpum_login_shortcode_args', $args, $atts ) );

		return $output;

	}

}

new WPUM_Shortcodes;