<?php
/**
 * Handles the function to work with the fields.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Gets list of registered field types.
 *
 * @since 1.0.0
 * @return array $field_types - list of field types.
 */
function wpum_get_field_types() {

	return apply_filters( 'wpum/get_field_types', array() );

}