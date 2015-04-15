<?php
/**
 * Recently registered users widget.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_Recently_Registered_Users Class
 *
 * @since 1.0.0
 */
class WPUM_Recently_Registered_Users extends WPH_Widget {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		// Configure widget array
		$args = array(
			'label'       => __( '[WPUM] Recent Users' ),
			'description' => __( 'Display a list of recently registered users.' ),
		);

		$args['fields'] = array(
			array(
				'name'   => __( 'Title' ),
				'id'     => 'title',
				'type'   => 'text',
				'class'  => 'widefat',
				'std'    => __( 'Recent Users' ),
				'filter' => 'strip_tags|esc_attr'
			),
		);

		// create widget
		$this->create_widget( $args );

	}

}