<?php
/**
 * Registers the file type field.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_Field_Type_Avatar Class
 *
 * @since 1.0.0
 */
class WPUM_Field_Type_File extends WPUM_Field_Type {

	/**
	 * Constructor for the field type
	 *
	 * @since 1.0.0
 	 */
	public function __construct() {

		// DO NOT DELETE
		parent::__construct();

		// Label of this field type.
		$this->name             = _x( 'File', 'field type name', 'wpum' );
		// Field type name.
		$this->type             = 'file';
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

		$options[] = array(
			'name'             => 'mime_types',
			'label'            => esc_html__( 'Allowed file types' ),
			'desc'             => esc_html__( 'Select the file types that can be uploaded through this field.' ),
			'type'             => 'select',
			'multiple'         => true,
			'options'          => function_exists( 'wpumcf_get_formatted_mime_types' ) ? wpumcf_get_formatted_mime_types(): array(),
			'show_option_all'  => false,
			'show_option_none' => false,
 		);

		$options[] = array(
			'name'  => 'multiple',
			'label' => esc_html__( 'Allow multiple files' ),
			'desc'  => esc_html__( 'Enable this option to allow users to upload multiple files through this field.' ),
			'type'  => 'checkbox',
		);

		$options[] = array(
			'name'  => 'max_file_size',
			'label' => esc_html__( 'Maximum file size' ),
			'desc'  => esc_html__( 'Enter the maximum file size users can upload through this field. The amount must be in bytes.' ),
			'type'  => 'text',
		);

		return $options;

	}

}

new WPUM_Field_Type_File;
