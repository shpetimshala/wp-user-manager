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

if ( ! function_exists( 'wpum_get_psw_lenghts' ) ) :
/**
 * Define login methods for options panel
 *
 * @access public
 * @return array
 */
function wpum_get_psw_lenghts() {
	return apply_filters( 'wpum_get_psw_lenghts', array(
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
 *
 * @access public
 * @return string
 */
function wpum_logout_url( $custom_redirect = null ) {
		
	$redirect = null;

	if( !empty($custom_redirect) ) {
		$redirect = esc_url($custom_redirect);
	} else if( wpum_get_option('logout_redirect') ) {
		$redirect = esc_url( wpum_get_option('logout_redirect') );
	}

	return wp_logout_url( $redirect );

}
endif;

if ( ! function_exists( 'wpum_get_username_label' ) ) :
/**
 * Returns the correct username label on the login form
 * based on the selected login method.
 *
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