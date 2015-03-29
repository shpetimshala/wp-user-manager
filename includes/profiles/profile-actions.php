<?php
/**
 * User profiles actions.
 * Holds templating actions to display various components of the layout.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

/**
 * Force 404 error if user or tabs do not exist.
 * 
 * @since 1.0.0
 * @access public
 * @return void
 */
function wpum_profile_force_404_error() {
	
	global $wp_query;

	$wp_query->set_404();
    status_header( 404 );
    nocache_headers();

}
//add_action( 'wp', 'wpum_profile_force_404_error' );

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

		// Show edit account only when viewing own profile
		if( $user_data->ID == get_current_user_id() )
			$output .= '<small><a href="'. wpum_get_core_page_url('account') .'" class="wpum-profile-account-edit">'. __(' (Edit Account)') .'</a></small>';
		
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

	$output = get_wpum_template( 'profile/profile-links.php', array( 'user_data' => $user_data ) );

	echo $output;

}
add_action( 'wpum_secondary_profile_details', 'wpum_profile_show_user_links', 10 );
endif;