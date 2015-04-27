<?php
/**
 * Installation Functions
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Install
 *
 * Runs on plugin install by setting up the post types,
 * flushing rewrite rules and also creates the plugin and 
 * populates the settings fields.
 * 
 * After successful install, the user is redirected to the WPUM Welcome
 * screen.
 *
 * @since 1.0
 * @global $edd_options
 * @global $wp_version
 * @return void
 */
function wpum_install() {

	global $wpum_options, $wp_version;

}
register_activation_hook( WPUM_PLUGIN_FILE, 'edd_install' );