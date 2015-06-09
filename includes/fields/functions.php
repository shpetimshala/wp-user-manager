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

	return apply_filters( 'wpum/field/types', array() );

}

/**
 * Gets list of registered field classes.
 *
 * @since 1.0.0
 * @return array $field_classes - list of field types and class names.
 */
function wpum_get_field_classes() {

	return apply_filters( 'wpum/field/types/classes', array() );

}

/**
 * Verify if a field type exists
 *
 * @since 1.0.0
 * @return bool - true | false.
 */
function wpum_field_type_exists( $type = '' ) {

	$exists = false;

	$all_types = wpum_get_field_classes();

	if( array_key_exists( $type , $all_types ) )
		$exists = true;

	return $exists;

}

/**
 * Get the class of a field type and returns the object.
 *
 * @since 1.0.0
 * @param  $type type of field
 * @return object - class.
 */
function wpum_get_field_type_object( $type = '' ) {

	$object = null;

	$field_types = wpum_get_field_classes();

	if( !empty( $type ) && wpum_field_type_exists( $type ) ) {
		$class = $field_types[ $type ];
		$object = new $class;
	}

	return $object;
}

/**
 * Get the options of a field type
 *
 * @since 1.0.0
 * @param  $type type of field
 * @return array - list of options.
 */
function wpum_get_field_options( $type = '' ) {

	$options = array();
	$field_types = wpum_get_field_classes();

	if( !empty( $type ) && wpum_field_type_exists( $type ) ) {
		$class = $field_types[ $type ];
		$options = call_user_func( "$class::options" );
	}

	return $options;

}
