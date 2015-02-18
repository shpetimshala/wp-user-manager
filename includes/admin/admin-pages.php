<?php
/**
 * Admin Pages handler
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Creates the admin submenu pages under the Users menu and assigns their
 * links to global variables
 *
 * @since 1.0.0
 * @global $wpum_settings_page
 * @return void
 */
function wpum_add_options_link() {

	global $wpum_settings_page;
	
	$wpum_settings_page = add_users_page( __('WP User Manager Settings'), __('WPUM Settings'), 'manage_options', 'wpum-settings', 'wpum_options_page');

}
add_action( 'admin_menu', 'wpum_add_options_link', 10 );