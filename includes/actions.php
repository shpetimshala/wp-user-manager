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
 * @since    1.0.0
 */
function wpum_add_nonce_to_login_form() {
	return wp_nonce_field( "wpum_nonce_login_form", "wpum_nonce_login_security" );
}
add_action( 'login_form_bottom', 'wpum_add_nonce_to_login_form' );