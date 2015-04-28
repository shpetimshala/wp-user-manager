<?php
/**
 * Uninstall WPUM
 *
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

// Delete options
delete_option( 'wpum_settings' );
delete_option( 'wpum_emails' );
delete_option( 'wpum_permalink' );
delete_option( 'wpum_custom_fields' );

// Delete post type contents
$wpum_post_types = array( 'wpum_directory' );

foreach ( $wpum_post_types as $post_type ) {
	$items = get_posts( array( 'post_type' => $post_type, 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) );
	if ( $items ) {
		foreach ( $items as $item ) {
			wp_delete_post( $item, true);
		}
	}
}