<?php
/**
 * WP User Manager Directories
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_Directory Class
 *
 * @since 1.0.0
 */
class WPUM_Directory {

	/**
	 * WPUM Directory Meta Options
	 *
	 * @var object
	 * @since 1.0.0
	 */
	public $directory_options;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		
		add_action( 'init', array( $this, 'directory_post_type' ) );
  		$this->directory_options = new Pretty_Metabox( $this->meta_options() );

	}

	/**
	 * Adds the directory post type.
	 * This handles the creation of user directories.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function directory_post_type() {

		$labels = array(
			'name'                => _x( 'User Directories', 'Post Type General Name', 'wpum' ),
			'singular_name'       => _x( 'Directory', 'Post Type Singular Name', 'wpum' ),
			'menu_name'           => __( 'Directory', 'wpum' ),
			'name_admin_bar'      => __( 'Directory', 'wpum' ),
			'parent_item_colon'   => __( 'Directory', 'wpum' ),
			'all_items'           => __( 'User Directories', 'wpum' ),
			'add_new_item'        => __( 'Add New Directory', 'wpum' ),
			'add_new'             => __( 'Add New Directory', 'wpum' ),
			'new_item'            => __( 'New Directory', 'wpum' ),
			'edit_item'           => __( 'Edit Directory', 'wpum' ),
			'update_item'         => __( 'Update Directory', 'wpum' ),
			'view_item'           => __( 'View Directory', 'wpum' ),
			'search_items'        => __( 'Search Directory', 'wpum' ),
			'not_found'           => __( 'Not found', 'wpum' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'wpum' ),
		);
		$args = array(
			'label'               => __( 'wpum_directory', 'wpum' ),
			'labels'              => apply_filters( 'wpum_directory_post_type_labels', $labels ),
			'supports'            => array( 'title', ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => 'users.php',
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
		);
		
		register_post_type( 'wpum_directory', apply_filters( 'wpum_directory_post_type_args', $args ) );

	}

	/**
	 * Adds the directory post type.
	 * This handles the creation of user directories.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function meta_options() {

		$config = array(
			'id'    => 'wpum_directory_options',
			'title' => __( 'General Settings' ),
			'pages' => array( 'wpum_directory' ),
			'fields' => array(
				array(
					'id'   => 'directory_roles',
					'name' => __( 'User Roles' ),
					'sub' => __( 'Leave blank to display all user roles.' ),
					'desc' => __( 'Select the user roles you wish to display into this directory.' ),
					'type' => 'multiselect',
					'options' => wpum_get_roles( true )
				),
			),
		);

		return apply_filters( 'wpum_directory_meta_options', $config );

	}

}

return new WPUM_Directory;