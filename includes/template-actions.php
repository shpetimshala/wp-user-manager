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

/**
 * Adds total number of users found on top of the directory.
 * 
 * @since 1.0.0
 * @access public
 * @param int $directory_id directory id number.
 * @param string $users_found amount of users found.
 * @param string $total_users amount of users for pagination.
 * @param string $total_pages amount of pages for pagination.
 * @param bool $page whether its paged or not.
 * @param bool $search_form whether the search form is enabled.
 * @param bool|string $template name if selected - false if default.
 * @param array $user_data contains found users.
 * @return void
 */
function wpum_display_total_users_found( $directory_id, $users_found, $total_users, $total_pages, $paged, $search_form, $template, $user_data ) {

	echo '<div class="wpum-users-found">';

		echo '<span>'.sprintf( __('Found %s users.'), $users_found ).'</span>';

	echo '</div>';

}
add_action( 'wpum_before_user_directory', 'wpum_display_total_users_found', 10, 8 );

/**
 * Adds pagination at the bottom of the user directory.
 * 
 * @since 1.0.0
 * @access public
 * @param int $directory_id directory id number.
 * @param string $users_found amount of users found.
 * @param string $total_users amount of users for pagination.
 * @param string $total_pages amount of pages for pagination.
 * @param bool $page whether its paged or not.
 * @param bool $search_form whether the search form is enabled.
 * @param bool|string $template name if selected - false if default.
 * @param array $user_data contains found users.
 * @return void
 */
function wpum_user_directory_pagination( $directory_id, $users_found, $total_users, $total_pages, $paged, $search_form, $template, $user_data ) {

	echo paginate_links( array(
				'base' => get_pagenum_link(1) . '%_%',
				'format' => '?paged=%#%',
				'current' => $paged,
				'total' => $total_pages,
				'prev_text' => 'Previous',
				'next_text' => 'Next'
			) 
		);

}
add_action( 'wpum_after_user_directory', 'wpum_user_directory_pagination', 10, 8 );
