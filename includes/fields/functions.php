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
			'priority'    => $field['field_order'],
			'label'       => $field['name'],
			'type'        => $field['type'],
			'meta'        => $field['meta'],
			'required'    => $field['is_required'],
			'description' => $field['description'],
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

/**
 * Get the list of account fields formatted into an array.
 * The format of the array is used by the forms.
 *
 * @since 1.0.0
 * @return array - list of fields.
 */
function wpum_get_account_fields() {

	// Get fields from the database
	$primary_group = WPUM()->field_groups->get_group_by('primary');

	$args = array(
		'id'           => $primary_group->id,
		'array'        => true,
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
			case 'nickname':
				$field['type'] = 'text';
				break;
			case 'display_name':
				$field['type'] = 'select';
				break;
			case 'avatar':
				$field['type'] = 'file';
				break;
		}

		$fields[ $field['meta'] ] = array(
			'priority'    => $field['field_order'],
			'label'       => $field['name'],
			'type'        => $field['type'],
			'meta'        => $field['meta'],
			'required'    => $field['is_required'],
			'description' => $field['description'],
			'placeholder' => apply_filters( 'wpum_profile_field_placeholder', null, $field ),
			'options'     => apply_filters( 'wpum_profile_field_options', null, $field ),
			'value'       => apply_filters( 'wpum_profile_field_value', null, $field )
		);

	}

	// Remove password field from here
	unset( $fields['password'] );

	// The username cannot be changed, let's remove that field since it's useless
	unset( $fields['username'] );

	// Remove the user avatar field if not enabled
	if( ! wpum_get_option( 'custom_avatars' ) )
		unset( $fields['user_avatar'] );

	return apply_filters( 'wpum_get_account_fields', $fields );

}

/**
 * Displays the html of a field within a form.
 *
 * @since 1.0.0
 * @return mixed
 */
function wpum_get_field_input_html( $key, $field ) {

	if( wpum_field_type_exists( $field['type'] ) ) {

		$object = wpum_get_field_type_object( $field['type'] );

		if ( method_exists( $object->class, "input_html" ) ) {
			echo call_user_func( $object->class . "::input_html", $key, $field );
		} else {
			get_wpum_template( 'form-fields/' . $field['type'] . '-field.php', array( 'key' => $key, 'field' => $field ) );
		}

	} else {
		echo __( 'This field type has no output', 'wpum' );
	}

}

/**
 * Get value of a user custom field given the user id and field meta key.
 *
 * @since 1.2.0
 * @param  string $user_id    the id number of the user.
 * @param  string $field_meta the metakey of the field
 * @return mixed
 */
function wpum_get_field_value( $user_id, $field_meta ) {

	$field_data = false;

	if( empty( $user_id ) || ! is_int( $user_id ) ) {
		return false;
	}

	switch ( $field_meta ) {
		case 'user_email':
			$field_data = wpum_get_user_email( $user_id );
			break;
		case 'username':
			$field_data = wpum_get_user_username( $user_id );
			break;
		case 'display_name':
			$field_data = wpum_get_user_displayname( $user_id );
			break;
		case 'user_url':
			$field_data = wpum_get_user_website( $user_id );
			break;
		default:
			$field_data = get_user_meta( $user_id, $field_meta, $single = true );
			break;
	}

	return maybe_unserialize( $field_data );

}

/**
 * Retrieve field groups, populated with fields and associated user data.
 *
 * @since 1.2.0
 * @param  array $args arguments for the query.
 * @return array       list of groups and fields with associated data.
 */
function wpum_get_field_groups( $args = array() ) {

	if( $args['field_group_id'] && is_int( $args['field_group_id'] ) ) {
		$groups = array();
		$groups[] = WPUM()->field_groups->get_group_by( 'id', absint( $args['field_group_id'] ), true );
	} else {
		$groups = WPUM()->field_groups->get_groups( $args );
	}

	// Merge fields for each group
	if( ! empty( $groups ) ) {
		foreach ( $groups as $key => $group ) {

			$fields = WPUM()->fields->get_by_group( array(
				'id'      => absint( $group['id'] ),
				'orderby' => 'field_order',
				'order'   => 'ASC',
				'array'   => true
			) );

			if( empty( $fields ) && $args['hide_empty_groups'] === true ) {
				unset( $groups[ $key ] );
			} else {

				foreach ( $fields as $field_key => $field ) {

					if( $field['meta'] == 'password' || $field['meta'] == 'user_avatar' ) {
						unset( $fields[ $field_key ] );
					} else {
						$fields[ $field_key ]['value'] = wpum_get_field_value( $args['user_id'], $field['meta'] );
						$fields[ $field_key ] = wpum_array_to_object( $fields[ $field_key ] );
					}

				}

				$fields = array_values( $fields );

				$groups[ $key ][ 'fields' ] = $fields;

			}

		}
	}

	return apply_filters( 'wpum_get_field_groups', $groups, $args );

}

/**
 * Use this function to start a loop of profile fields.
 *
 * @since 1.2.0
 * @global $wpum_profile_fields
 * @param  string  $args arguments to create the loop.
 * @return boolean       whether there's any group found.
 */
function wpum_has_profile_fields( $args = '' ) {

	global $wpum_profile_fields;

	$defaults = array(
		'user_id'           => 1,
		'field_group_id'    => false,
		'number'            => false,
		'hide_empty_groups' => true,
		'hide_empty_fields' => false,
		'exclude_groups'    => false,
		'exclude_fields'    => false,
	);

	// Parse incoming $args into an array and merge it with $defaults.
	$args = wp_parse_args( $args, $defaults );

	$wpum_profile_fields = new WPUM_Fields_Data_Template( $args );

	return apply_filters( 'wpum_has_profile_fields', $wpum_profile_fields->has_groups(), $wpum_profile_fields );

}

/**
 * Setup the profile fields loop.
 *
 * @since 1.2.0
 * @global $wpum_profile_fields
 * @return bool
 */
function wpum_profile_field_groups() {

	global $wpum_profile_fields;
	return $wpum_profile_fields->profile_groups();

}

/**
 * Setup the current field group within the loop.
 *
 * @since 1.2.0
 * @global $wpum_profile_fields
 * @return array the current group within the loop.
 */
function wpum_the_profile_field_group() {

	global $wpum_profile_fields;
	return $wpum_profile_fields->the_profile_group();

}

/**
 * Return the group id number of a group within the loop.
 *
 * @since 1.2.0
 * @global $wpum_fields_group
 * @return string the current group id.
 */
function wpum_get_field_group_id() {

	global $wpum_fields_group;
	return apply_filters( 'wpum_get_field_group_id', $wpum_fields_group['id'] );

}

/**
 * Echo the group id number of a group within the loop.
 *
 * @since 1.2.0
 * @return void
 */
function wpum_the_field_group_id() {
	echo wpum_get_field_group_id();
}

/**
 * Return the name of a group within the loop.
 *
 * @since 1.2.0
 * @global $wpum_fields_group
 * @return string
 */
function wpum_get_field_group_name() {

	global $wpum_fields_group;
	return apply_filters( 'wpum_get_field_group_name', $wpum_fields_group['name'] );

}

/**
 * Echo the name of a group within the loop.
 *
 * @since 1.2.0
 * @return void
 */
function wpum_the_field_group_name() {
	echo wpum_get_field_group_name();
}

/**
 * Return the slug of a group within the loop.
 *
 * @since 1.2.0
 * @global $wpum_fields_group
 * @return string
 */
function wpum_get_field_group_slug() {

	global $wpum_fields_group;
	return apply_filters( 'wpum_get_field_group_slug', sanitize_title( $wpum_fields_group['name'] ) );

}

/**
 * Echo the slug of a group within the loop.
 *
 * @since 1.2.0
 * @return void
 */
function wpum_the_field_group_slug() {
	echo wpum_get_field_group_slug();
}

/**
 * Retrieve the description of the group within the loop.
 *
 * @since 1.2.0
 * @return string
 */
function wpum_get_field_group_description() {

	global $wpum_fields_group;
	return apply_filters( 'wpum_get_field_group_description', $wpum_fields_group['description'] );

}

/**
 * Echo the description of a field group within the loop.
 *
 * @since 1.2.0
 * @return void
 */
function wpum_the_field_group_description() {
	echo wpum_get_field_group_description();
}

/**
 * Whether the current group within the loop has fields.
 *
 * @since 1.2.0
 * @global $wpum_profile_fields
 * @return array the current group fields within the loop.
 */
function wpum_field_group_has_fields() {

	global $wpum_profile_fields;
	return $wpum_profile_fields->has_fields();

}
