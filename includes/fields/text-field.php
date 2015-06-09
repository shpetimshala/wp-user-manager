<?php
/**
 * Registers the text type field.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WPUM_Field_Type_Text extends WPUM_Field_Type {

	/**
	 * Constructor for the field type
	 *
	 * @since 1.0.0
 	 */
	public function __construct() {
		
		// DO NOT DELETE
		parent::__construct();

		$this->name  = _x( 'Text Field', 'field type name', 'wpum' );
		$this->type  = 'text';
		$this->class = __CLASS__;

	}

}

new WPUM_Field_Type_Text;