<?php
/**
 * Plugin Template Actions
 * This file holds all the template actions
 * that have effects on the templating system of the plugin.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Action to display helper links.
 * 
 * @since 1.0.0
 * @access public
 * @param string $login yes/no
 * @param string $register yes/no
 * @param string $password yes/no
 * @return void
 */
function wpum_add_links_to_forms( $login, $register, $password ) {

	get_wpum_template( 'helper-links.php', 
		array(
			'login'    => esc_attr( $login ),
			'register' => esc_attr( $register ),
			'password' => esc_attr( $password )
		)
	);

}
add_action( 'wpum_do_helper_links', 'wpum_add_links_to_forms', 10, 3 );

/**
 * Add helper links to the password form.
 * 
 * @since 1.0.0
 * @access public
 * @param array $atts Settings of the shortcode.
 * @return void
 */
function wpum_add_helper_links( $atts ) {

	$login_link    = $atts['login_link'];
	$psw_link      = $atts['psw_link'];
	$register_link = $atts['register_link'];

	// Display helper links
	do_action( 'wpum_do_helper_links', $login_link, $register_link, $psw_link );

}
add_action( 'wpum_after_password_form_template', 'wpum_add_helper_links', 10, 1 );
add_action( 'wpum_after_register_form_template', 'wpum_add_helper_links', 10, 1 );

if ( ! function_exists( 'wpum_profile_show_user_name' ) ) :
/**
 * Display user name in profile.php template.
 * 
 * @since 1.0.0
 * @param object $user_data holds WP_User object
 * @access public
 * @return void
 */
function wpum_profile_show_user_name( $user_data ) {

	$output = '<div class="wpum-user-display-name">';
		$output .= '<a href="'. wpum_get_user_profile_url( $user_data ) .'">'. esc_attr( $user_data->display_name ) .'</a>';
	$output .= '</div>';

	echo $output;

}
add_action( 'wpum_main_profile_details', 'wpum_profile_show_user_name', 10 );
endif;

if ( ! function_exists( 'wpum_profile_show_user_description' ) ) :
/**
 * Display user description in profile.php template.
 * 
 * @since 1.0.0
 * @param object $user_data holds WP_User object
 * @access public
 * @return void
 */
function wpum_profile_show_user_description( $user_data ) {

	$output = '<div class="wpum-user-description">';
		$output .= wpautop( esc_attr( get_user_meta( $user_data->ID, 'description', true) ), true );
	$output .= '</div>';

	echo $output;

}
add_action( 'wpum_main_profile_details', 'wpum_profile_show_user_description', 10 );
endif;

if ( ! function_exists( 'wpum_profile_show_user_links' ) ) :
/**
 * Display user name in profile.php template.
 * 
 * @since 1.0.0
 * @param object $user_data holds WP_User object
 * @access public
 * @return void
 */
function wpum_profile_show_user_links( $user_data ) {

	$output = get_wpum_template( 'profile-links.php', array( 'user_data' => $user_data ) );

	echo $output;

}
add_action( 'wpum_secondary_profile_details', 'wpum_profile_show_user_links', 10 );
endif;