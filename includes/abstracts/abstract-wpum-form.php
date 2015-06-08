<?php

/**
 * Abstract WPUM_Form class.
 *
 * @abstract
 * @author      Mike Jolley
 * @author      Alessandro Tesoro
 */
abstract class WPUM_Form {

	protected static $fields;
	protected static $action;
	protected static $errors = array();
	protected static $confirmations = array();

	/**
	 * Add an error.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return array of errors.
	 */
	public static function add_error( $error ) {
		self::$errors[] = $error;
	}

	/**
	 * Show errors.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public static function show_errors() {
		foreach ( self::$errors as $error )
			echo '<div class="wpum-message error"><p class="the-message">' . $error . '</p></div>';
	}

	/**
	 * Add a confirmation message.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return $array of confirmation messages
	 */
	public static function add_confirmation( $confirmation ) {
		self::$confirmations[] = $confirmation;
	}

	/**
	 * Show confirmation messages.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return $void
	 */
	public static function show_confirmations() {
		foreach ( self::$confirmations as $confirmation )
			echo '<div class="wpum-message success"><p class="the-message">' . $confirmation . '</p></div>';
	}

	/**
	 * Get action
	 *
	 * @return string
	 */
	public static function get_action() {
		return self::$action;
	}

	/**
	 * get_fields function.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param mixed $key
	 * @return array
	 */
	public static function get_fields( $key ) {
		if ( empty( self::$fields[ $key ] ) )
			return array();

		$fields = self::$fields[ $key ];

		uasort( $fields, __CLASS__ . '::priority_cmp' );

		return $fields;
	}

	/**
	 * priority_cmp function.
	 *
	 * @access private
	 * @since 1.0.0
	 * @param mixed $a
	 * @param mixed $b
	 * @return void
	 */
	public static function priority_cmp( $a, $b ) {
	    if ( $a['priority'] == $b['priority'] )
	        return 0;
	    return ( $a['priority'] < $b['priority'] ) ? -1 : 1;
	}
}