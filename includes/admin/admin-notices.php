<?php
/**
 * Admin Messages
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Admin Messages
 *
 * @since 1.0
 * @global $wpum_options Array of all the WPUM Options
 * @return void
 */
function wpum_admin_messages() {
	global $wpum_options;

	if (  isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true && !wpum_get_option('custom_passwords') && wpum_get_option('password_strength') ) {
		add_settings_error( 'wpum-notices', 'custom-passwords-disabled', __( 'You have enabled the "Minimum Password Strength" option, the "Users custom passwords" is currently disabled and must be enabled for custom passwords to work.', 'wpum' ), 'error' );
	}

	settings_errors( 'wpum-notices' );
}
add_action( 'admin_notices', 'wpum_admin_messages' );