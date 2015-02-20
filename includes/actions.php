<?php
/**
 * Plugin Actions
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add nonce field to login form needed for ajax validation
 * @since    1.0.0
 */
function wpum_add_nonce_to_login_form() {
	return wp_nonce_field( "wpum_nonce_login_form", "wpum_nonce_login_security" );
}
add_action( 'login_form_bottom', 'wpum_add_nonce_to_login_form' );

/**
 * Adds a registration link to the login from
 * 
 * @since 1.0.0
 * @uses  wpum_registration_link_label filter for label.
 * @uses  wpum_get_registration_page() function for retrieving the registration page url.
 */
function wpum_add_reg_link_to_loginform( $args ) {

	$output = null;
	$label = apply_filters( 'wpum_registration_link_label', __('Signup Now &raquo;') );
	$url = '';

	if(wpum_get_option('display_registration_link'))
		$output = '<p class="wpum-registration-link"><a href="'.$url.'">'. $label .'</a></p>';

	echo $output;

}
add_action( 'wpum_after_inside_loginform_template', 'wpum_add_reg_link_to_loginform' );

/**
 * Adds a password recovery link to the login from
 * 
 * @since 1.0.0
 * @uses  wpum_pwd_link_label filter for label.
 * @uses  wpum_get_pwd_page() function for retrieving the password recovery page url.
 */
function wpum_add_pwd_link_to_loginform( $args ) {

	$output = null;
	$label = apply_filters( 'wpum_pwd_link_label', __('Lost your password?') );
	$url = '';

	if(wpum_get_option('display_password_link'))
		$output = '<p class="wpum-pwd-link"><a href="'.$url.'">'. $label .'</a></p>';

	echo $output;

}
add_action( 'wpum_after_inside_loginform_template', 'wpum_add_pwd_link_to_loginform' );