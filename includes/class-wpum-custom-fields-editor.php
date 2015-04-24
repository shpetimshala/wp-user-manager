<?php
/**
 * Custom Fields Editor.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_Custom_Fields_Editor Class
 *
 * @since 1.0.0
 */
class WPUM_Custom_Fields_Editor {

	/**
	 * Holds the editor page id.
	 *
	 * @since 1.0.0
	 */
	const Hook = 'users_page_wpum-custom-fields-editor';

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		add_action( 'load-'.self::Hook, array( $this, 'add_screen_meta_boxes' ) );
		add_action( 'add_meta_boxes_'.self::Hook, array( $this, 'add_meta_box' ) );
		add_action( 'admin_footer-'.self::Hook, array( $this, 'print_script_in_footer' ) );

		// Load WP_List_Table
		if( ! class_exists( 'WP_List_Table' ) ) {
		    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
		}

	}

	/**
	 * Handles the display of the editor page in the backend.
	 *
	 * @access public
	 * @return void
	 */
	public static function editor_page() {

		ob_start();
		
		?>

		<div class="wrap">

			<h2 class="wpum-page-title"><?php _e( 'WP User Manager - Custom Fields Editor' ); ?></h2>


			<div id="nav-menus-frame">

				<!-- Sidebar -->
				<div id="menu-settings-column" class="metabox-holder">
					
					<div class="clear"></div>

					<?php do_accordion_sections( self::Hook, 'side', null ); ?>
						
				</div>
				<!-- End Sidebar -->

				<div id="menu-management-liquid" class="wpum-editor-container">
				
						<?php 

						$custom_fields_table = new WPUM_Custom_Fields_List();
					    $custom_fields_table->prepare_items();
					    $custom_fields_table->display();

					    wp_nonce_field( 'wpum_fields_editor' );

					    echo '<div class="wpum-table-loader"><span id="wpum-spinner" class="spinner wpum-spinner"></span></div></div>';

					    ?>

				</div>

			</div>

		</div>

		<?php
		
		echo ob_get_clean();

	}

	/**
	 * Trigger the add_meta_boxes hooks to allow meta boxes to be added.
	 *
	 * @access public
	 * @return void
	 */
	public function add_screen_meta_boxes() {
 
	    do_action( 'add_meta_boxes_'.self::Hook, null );
	    do_action( 'add_meta_boxes', self::Hook, null );
	 
	    /* Enqueue WordPress' script for handling the meta boxes */
	    wp_enqueue_script('postbox');
	}

	/**
	 * Register metaboxes.
	 *
	 * @access public
	 * @return void
	 */
	public function add_meta_box() {

		add_meta_box( 'wpum_fields_editor_help', __( 'How it works' ), array( $this, 'help_text' ), self::Hook, 'side' );

	}

	/**
	 * Content of the first metabox.
	 *
	 * @access public
	 * @return mixed content of the "how it works" metabox.
	 */
	public function help_text() {

		$output = '<p>';
			$output .= sprintf( __('Click and drag the %s button to change the order of the fields.'), '<span class="dashicons dashicons-sort"></span>');
		$output .= '</p>';

		echo $output;

	}

	/**
	 * Print metabox scripts into the footer.
	 *
	 * @access public
	 * @return void
	 */
	public function print_script_in_footer() {
		?>
		<script>jQuery(document).ready(function(){ postboxes.add_postbox_toggles(pagenow); });</script>
		<?php
	}

}

new WPUM_Custom_Fields_Editor;
