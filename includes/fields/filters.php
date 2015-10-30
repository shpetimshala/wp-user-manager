<?php
/**
 * Handles filters to work with the fields input/output.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Adjust the output of the user website field to display an html anchor tag.
 * Because this field is stored into the database as a text field,
 * the default output would be just text, we use the filter within the loop,
 * to change it's output to a link.
 *
 * @param  string $value the value of field.
 * @param  string $type  field type.
 * @param  string $meta  field meta key.
 * @param  int $id    the field id number.
 * @return mixed        html output of this field.
 * @since 1.2.0
 */
function wpum_adjust_website_meta_output( $value, $type, $meta, $id ) {

	if( $meta == 'user_url' ) {
		$value = '<a href="'.esc_url( $value ).'" rel="nofollow">'. esc_url( $value ) .'</a>';
	}

	return $value;

}
add_filter( 'wpum_get_the_field_value', 'wpum_adjust_website_meta_output', 10, 4 );
