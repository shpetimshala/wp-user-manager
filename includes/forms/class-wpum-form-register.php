<?php
/**
 * WP User Manager Forms
 *
 * @package     wp-user-manager
 * @author      Mike Jolley
 * @author      Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_Form_Register Class
 *
 * @since 1.0.0
 */
class WPUM_Form_Register extends WPUM_Form {

	public static $form_name = 'register';
	public static $random_password = true;

	/**
	 * Init the form.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function init() {

		add_action( 'wp', array( __CLASS__, 'process' ) );

		// Check for password field
		if(wpum_get_option('custom_passwords')) :
			
			self::$random_password = false;
			add_filter( 'wpum_default_registration_fields', array( __CLASS__, 'add_password_field' ) );
			add_filter( 'wpum_register_form_validate_fields', array( __CLASS__, 'validate_password_field' ), 10, 3 );

			// Add password meter field
			if( wpum_get_option('display_password_meter_registration') )
				add_action( 'wpum_after_inside_register_form_template', array( __CLASS__, 'add_password_meter_field' ) );

			// Automatic login after registration
			if( wpum_get_option('login_after_registration') )
				add_action( 'wpum_registration_is_complete', array( __CLASS__, 'do_login' ), 10, 3 );

		endif;

		// Add honeypot spam field
		if( wpum_get_option('enable_honeypot') ) :
			add_action( 'wpum_default_registration_fields', array( __CLASS__, 'add_honeypot_field' ) );
			add_filter( 'wpum_register_form_validate_fields', array( __CLASS__, 'validate_honeypot_field' ), 10, 3 );
		endif;

		// Add terms & conditions field
		if( wpum_get_option('enable_terms') ) :
			add_action( 'wpum_default_registration_fields', array( __CLASS__, 'add_terms_field' ) );
		endif;

	}

	/**
	 * Define registration fields
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function get_registration_fields() {

		if ( self::$fields ) {
			return;
		}

		self::$fields = apply_filters( 'wpum_default_registration_fields', array(
			'register' => array(
				'username' => array(
					'label'       => __( 'Username' ),
					'type'        => 'text',
					'required'    => true,
					'placeholder' => '',
					'priority'    => 1
				),
				'email' => array(
					'label'       => __( 'Email' ),
					'type'        => 'email',
					'required'    => true,
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
		self::get_registration_fields();

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

		return apply_filters( 'wpum_register_form_validate_fields', true, self::$fields, $values );

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
		self::get_registration_fields();

		// Get posted values
		$values = self::get_posted_fields();

		if ( empty( $_POST['wpum_submit_form'] ) ) {
			return;
		}

		// Validate required
		if ( is_wp_error( ( $return = self::validate_fields( $values ) ) ) ) {
			self::add_error( $return->get_error_message() );
			return;
		}

		// Let's do the registration
		self::do_registration( $values['register']['username'], $values['register']['email'], $values );

	}

	/**
	 * Add password field if option is enabled.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function add_password_field( $fields ) {

		$fields['register']['password'] = array(
		    'label' => __( 'Password' ),
		    'type' => 'password',
		    'required' => true,
		    'placeholder' => '',
		    'priority' => 3
		);
		
		return $fields;

	}

	/**
	 * Validate the password field.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function validate_password_field( $passed, $fields, $values ) {

		$pwd = $values['register']['password'];
		$pwd_strenght = wpum_get_option('password_strength');

		$containsLetter  = preg_match('/[A-Z]/', $pwd);
		$containsDigit   = preg_match('/\d/', $pwd);
		$containsSpecial = preg_match('/[^a-zA-Z\d]/', $pwd);

		if($pwd_strenght == 'weak') {
			if(strlen($pwd) < 8)
				return new WP_Error( 'password-validation-error', __( 'Password must be at least 8 characters long' ) );
		}
		if($pwd_strenght == 'medium') {
			if( !$containsLetter || !$containsDigit || strlen($pwd) < 8 )
				return new WP_Error( 'password-validation-error', __( 'Password must be at least 8 characters long and contain at least 1 number and 1 uppercase letter.' ) );
		}
		if($pwd_strenght == 'strong') {
			if( !$containsLetter || !$containsDigit || !$containsSpecial || strlen($pwd) < 8 )
				return new WP_Error( 'password-validation-error', __( 'Password must be at least 8 characters long and contain at least 1 number and 1 uppercase letter and 1 special character.' ) );
		}

		return $passed;

	}

	/**
	 * Do registration.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function do_registration( $username, $email, $values ) {

		// Try registration
		if( self::$random_password ) {
			$do_user = register_new_user($username, $email);
		} else {
			$pwd = $values['register']['password'];
			$do_user = wp_create_user( $username, $pwd, $email );
		}

		// Check for errors
		if ( is_wp_error( $do_user ) ) {
			
			foreach ($do_user->errors as $error) {
				self::add_error( $error[0] );
			}
			return;

		} else {

			// Send notification if password is manually added by the user.
			if(!self::$random_password):
				wp_new_user_notification( $do_user, $pwd );
			endif;

			self::add_confirmation( __('Registration Complete') );

			// Add ability to extend registration process.
			$user_id = $do_user;
			do_action('wpum_registration_is_complete', $user_id, $values );

		}

	}

	/**
	 * Add password meter field.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function add_password_meter_field( $atts ) {
		echo '<span id="password-strength"></span>';		
	}

	/**
	 * Add Honeypot field markup.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function add_honeypot_field( $fields ) {

		$fields['register'][ 'comments' ] = array(
		    'label' => 'Comments',
		    'type' => 'textarea',
		    'required' => false,
		    'placeholder' => '',
		    'priority' => 9999,
		    'class' => 'wpum-honeypot-field'
		);

		return $fields;

	}

	/**
	 * Validate the honeypot field.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function validate_honeypot_field( $passed, $fields, $values ) {

		$fake_field = $values['register'][ 'comments' ];

		if( $fake_field )
			return new WP_Error( 'honeypot-validation-error', __( 'Failed Honeypot validation' ) );

		return $passed;

	}

	/**
	 * Autologin.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function do_login( $user_id, $values ) {

		$userdata = get_userdata( $user_id );

		$data = array();
		$data['user_login']    = $userdata->user_login;
		$data['user_password'] = $values['register']['password'];
		$data['rememberme']    = true;

		$user_login = wp_signon( $data, false );

		wp_redirect( get_permalink() );
		exit;

	}

	/**
	 * Add Terms field.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function add_terms_field( $fields ) {

		$fields['register'][ 'terms' ] = array(
		    'label' => __('Terms &amp; Conditions'),
		    'type' => 'checkbox',
		    'description' => sprintf(__('By registering to this website you agree to the <a href="%s" target="_blank">terms &amp; conditions</a>.'), get_permalink( wpum_get_option('terms_page') ) ),
		    'required' => true,
		    'priority' => 9999,
		);

		return $fields;

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
		self::get_registration_fields();

		// Show errors from fields
		self::show_errors();

		// Show confirmation messages
		self::show_confirmations();

		// Display template
		get_wpum_template( 'default-registration-form.php', 
			array(
				'atts' => $atts,
				'form' => self::$form_name,
				'register_fields' => self::get_fields( 'register' ),
			)
		);

	}

}