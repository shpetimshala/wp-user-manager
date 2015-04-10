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
	 * WPUM Directory General Options
	 *
	 * @var object
	 * @since 1.0.0
	 */
	public $directory_general_options;

	/**
	 * WPUM Directory Sorting Options
	 *
	 * @var object
	 * @since 1.0.0
	 */
	public $directory_sorting_options;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		
		add_action( 'init', array( $this, 'directory_post_type' ) );
  		add_action( 'admin_init', array( $this, 'meta_options' ) );

  		// Only in admin panel
  		if( is_admin() ) {
  			add_filter( 'manage_edit-wpum_directory_columns', array( $this, 'post_type_columns' ) );
  			add_action( 'manage_wpum_directory_posts_custom_column', array( $this, 'post_type_columns_content' ), 2 );
  			add_filter( 'post_row_actions', array( $this, 'remove_action_rows'), 10, 2 );
  			add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
  			add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_post_updated_messages' ) );
  		}

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

		$general_options = array(
			'id'     => 'wpum_directory_general_options',
			'title'  => __( 'General Settings' ),
			'pages'  => array( 'wpum_directory' ),
			'fields' => array(
				array(
					'id'      => 'directory_roles',
					'name'    => __( 'User Roles' ),
					'sub'     => __( 'Leave blank to display all user roles.' ),
					'desc'    => __( 'Select the user roles you wish to display into this directory.' ),
					'type'    => 'multiselect',
					'options' => wpum_get_roles( true )
				),
				array(
					'id'   => 'display_search_form',
					'name' => __( 'Display search form' ),
					'desc' => __( 'Enable this option to display the user search form' ),
					'type' => 'checkbox',
					'std'  => 1
				),
				array(
					'id'   => 'excluded_ids',
					'name' => __( 'Exclude users' ),
					'sub'  => __( 'Comma separated list of users id you wish to exclude.' ),
					'desc' => sprintf( __(' Example: type %s to exclude users with those id(s).'), '<code>1, 6, 89</code>' ),
					'type' => 'text',
				),
				array(
					'id'   => 'profiles_per_page',
					'name' => __( 'Profiles per page' ),
					'sub'  => __( 'Select how many profiles you wish to display per page.' ),
					'type' => 'number',
					'std'  => 10
				),
				array(
					'id'      => 'directory_template',
					'name'    => __( 'Directory Template' ),
					'desc'    => __( 'Select a template from the list.' ),
					'type'    => 'select',
					'options' => wpum_get_directory_templates()
				),
			),
		);
		
		// Create the new metabox
		$this->directory_general_options = new Pretty_Metabox( apply_filters( 'wpum_directory_general_options', $general_options ) );

		// Build the sorting metabox options
		$sorting_options = array(
			'id'     => 'wpum_directory_sorting_options',
			'title'  => __( 'Users sorting' ),
			'pages'  => array( 'wpum_directory' ),
			'fields' => array(
				array(
					'id'   => 'display_sorter',
					'name' => __( 'Display sorter' ),
					'desc' => __( 'Enable this option to display the user sorter field.' ),
					'type' => 'checkbox',
					'std'  => 0
				),
				array(
					'id'      => 'default_sorting_method',
					'name'    => __( 'Sorting method' ),
					'sub'     => __( 'Select the sorting method for the directory' ),
					'desc'    => __('If the sorter field is visible, this will be used as default option.'),
					'type'    => 'select',
					'options' => wpum_get_directory_sorting_methods()
				),
			),
		);

		// Create the new metabox
		$this->directory_sorting_options = new Pretty_Metabox( apply_filters( 'wpum_directory_general_options', $sorting_options ) );

	}

	/**
	 * Modifies the list of columns available into the directory post type.
	 *
	 * @access public
	 * @param mixed $columns
	 * @return array $columns
	 */
	public function post_type_columns( $columns ) {
		if ( ! is_array( $columns ) )
			$columns = array();

		unset( $columns['date'], $columns['author'] );

		$columns["roles"]             = __( 'User Roles' );
		$columns["search_form"]       = __( 'Display search form' );
		$columns["profiles_per_page"] = __( 'Profiles per page' );
		$columns["shortcode"]         = __( 'Shortcode' );

		return $columns;
	}

	/**
	 * Adds the content to the custom columns for the directory post type
	 *
	 * @access public
	 * @param mixed $column
	 * @return void
	 */
	public function post_type_columns_content( $columns ) {

		global $post;

		switch ( $columns ) {
			case 'roles':
				$roles = get_post_meta( $post->ID, 'directory_roles', true );	
				if( $roles ) {
					echo implode( ', ', $roles );
				} else {
					echo __( 'All' );
				}
				break;
			case 'search_form':
				if( get_post_meta( $post->ID, 'display_search_form', true ) ) {
					echo '<span class="dashicons dashicons-yes"></span>';
				} else {
					echo '<span class="dashicons dashicons-no"></span>';
				}
				break;
			case 'profiles_per_page':
				echo get_post_meta( $post->ID, 'profiles_per_page', true );
				break;
			case 'shortcode':
				echo '[wpum_user_directory id="'.$post->ID.'"]';
				break;
		}

	}

	/**
	 * Modifies the action links into the post type page.
	 *
	 * @access public
	 * @return $actions array contains all action links.
	 */
	public function remove_action_rows( $actions, $post ) {
		
		if ( $post->post_type == 'wpum_directory' ) {
			unset($actions['inline hide-if-no-js']);
			unset($actions['view']);
		}

		return $actions;

	}

	/**
	 * Modifies the post update messages for this post type.
	 *
	 * @access public
	 * @param mixed $messages
	 * @return void
	 */
	function post_updated_messages( $messages ) {
		
		global $post, $post_ID;

		$messages['wpum_directory'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Directory updated.' ),
			2  => __( 'Custom field updated.' ),
			3  => __( 'Custom field deleted.'),
			4  => __( 'Directory updated.' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Directory restored to revision from %s' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => sprintf( __( 'Directory created. Use the following shortcode to display this directory %s' ), '<code>[wpum_user_directory id="'.$post_ID.'"]</code>' ),
			7  => __( 'Directory saved.' ),
			8  => __( 'Directory submitted.' ),
		);

		return $messages;
	}

	/**
	 * Modifies the text of the trash function.
	 *
	 * @access public
	 * @param mixed $bulk_messages
	 * @return array $bulk_messages
	 */
	public function bulk_post_updated_messages( $bulk_messages ) {
		global $post, $post_ID;

		$bulk_counts = array(
			'updated'   => isset( $_REQUEST['updated'] )   ? absint( $_REQUEST['updated'] )   : 0,
			'locked'    => isset( $_REQUEST['locked'] )    ? absint( $_REQUEST['locked'] )    : 0,
			'deleted'   => isset( $_REQUEST['deleted'] )   ? absint( $_REQUEST['deleted'] )   : 0,
			'trashed'   => isset( $_REQUEST['trashed'] )   ? absint( $_REQUEST['trashed'] )   : 0,
			'untrashed' => isset( $_REQUEST['untrashed'] ) ? absint( $_REQUEST['untrashed'] ) : 0,
		);

		$bulk_messages['wpum_directory'] = array(
			'updated'   => _n( '%s directory updated.', '%s directory updated.', $bulk_counts['updated'], 'wprm' ),
			'locked'    => _n( '%s directory not updated, somebody is editing it.', '%s directories not updated, somebody is editing them.', $bulk_counts['locked'], 'wprm' ),
			'deleted'   => _n( '%s directory permanently deleted.', '%s directories permanently deleted.', $bulk_counts['deleted'], 'wprm' ),
			'trashed'   => _n( '%s directory has been deleted.', '%s directories have been deleted.', $bulk_counts['trashed'], 'wprm' ),
			'untrashed' => _n( '%s directory restored from the Trash.', '%s directories restored from the Trash.', $bulk_counts['untrashed'], 'wprm' ),
		);

		return $bulk_messages;
	}

}

return new WPUM_Directory;