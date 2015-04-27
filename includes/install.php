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
 * flushing rewrite rules and also populates the settings fields.
 * After successful install, the user is redirected to the WPUM Welcome screen.
 *
 * @since 1.0
 * @global $wpum_options
 * @global $wp_version
 * @return void
 */
function wpum_install() {

	global $wpum_options, $wp_version;

	// Check PHP Version and deactivate & die if it doesn't meet minimum requirements.
	if ( version_compare(PHP_VERSION, '5.3', '<') ) {
		deactivate_plugins( plugin_basename( WPUM_PLUGIN_FILE ) );
		wp_die( sprintf( __( 'This plugin requires a minimum PHP Version 5.3 to be installed on your host. <a href="%s" target="_blank">Click here to read how you can update your PHP version</a>.'), 'http://www.wpupdatephp.com/contact-host/' ) . '<br/><br/>' . '<small><a href="'.admin_url().'">'.__('Back to your website.').'</a></small>' );
	}

	// Clear the permalinks
	flush_rewrite_rules( true );

	// Add Upgraded From Option
	$current_version = get_option( 'wpum_version' );
	if ( $current_version ) {
		update_option( 'wpum_version_upgraded_from', $current_version );
	}

	// Update current version
	update_option( 'wpum_version', WPUM_VERSION );

	// Add the transient to redirect
	set_transient( '_wpum_activation_redirect', true, 30 );

}
register_activation_hook( WPUM_PLUGIN_FILE, 'wpum_install' );