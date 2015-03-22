<?php
/**
 * User profiles tabs.
 * Displays the tabs and content of each tab into the profile page.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

/**
 * Load profile tabs.
 * 
 * @since 1.0.0
 * @access public
 * @param object $user_data holds WP_User object
 * @return void
 */
function wpum_load_profile_tabs( $user_data ) {

	$output = get_wpum_template( 'profile-tabs.php', array( 'user_data' => $user_data, 'tabs' => wpum_get_user_profile_tabs() ) );

	echo $output;

}
add_action( 'wpum_after_profile_details', 'wpum_load_profile_tabs', 10 );
