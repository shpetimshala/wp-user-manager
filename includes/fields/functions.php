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

/**
 * Get the list of registration fields formatted into an array.
 * The format of the array is used by the forms.
 *
 * @since 1.0.0
 * @return array - list of fields.
 */
function wpum_get_registration_fields() {

	// Get fields from the database
	$primary_group = WPUM()->field_groups->get_group_by('primary');

	$args = array(
		'id'           => $primary_group->id,
		'array'        => true,
		'registration' => true,
		'number'       => -1,
		'orderby'      => 'field_order',
		'order'        => 'ASC'
	);

	$data = WPUM()->fields->get_by_group( $args );

	// Manipulate fields list into a list formatted for the forms API.
	$fields = array();
	
	// Loop through the found fields
	foreach ( $data as $key => $field ) {
		
		// Adjust field type parameter if no field type template is defined.
		switch ( $field['type'] ) {
			case 'username':
				$field['type'] = 'text';
				break;
			case 'avatar':
				$field['type'] = 'file';
				break;
		}

		$fields[ $field['meta'] ] = array(
			'priority'       => $field['field_order'],
			'label'          => $field['name'],
			'type'           => $field['type'],
			'meta'           => $field['meta'],
			'required'       => $field['is_required'],
		);

	}

	// Remove password field if not enabled
    if( ! wpum_get_option('custom_passwords') )
    	unset( $fields['password'] );

    // Remove the user avatar field if not enabled
	if( ! wpum_get_option('custom_avatars') )
		unset( $fields['user_avatar'] );

	return apply_filters( 'wpum_get_registration_fields', $fields );

}
