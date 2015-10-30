<?php
/**
 * Collection of functions that should be used only within the admin panel.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Get all stored options of a custom field.
 *
 * @param  int $field_id the id number of the field.
 * @return array           list of the options.
 * @since 1.2.0
 */
function wpum_get_field_options( $field_id ) {

	$options = array();

	$field_options = WPUM()->fields->get_column_by( 'options', 'id', $field_id );
	$field_options = maybe_unserialize( $field_options );

	if ( is_array( $field_options ) ) {
		$options = $field_options; }

	return $options;

}

/**
 * Helper function to update an option of a field.
 *
 * @param  int    $field_id the field id number.
 * @param  string $option   the option that needs to be updated.
 * @param  string $value    the new value of the option.
 * @return mixed
 * @since 1.2.0
 */
function wpum_update_field_option( $field_id, $option, $value ) {

	$all_options = wpum_get_field_options( $field_id );

	$option = trim( $option );

	if ( empty( $option ) ) {
		return false; }

	// Sanitize the value being saved.
	$value = is_array( $value ) ? $value : sanitize_text_field( $value );
	$value = maybe_serialize( $value );

	if ( is_array( $all_options ) ) {
		$all_options[ $option ] = $value;
		WPUM()->fields->update( $field_id, array( 'options' => maybe_serialize( $all_options ) ) );
	}

}

/**
 * Retrieve a single field option from the database.
 *
 * @param  int    $field_id the id of the field.
 * @param  string $option   the name of the option to retrieve.
 * @return mixed           the option value.
 * @since 1.2.0
 */
function wpum_get_field_option( $field_id, $option ) {

	$option_value = false;
	$option = trim( $option );

	if ( empty( $option ) || empty( $field_id ) ) {
				return false; }

	$all_options = wpum_get_field_options( $field_id );

	if ( array_key_exists( $option, $all_options ) ) {
		$option_value = maybe_unserialize( $all_options[ $option ] );
	}

	return $option_value;

}
