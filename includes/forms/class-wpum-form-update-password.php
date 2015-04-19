<?php
/**
 * WP User Manager Forms: update password form
 * This form is used into the account page when a user is already logged in.
 * 
 * @package     wp-user-manager
 * @author      Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_Form_Update_Password Class
 *
 * @since 1.0.0
 */
class WPUM_Form_Update_Password extends WPUM_Form {

	public static $form_name = 'update-password';

	/**
	 * Init the form.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function init() {

		add_action( 'wp', array( __CLASS__, 'process' ) );

	}

	/**
	 * Define password update form fields
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function get_update_password_fields() {

		self::$fields = apply_filters( 'wpum_password_update_fields', array(
			'password_update' => array(
				'password' => array(
					'label'       => __( 'Password' ),
					'type'        => 'password',
					'required'    => false,
					'placeholder' => '',
					'priority'    => 1
				),
				'password_repeat' => array(
					'label'       => __( 'Repeat Password' ),
					'type'        => 'password',
					'required'    => false,
					'placeholder' => '',
					'priority'    => 2
				),
			),
		) );

	}

	/**
	 * Get submitted fields values.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return $values array of data from the fields.
	 */
	protected static function get_posted_fields() {

		// Get fields
		self::get_update_password_fields();

		$values = array();

		foreach ( self::$fields as $group_key => $group_fields ) {
			foreach ( $group_fields as $key => $field ) {
				// Get the value
				$field_type = str_replace( '-', '_', $field['type'] );

				if ( method_exists( __CLASS__, "get_posted_{$field_type}_field" ) ) {
					$values[ $group_key ][ $key ] = call_user_func( __CLASS__ . "::get_posted_{$field_type}_field", $key, $field );
				} else {
					$values[ $group_key ][ $key ] = self::get_posted_field( $key, $field );
				}

				// Set fields value
				self::$fields[ $group_key ][ $key ]['value'] = $values[ $group_key ][ $key ];
			}
		}

		return $values;
	}

	/**
	 * Goes through fields and sanitizes them.
	 *
	 * @access public
	 * @param array|string $value The array or string to be sanitized.
	 * @since 1.0.0
	 * @return array|string $value The sanitized array (or string from the callback)
	 */
	public static function sanitize_posted_field( $value ) {
		// Decode URLs
		if ( is_string( $value ) && ( strstr( $value, 'http:' ) || strstr( $value, 'https:' ) ) ) {
			$value = urldecode( $value );
		}

		// Santize value
		$value = is_array( $value ) ? array_map( array( __CLASS__, 'sanitize_posted_field' ), $value ) : sanitize_text_field( stripslashes( trim( $value ) ) );

		return $value;
	}

	/**
	 * Get the value of submitted fields.
	 *
	 * @access protected
	 * @param  string $key
	 * @param  array $field
	 * @since 1.0.0
	 * @return array|string content of the submitted field
	 */
	protected static function get_posted_field( $key, $field ) {
		return isset( $_POST[ $key ] ) ? self::sanitize_posted_field( $_POST[ $key ] ) : '';
	}

	/**
	 * Validate the posted fields
	 *
	 * @return bool on success, WP_ERROR on failure
	 */
	protected static function validate_fields( $values ) {

		foreach ( self::$fields as $group_key => $group_fields ) {
			foreach ( $group_fields as $key => $field ) {
				if ( $field['required'] && empty( $values[ $group_key ][ $key ] ) ) {
					return new WP_Error( 'validation-error', sprintf( __( '%s is a required field' ), $field['label'] ) );
				}
			}
		}

		return apply_filters( 'wpum_password_form_validate_fields', true, self::$fields, $values );

	}

	/**
	 * Process the submission.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function process() {

		// Get fields
		self::get_update_password_fields();

		// Get posted values
		$values = self::get_posted_fields();

	}

	/**
	 * Output the form.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function output( $atts = array() ) {
		
		// Get fields
		self::get_update_password_fields();

		// Show errors from fields
		self::show_errors();

		// Show confirmation messages
		self::show_confirmations();

		// Display template
		if( is_user_logged_in() ) :

			get_wpum_template( 'forms/password-update-form.php', 
				array(
					'form'            => self::$form_name,
					'password_fields' => self::get_fields( 'password_update' ),
				)
			);

		// Show psw form if not logged in
		else :
			

		endif;

	}

}