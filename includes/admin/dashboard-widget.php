<?php
/**
 * User overview widget for admin dashboard.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register the dashboard widgets.
 *
 * @since 1.1.0
 * @return void
 */
function wpum_register_dashboard_widgets() {

  if( current_user_can( apply_filters( 'wpum_stats_cap', 'manage_options' ) ) ) {
    wp_add_dashboard_widget( 'wpum_dashboard_users', __( 'WP User Manager Users Overview' ), 'wpum_dashboard_users_overview' );
  }

}
add_action( 'wp_dashboard_setup', 'wpum_register_dashboard_widgets', 10 );

/**
 * Build and render the users overview widget.
 *
 * @since 1.1.0
 * @return void
 */
function wpum_dashboard_users_overview() {

  echo "hey";

}
