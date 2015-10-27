<?php
/**
 * Registers the url type field.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_Field_Type_Url Class
 *
 * @since 1.0.0
 */
class WPUM_Field_Type_Url extends WPUM_Field_Type {

	/**
	 * Constructor for the field type
	 *
	 * @since 1.0.0
	*/
	public function __construct() {

		// DO NOT DELETE.
		parent::__construct();

		// Label of this field type.
		$this->name             = _x( 'Url', 'field type name', 'wpum' );
		// Field type name
		$this->type             = 'url';
		// Class of this field
		$this->class            = __CLASS__;
		// Set registration
		$this->set_registration = true;
		// Set requirement
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

		$options[] = array(
			'name'     => 'rel',
			'label'    => esc_html__( 'Nofollow' ),
			'desc'     => esc_html__( 'Enable this option to specify that the search spiders should not follow this link.' ),
			'type'     => 'checkbox',
		);

		return $options;

	}

}

new WPUM_Field_Type_Url;
