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

	settings_errors( 'wpum-notices' );
}
add_action( 'admin_notices', 'wpum_admin_messages' );