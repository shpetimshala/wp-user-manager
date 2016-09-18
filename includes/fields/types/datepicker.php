<?php
/**
 * Registers the datepicker type field.
 *
 * @package     wpum-custom-fields
 * @copyright   Copyright (c) 2016, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_Field_Type_Datepicker Class
 *
 * @since 1.0.0
 */
class WPUM_Field_Type_Datepicker extends WPUM_Field_Type {

	/**
	 * Constructor for the field type
	 *
	 * @since 1.0.0
 	 */
	public function __construct() {

		// DO NOT DELETE
		parent::__construct();

		// Label of this field type.
		$this->name             = _x( 'Datepicker', 'field type name', 'wpum' );
		// Field type name.
		$this->type             = 'datepicker';
		// Field category.
		$this->category         = 'advanced';
		// Class of this field.
		$this->class            = __CLASS__;
		// Set registration.
		$this->set_registration = true;
		// Set requirement.
		$this->set_requirement  = true;

	}

	/**
	 * Method to register options for fields.
	 *
	 * @since 1.2.0
	 * @access public
	 * @return array list of options.
	 */
	public static function options() {

		$options = array();

		return $options;

	}

	/**
	 * Modify the output of the field on the fronted profile.
	 *
	 * @since 1.2.0
	 * @param  string $value the value of the field.
	 * @param  object $field field details.
	 * @return string        the formatted field value.
	 */
	public static function output_html( $value, $field ) {

		$output = '';

		return $output;

	}

}

new WPUM_Field_Type_Datepicker;
