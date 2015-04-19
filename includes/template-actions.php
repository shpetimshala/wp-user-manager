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
 * @param string  $login    yes/no
 * @param string  $register yes/no
 * @param string  $password yes/no
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
 * @param array   $atts Settings of the shortcode.
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
 * @param array   $directory_args directory arguments.
 * @see
 * @return void
 */
function wpum_directory_topbar( $directory_args ) {

	get_wpum_template( "directory/top-bar.php", array(
			'users_found'  => $directory_args['users_found'],
			//'search_form'  => $directory_args['search_form'], Search form under construction
			'directory_id' => $directory_args['directory_id']
		) );

}
add_action( 'wpum_before_user_directory', 'wpum_directory_topbar' );

/**
 * Adds pagination at the bottom of the user directory.
 *
 * @since 1.0.0
 * @access public
 * @param array   $directory_args directory arguments.
 * @see
 * @return void
 */
function wpum_user_directory_pagination( $directory_args ) {

	echo '<div class="wpum-directory-pagination">';

	echo paginate_links( array(
			'base'      => get_pagenum_link( 1 ) . '%_%',
			'format'    => isset( $_GET['sort'] ) || isset( $_GET['amount'] ) ? '&paged=%#%' : '?paged=%#%',
			'current'   => $directory_args['paged'],
			'total'     => $directory_args['total_pages'],
			'prev_text' => __( 'Previous page' ),
			'next_text' => __( 'Next page' )
		)
	);

	echo '</div>';

}
add_action( 'wpum_after_user_directory', 'wpum_user_directory_pagination' );

/**
 * Adds tabs navigation on top of the account edit page.
 *
 * @since 1.0.0
 * @access public
 * @param array $atts.
 * @return void
 */
function wpum_add_account_tabs( $current_tab, $all_tabs, $form, $fields, $user_id, $atts ) {

	get_wpum_template( "account-tabs.php", array( 'tabs'  => wpum_get_account_page_tabs(), 'current_tab' => $current_tab, 'all_tabs' => $all_tabs ) );

}
add_action( 'wpum_before_account', 'wpum_add_account_tabs', 10, 6 );

/**
 * Display content of the first tab into the account page.
 *
 * @since 1.0.0
 * @access public
 * @param array $atts.
 * @return void
 */
function wpum_show_account_edit_form( $current_tab, $all_tabs, $form, $fields, $user_id, $atts ) {

	get_wpum_template( 'forms/account-form.php', 
		array(
			'atts'    => $atts,
			'form'    => $form,
			'fields'  => $fields,
			'user_id' => $user_id
		)
	);

}
add_action( 'wpum_account_tab_details', 'wpum_show_account_edit_form', 10, 6 );

/**
 * Display content of the first tab into the account page.
 *
 * @since 1.0.0
 * @access public
 * @param array $atts.
 * @return void
 */
function wpum_show_psw_update_form( $current_tab, $all_tabs, $form, $fields, $user_id, $atts ) {

	echo WPUM()->forms->get_form( 'update-password' );

}
add_action( 'wpum_account_tab_change-password', 'wpum_show_psw_update_form', 10, 6 );

/**
 * Display content of the first tab into the account page.
 *
 * @since 1.0.0
 * @access public
 * @param array $atts.
 * @return void
 */
function wpum_show_failed_login_message() {

	if( isset( $_GET['login'] ) && $_GET['login'] == 'failed' ) {
		$args = array( 
				'id'   => 'wpum-login-failed', 
				'type' => 'error', 
				'text' => __( 'Login failed: You have entered incorrect login details, please try again.' )
		);
		$warning = wpum_message( $args, true );
	}

}
add_action( 'wpum_before_loginform_template', 'wpum_show_failed_login_message' );
