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
		add_shortcode( 'wpum_logout', array( $this, 'wpum_logout' ) );
		add_shortcode( 'wpum_register', array( $this, 'wpum_registration' ) );
		add_shortcode( 'wpum_password_recovery', array( $this, 'wpum_password' ) );
		add_shortcode( 'wpum_profile_edit', array( $this, 'wpum_profile_edit' ) );
		add_shortcode( 'wpum_profile', array( $this, 'wpum_profile' ) );

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
			'label_log_in'   => '',
			'login_link'     => '',
			'psw_link'       => '',
			'register_link'  => ''
		), $atts ) );

		// Set default values if options missing
		if(empty($id))
			$id = 'wpum_loginform';
		if(empty($redirect))
			$redirect = site_url( $_SERVER['REQUEST_URI'] );
		if(empty($label_username))
			$label_username = wpum_get_username_label();
		if(empty($label_password))
			$label_password = __('Password');
		if(empty($label_remember))
			$label_remember = __('Remember Me');
		if(empty($label_log_in))
			$label_log_in = __('Login');

		$args = array(
			'echo'           => true,
			'redirect'       => esc_url($redirect),
			'form_id'        => esc_attr($id),
			'label_username' => esc_attr($label_username),
			'label_password' => esc_attr($label_password),
			'label_remember' => esc_attr($label_remember),
			'label_log_in'   => esc_attr($label_log_in),
			'id_username'    => esc_attr($id).'user_login',
			'id_password'    => esc_attr($id).'user_pass',
			'id_remember'    => esc_attr($id).'rememberme',
			'id_submit'      => esc_attr($id).'wp-submit',
			'login_link'     => esc_attr($login_link),
			'psw_link'       => esc_attr($psw_link),
			'register_link'  => esc_attr($register_link)
		);

		ob_start();

		// Show already logged in message
		if( is_user_logged_in() ) :

			get_wpum_template( 'already-logged-in.php', 
				array(
					'args' => $args,
					'atts' => $atts,
				)
			);

		// Show login form if not logged in
		else :

			get_wpum_template( 'login-form.php', 
				array(
					'args' => $args,
					'atts' => $atts,
				)
			);

			// Display helper links
			do_action( 'wpum_do_helper_links', $login_link, $register_link, $psw_link );

		endif;

		$output = ob_get_clean();

		return $output;

	}

	/**
	 * Login Form Shortcode
	 *
	 * @access public
	 * @since  1.0.0
	 * @return $output shortcode output
	 */
	public function wpum_logout( $atts, $content=null ) {

		extract( shortcode_atts( array(
			'redirect' => '',
			'label'    => __('Logout')
		), $atts ) );

		$output = null;

		if(is_user_logged_in())
			$output = sprintf( __('<a href="%s">%s</a>'), wpum_logout_url($redirect), esc_attr($label) );

		return $output;

	}

	/**
	 * Registration Form Shortcode
	 *
	 * @access public
	 * @since  1.0.0
	 * @return $output shortcode output
	 */
	public function wpum_registration( $atts, $content=null ) {

		extract( shortcode_atts( array(
			'form_id'       => 'default_registration_form',
			'login_link'    => '',
			'psw_link'      => '',
			'register_link' => ''
		), $atts ) );

		// Set default values
		if( !array_key_exists('form_id', $atts) || empty($atts['form_id']) )
			$atts['form_id'] = 'default_registration_form';

		return WPUM()->forms->get_form( 'register', $atts );

	}

	/**
	 * Password Recovery Form Shortcode
	 *
	 * @access public
	 * @since  1.0.0
	 * @return $output shortcode output
	 */
	public function wpum_password( $atts, $content=null ) {

		extract( shortcode_atts( array(
			'form_id'       => 'default_password_form',
			'login_link'    => '',
			'psw_link'      => '',
			'register_link' => ''
		), $atts ) );

		// Set default values
		if( !array_key_exists('form_id', $atts) || empty($atts['form_id']) )
			$atts['form_id'] = 'default_password_form';

		return WPUM()->forms->get_form( 'password', $atts );

	}

	/**
	 * Profile Edit Form Shortcode
	 *
	 * @access public
	 * @since  1.0.0
	 * @return $output shortcode output
	 */
	public function wpum_profile_edit( $atts, $content=null ) {

		extract( shortcode_atts( array(
			'' => ''
		), $atts ) );

		return WPUM()->forms->get_form( 'profile', $atts );

	}

	/**
	 * Profile Shortcode
	 *
	 * @access public
	 * @since  1.0.0
	 * @return $output shortcode output
	 */
	public function wpum_profile( $atts, $content=null ) {

		extract( shortcode_atts( array(
			'' => ''
		), $atts ) );

		ob_start();
		
		// Check if profiles can be seen by guests
		if( !wpum_guests_can_view_profiles() ) {

			return;
		}

		get_wpum_template( 'profile-card.php', array( 
				'user_data' => wpum_get_user_by_data(),
			) 
		);

		$output = ob_get_clean();

		return $output;

	}

}

new WPUM_Shortcodes;