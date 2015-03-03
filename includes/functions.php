<?php
/**
 * Main Functions
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'wpum_get_login_methods' ) ) :
/**
 * Define login methods for options panel
 * 
 * @since 1.0.0
 * @access public
 * @return array
 */
function wpum_get_login_methods() {
	return apply_filters( 'wpum_get_login_methods', array(
		'username'       => __( 'Username only' ),
		'email'          => __( 'Email only' ),
		'username_email' => __( 'Username or Email' ),
	) );
}
endif;

if ( ! function_exists( 'wpum_get_psw_lengths' ) ) :
/**
 * Define login methods for options panel
 *
 * @since 1.0.0
 * @access public
 * @return array
 */
function wpum_get_psw_lengths() {
	return apply_filters( 'wpum_get_psw_lengths', array(
		''       => __( 'Disabled' ),
		'weak'   => __( 'Weak' ),
		'medium' => __( 'Medium' ),
		'strong' => __( 'Strong' ),
	) );
}
endif;

if ( ! function_exists( 'wpum_logout_url' ) ) :
/**
 * A simple wrapper function for the wp_logout_url function
 * 
 * The function checks whether a custom url has been passed,
 * if not, looks for the settings panel option,
 * defaults to wp_logout_url
 * 
 * @since 1.0.0
 * @access public
 * @return string
 */
function wpum_logout_url( $custom_redirect = null ) {
		
	$redirect = null;

	if( !empty($custom_redirect) ) {
		$redirect = esc_url($custom_redirect);
	} else if( wpum_get_option('logout_redirect') ) {
		$redirect = get_permalink( wpum_get_option('logout_redirect') );
	}

	return wp_logout_url( $redirect );

}
endif;

if ( ! function_exists( 'wpum_get_username_label' ) ) :
/**
 * Returns the correct username label on the login form
 * based on the selected login method.
 *
 * @since 1.0.0
 * @access public
 * @return string
 */
function wpum_get_username_label() {
		
	$label = __('Username');

	if( wpum_get_option('login_method') == 'email' ) {
		$label = __('Email');
	} else if( wpum_get_option('login_method') == 'username_email' ) {
		$label = __('Username or email');
	}

	return $label;

}
endif;

if ( ! function_exists( 'wp_new_user_notification' ) ) :
/**
 * Replaces the default wp_new_user_notification function of the core.
 * 
 * Email login credentials to a newly-registered user.
 * A new user registration notification is also sent to admin email.
 *
 * @since 1.0.0
 * @access public
 * @return void
 */
function wp_new_user_notification( $user_id, $plaintext_pass ) {
		
	$user = get_userdata( $user_id );

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	// Send notification to admin if not disabled.
	if( !wpum_get_option('disable_admin_register_email') ) {
		$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
		$message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
		$message .= sprintf(__('E-mail: %s'), $user->user_email) . "\r\n";
		wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);
	}

	/* == Send notification to the user now == */

	if ( empty($plaintext_pass) )
		return;

	// Check if email exists first
	if( wpum_email_exists('register') ) {

		// Retrieve the email from the database
		$register_email = wpum_get_email('register');

		$message = wpautop( $register_email['message'] );
		$message = wpum_do_email_tags( $message, $user_id, $plaintext_pass );

		WPUM()->emails->send( $user->user_email, $register_email['subject'], $message );

	}

}
endif;

if ( !function_exists( 'wp_password_change_notification' ) && wpum_get_option('disable_admin_password_recovery_email') ) :
/**
 * This function is empty, if enabled into the settings panel,
 * it will not send any email to the admin when users reset their password.
 *
 * @since 1.0.0
 * @access public
 * @return void
 */
function wp_password_change_notification() {}

endif;