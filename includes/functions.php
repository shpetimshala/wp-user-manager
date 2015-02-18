<?php
/**
 * Main Functions
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'wpum_get_login_methods' ) ) :
/**
 * Define login methods for options panel
 *
 * @access public
 * @return array
 */
function wpum_get_login_methods() {
	return apply_filters( 'wpum_get_login_methods', array(
		'username'       => __( 'Username only' ),
		'email'          => __( 'Email only' ),
		'username_email' => __( 'Username and Email' ),
	) );
}
endif;

if ( ! function_exists( 'wpum_get_psw_lenghts' ) ) :
/**
 * Define login methods for options panel
 *
 * @access public
 * @return array
 */
function wpum_get_psw_lenghts() {
	return apply_filters( 'wpum_get_psw_lenghts', array(
		''       => __( 'Disabled' ),
		'weak'   => __( 'Weak' ),
		'medium' => __( 'Medium' ),
		'strong' => __( 'Strong' ),
	) );
}
endif;