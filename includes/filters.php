<?php
/**
 * Plugin Filters
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add Settings Link To WP-Plugin Page
 * 
 * @since 1.0.0
 * @access public
 * @return array
 */
function wpum_add_settings_link( $links ) {
	$settings_link = '<a href="'.admin_url( 'users.php?page=wpum-settings' ).'">'.__('Settings','wpum').'</a>';
	array_push( $links, $settings_link );
	return $links;
}
add_filter( "plugin_action_links_".WPUM_SLUG , 'wpum_add_settings_link');

/**
 * Add links to plugin row
 * 
 * @since 1.0.0
 * @access public
 * @return array
 */
function wpum_plugin_row_meta( $input, $file ) {
	
	if ( $file != 'wp-user-manager/wp-user-manager.php' )
		return $input;

	$links = array(
		'<a href="http://support.wp-user-manager.com" target="_blank">' . esc_html__( 'Documentation', 'wpum' ) . '</a>',
		'<a href="http://wp-user-manager.com/addons/" target="_blank">' . esc_html__( 'Add Ons', 'wpum' ) . '</a>',
	);

	$input = array_merge( $input, $links );

	return $input;
}
add_filter( 'plugin_row_meta', 'wpum_plugin_row_meta', 10, 2 );