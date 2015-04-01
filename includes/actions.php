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
 * 
 * @since 1.0.0
 * @access public
 * @return string nonce field
 */
function wpum_add_nonce_to_login_form() {
	return wp_nonce_field( "wpum_nonce_login_form", "wpum_nonce_login_security" );
}
add_action( 'login_form_bottom', 'wpum_add_nonce_to_login_form' );

/**
 * Stops users from accessing wp-login.php?action=register
 * 
 * @since 1.0.0
 * @access public
 * @return void
 */
function wpum_restrict_wp_register() {

	if(wpum_get_option('wp_login_signup_redirect')):
		$permalink = wpum_get_option('wp_login_signup_redirect');
		wp_redirect( esc_url( get_permalink( $permalink ) ) );
    	exit();
    endif;

}
add_action( 'login_form_register', 'wpum_restrict_wp_register' );

/**
 * Stops users from seeing the admin bar on the frontend.
 * 
 * @since 1.0.0
 * @access public
 * @return void
 */
function wpum_remove_admin_bar() {

	$excluded_roles = wpum_get_option('adminbar_roles');
	$user = wp_get_current_user();

	if( !empty($excluded_roles) && array_intersect($excluded_roles, $user->roles ) && !is_admin() ) {
		if ( current_user_can( $user->roles[0] ) ) {
		  show_admin_bar(false);
		}
	}

}
add_action('after_setup_theme', 'wpum_remove_admin_bar');

/**
 * Stops users from seeing the profile.php page in wp-admin.
 * 
 * @since 1.0.0
 * @access public
 * @return void
 */
function wpum_remove_profile_wp_admin() {

	if( !current_user_can('administrator') && IS_PROFILE_PAGE && wpum_get_option('backend_profile_redirect') ){
		wp_redirect( esc_url( get_permalink( wpum_get_option('backend_profile_redirect' ) ) ) );
        exit;
	}

}
add_action( 'load-profile.php', 'wpum_remove_profile_wp_admin' );

/**
 * Show content of the User ID column in user list page
 * 
 * @since 1.0.0
 * @access public
 * @return array
 */
function wpum_show_user_id_column_content( $value, $column_name, $user_id ) {
	if ( 'user_id' == $column_name )
		return $user_id;
    return $value;
}
add_action( 'manage_users_custom_column',  'wpum_show_user_id_column_content', 10, 3 );