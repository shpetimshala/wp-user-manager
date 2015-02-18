<?php
/**
 * Admin Actions
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display links next to title in settings panel
 *
 * @since 1.0.0
 * @return array
*/
function wpum_add_links_to_settings_title() {
	echo '<a href="http://support.wp-user-manager.com" class="add-new-h2" target="_blank">'.__('Documentation').'</a>';
	echo '<a href="http://wp-user-manager.com/addons" class="add-new-h2" target="_blank">'.__('Add Ons').'</a>';
}
add_action('wpum_next_to_settings_title','wpum_add_links_to_settings_title');

/**
 * Function to display content of the "registration_status" option.
 *
 * @since 1.0.0
 * @return array
*/
function wpum_option_registration_status() {

	$output = null;

	if( get_option( 'users_can_register' ) ) {
		$output = '<div class="wpum-admin-message">'.sprintf( __( '<strong>Enabled.</strong> <br/> <small>Registrations can be disabled in <a href="%s" target="_blank">Settings -> General</a>.</small>', 'wpum' ), admin_url( 'options-general.php#users_can_register' ) ).'</div>';
	} else {
		$output = '<div class="wpum-admin-message">'.sprintf( __( 'Registrations are disabled. Enable the "Membership" option in <a href="%s" target="_blank">Settings -> General</a>.', 'wpum' ), admin_url( 'options-general.php#users_can_register' ) ).'</div>';
	}

	echo $output;

}
add_action('wpum_registration_status','wpum_option_registration_status');