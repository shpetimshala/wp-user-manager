<?php
/**
 * WP User Manager Forms: Profile Edit Form
 *
 * @package     wp-user-manager
 * @author      Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_Form_Password Class
 *
 * @since 1.0.0
 */
class WPUM_Form_Profile extends WPUM_Form {

	public static $form_name = 'profile';
	private static $user;

	/**
	 * Init the form.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function init() {

		add_action( 'wp', array( __CLASS__, 'process' ) );



		// Set values to the fields
		if(!is_admin()) :
			self::$user = wp_get_current_user();
			add_filter( 'wpum_profile_field_value', array( __CLASS__, 'set_fields_values' ), 10, 3 );
			add_filter( 'wpum_profile_field_options', array( __CLASS__, 'set_fields_options' ), 10, 3 );
		endif;

	}

	/**
	 * Builds a list of all the profile fields sorted
	 * through the settings panel.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return $fields_list array of all the fields.
	 */
	protected static function get_sorted_profile_fields() {

		$fields_list = array();

		// Grab default fields list
		$default_fields = wpum_default_user_fields_list();
		
		// Get the sorted list from the settings panel
		$saved_order = get_option( 'wpum_default_fields' );

		// Merge them together
		if( $saved_order ) {
            foreach ($saved_order as $field) {
                $default_fields[ $field['meta'] ]['order'] = $field['order'];
                $default_fields[ $field['meta'] ]['required'] = $field['required'];
                $default_fields[ $field['meta'] ]['show_on_signup'] = $field['show_on_signup'];
            }
        }

		// Sort all together
        uasort( $default_fields, 'wpum_sort_default_fields_table');

        // Build new list
        foreach ($default_fields as $new_field) {

	        $fields_list[ $new_field['meta'] ] = array(
				'label'       => $new_field['title'],
				'type'        => $new_field['type'],
				'required'    => $new_field['required'],
				'placeholder' => apply_filters( 'wpum_profile_field_placeholder', null, $new_field ),
				'options'     => apply_filters( 'wpum_profile_field_options', null, $new_field ),
				'value'       => apply_filters( 'wpum_profile_field_value', null, $new_field ),
				'priority'    => $new_field['order']
			);

        }

        // Set the password field as non required on profile page
        $fields_list[ 'password' ]['required'] = false;

        // The username cannot be changed, let's remove that field since it's useless
		unset($fields_list['username']);

		return $fields_list;

	}

	/**
	 * Setup field values on the frontend based on the user
	 *
	 * @access public
	 * @since 1.0.0
	 * @return $value value of the field.
	 */
	public static function set_fields_values( $default, $new_field ) {

		$value = null;

		switch ($new_field['meta']) {
			case 'first_name':
				$value = self::$user->user_firstname;
				break;
			case 'last_name':
				$value = self::$user->user_lastname;
				break;
			case 'nickname':
				$value = self::$user->user_nicename;
				break;
			case 'user_email':
				$value = self::$user->user_email;
				break;
			case 'user_url':
				$value = self::$user->user_url;
				break;
			case 'description':
				$value = self::$user->description;
				break;
			case 'display_name':
				$value = self::get_selected_name();
				break;
			default:
				$value = null;
				break;
		}

		return $value;

	}

	/**
	 * Setup field options on the frontend based on the user
	 *
	 * @access public
	 * @since 1.0.0
	 * @return $value value of the field.
	 */
	public static function set_fields_options( $default, $new_field ) {

		$options = array();

		switch ($new_field['meta']) {
			case 'display_name':
				$options = self::get_display_name_options( self::$user );
				break;
			default:
				$options = array();
				break;
		}

		return $options;

	}

	/**
	 * Returns the options for the "display_name" field on the profile form.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return $options list of the available options
	 */
	public static function get_display_name_options( $user ) {

		$options = array();

		// Generate the options
		$public_display = array();
		$public_display['display_username']  = $user->user_login;
		$public_display['display_nickname']  = $user->nickname;

		if ( !empty($user->first_name) )
			$public_display['display_firstname'] = $user->first_name;
		if ( !empty($user->last_name) )
			$public_display['display_lastname'] = $user->last_name;
		if ( !empty($user->first_name) && !empty($user->last_name) ) {
			$public_display['display_firstlast'] = $user->first_name . ' ' . $user->last_name;
			$public_display['display_lastfirst'] = $user->last_name . ' ' . $user->first_name;
		}
		if ( !in_array( $user->display_name, $public_display ) ) // Only add this if it isn't duplicated elsewhere
			$public_display = array( 'display_displayname' => $user->display_name ) + $public_display;
		$public_display = array_map( 'trim', $public_display );
		$public_display = array_unique( $public_display );

		// Add options to original array
		foreach ( $public_display as $id => $item ) {
			$options += array($id => $item);
		}

		return $options;

	}

	/**
	 * Returns the correct default selected option based 
	 * on what display_name the user has chosen.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return $options list of the available options
	 */
	public static function get_selected_name() {

		$selected_name = self::$user->display_name;
		$user_login    = self::$user->user_login;
		$nickname      = self::$user->nickname;
		$first_name    = self::$user->first_name;
		$last_name     = self::$user->last_name;
		$firstlast     = self::$user->first_name . ' ' . self::$user->last_name;
		$lastfirst     = self::$user->last_name . ' ' . self::$user->first_name;

		$selected_value = $user_login;

		switch ($selected_name) {
			case $nickname:
				$selected_value = 'display_nickname';
				break;
			case $first_name:
				$selected_value = 'display_firstname';
				break;
			case $last_name:
				$selected_value = 'display_lastname';
				break;
			case $firstlast:
				$selected_value = 'display_firstlast';
				break;
			case $lastfirst:
				$selected_value = 'display_lastfirst';
				break;
			default:
				$selected_value = $user_login;
				break;
		}

		return $selected_value;

	}

	/**
	 * Define profile fields
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function get_profile_fields() {

		if ( self::$fields ) {
			return;
		}

		self::$fields = apply_filters( 'wpum_profile_fields', array(
			'profile' => self::get_sorted_profile_fields()
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
		self::get_profile_fields();

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
	 * Get the value of a posted multiselect field
	 * @param  string $key
	 * @param  array $field
	 * @return array
	 */
	protected static function get_posted_multiselect_field( $key, $field ) {
		return isset( $_POST[ $key ] ) ? array_map( 'sanitize_text_field', $_POST[ $key ] ) : array();
	}

	/**
	 * Get the value of a posted textarea field
	 * @param  string $key
	 * @param  array $field
	 * @return string
	 */
	protected static function get_posted_textarea_field( $key, $field ) {
		return isset( $_POST[ $key ] ) ? wp_kses_post( trim( stripslashes( $_POST[ $key ] ) ) ) : '';
	}

	/**
	 * Get the value of a posted textarea field
	 * @param  string $key
	 * @param  array $field
	 * @return string
	 */
	protected static function get_posted_wp_editor_field( $key, $field ) {
		return self::get_posted_textarea_field( $key, $field );
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

		return apply_filters( 'wpum_profile_validate_fields', true, self::$fields, $values );

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
		self::get_profile_fields();

		// Get posted values
		$values = self::get_posted_fields();

		if ( empty( $_POST['wpum_submit_form'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'profile' ) ) {
			return;
		}

		// Validate required
		if ( is_wp_error( ( $return = self::validate_fields( $values ) ) ) ) {
			self::add_error( $return->get_error_message() );
			return;
		}

		// Update the profile
		self::update_profile( $values );

	}

	/**
	 * Trigger update process.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function update_profile( $values ) {

		if( empty($values) || !is_array($values) )
			return;

		$user_data = array( 'ID' => self::$user->ID );

		foreach ( $values['profile'] as $meta_key => $meta_value ) {
			switch ( $meta_key ) {
				case 'password':
					// do nothing now
				break;
				case 'user_email':
					if(is_email( $meta_value )) :
						$user_data += array( 'user_email' => $meta_value );
					else :
						self::add_error( __('Please enter a valid email address.') );
						return;
					endif;
				break;
				case 'display_name':
					$user_data += array( 'display_name' => self::store_display_name( $values['profile'], $meta_value ) );
				break;
				case 'nickname':
					$user_data += array( 'user_nicename' => $meta_value );
					$user_data += array( 'nickname' => $meta_value );
				break;
				
				default:
					$user_data += array( $meta_key => $meta_value );
					break;
			}
		}

		do_action()

		$user_id = wp_update_user( $user_data );
		if ( is_wp_error( $user_id ) ) {

			$this_page = add_query_arg( array('updated' => 'error'), get_permalink() );
			wp_redirect( $this_page );
			exit();

		} else {

			$this_page = add_query_arg( array('updated' => 'success'), get_permalink() );
			wp_redirect( $this_page );
			exit();

		}
		

	}

	/**
	 * Decides which option should be stored into the database.
	 * This avoids the "display_name" option into the profile form to
	 * save the select field option value into the database.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function store_display_name( $values, $meta_value ) {

		$name = self::$user->user_login;

		switch ($meta_value) {
			case 'display_nickname':
				$name = $values['nickname'];
				break;
			case 'display_firstname':
				$name = $values['first_name'];
				break;
			case 'display_lastname':
				$name = $values['last_name'];
				break;
			case 'display_firstlast':
				$name = $values['first_name'] . ' ' . $values['last_name'];
				break;
			case 'display_lastfirst':
				$name = $values['last_name'] . ' ' . $values['first_name'];
				break;
			
			default:
				$name = self::$user->user_login;
				break;
		}

		return $name;

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
		self::get_profile_fields();

		// Show errors from fields
		self::show_errors();

		// Show confirmation messages
		self::show_confirmations();

		// Display template
		if( is_user_logged_in() ) :

			get_wpum_template( 'profile-form.php', 
				array(
					'args' => $atts,
					'form' => self::$form_name,
					'fields' => self::get_fields( 'profile' )
				)
			);

		// Show login form if not logged in
		else :
			
			echo do_shortcode( '[wpum_login_form redirect="'.get_permalink().'"]' );

		endif;


	}

}



