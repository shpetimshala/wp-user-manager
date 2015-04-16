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
			array(
				'name'     => __( 'Amount' ),
				'desc'     => __( 'Enter the amount of users you wish to display.' ),
				'id'       => 'amount',
				'type'     => 'text',
				'class'    => 'widefat',
				'std'      => '10',
				'filter'   => 'strip_tags|esc_attr',
				'validate' => 'numeric',
			),
			array(
				'name'   => __( 'Link to user profile' ),
				'desc'   => __( 'Enable to link to the user profile.' ),
				'id'     => 'profile',
				'type'   =>'checkbox',
				'std'    => 1,
				'filter' => 'strip_tags|esc_attr',
			),
		);

		// create widget
		$this->create_widget( $args );

	}

	/**
	 * Display widget content.
	 *
	 * @access private
	 * @since 1.0.0
	 * @return void
	 */
	public function widget( $args, $instance ) {

		ob_start();

		echo $args['before_widget'];
		echo $args['before_title'];
		echo $instance['title'];
		echo $args['after_title'];

		if( $instance['profile'] == 1 ) {
			$instance['profile'] = true;
		}

		get_wpum_template( 'recently-registered.php', array( 'amount' => $instance['amount'], 'link_to_profile' => $instance['profile'] ) );

		echo $args['after_widget'];

		$output = ob_get_clean();

		echo $output;

	}

}
