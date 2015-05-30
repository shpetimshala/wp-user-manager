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

			<h2 class="wpum-page-title">
				<?php 
					if( isset( $_GET['edit-field'] ) && !empty( $_GET['edit-field'] ) ) :

						$field = wpum_get_field_by_meta( $_GET['edit-field'] );

						echo sprintf( __( 'Editing "%s" Field' ), $field['label'] );
					else:
						_e( 'WP User Manager - Custom Fields Editor' );
					endif;
				?>
			</h2>

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
					    ?>

					    <div class="wpum-table-loader">
					    	<span id="wpum-spinner" class="spinner wpum-spinner"></span>
					    </div>

				</div>

			</div>

		</div>

		<?php
		
		echo ob_get_clean();

	}

	/**
	 * Content of the first metabox.
	 *
	 * @access public
	 * @return mixed content of the "how it works" metabox.
	 */
	public static function help_text( $current_menu = null ) {

		$output = '<p>';
			$output .= sprintf( __('Click and drag the %s button to change the order of the fields.'), '<span class="dashicons dashicons-sort"></span>');
		$output .= '</p>';

		echo $output;

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
	 * Display fields editor in admin panel.
	 *
	 * @since 1.0.0
	 * @param string $id meta parameter of a field from the array in wpum_default_fields_list() function.
	 * @return mixed $output
	 */
	public static function display_fields_editor( $id ) {

		$field         = wpum_get_field_by_meta( $id );
		$field_options = wpum_get_field_options( $id );

		$output = '<tr id="wpum-edit-field-'.esc_attr($id).'" class="wpum-fields-editor field-'.esc_attr($id).'">';
			$output .= '<td colspan="5"><form method="post" action="" class="wpum-update-single-field">';
				$output .= '<div id="postbox-'.esc_attr($id).'" class="postbox wpum-editor-postbox">';
					$output .= '<h3 class="hndle ui-sortable-handle"><span>'. sprintf( __( 'Editing "%s" field.' ), $field['label'] ) .'</span></h3>';
						$output .= '<div class="inside">';

						// Generate options if any
						if( $field_options ) {
							foreach ($field_options as $key => $option) {

								$output .= '<div class="wpum-editor-field">';

								// Check field type - generate option accordingly
								switch ( $option['type'] ) {
									case 'text':
										$output .= WPUM()->html->$option['type']( 
											array( 
												'name'  => esc_attr( $option['name'] ),
												'label' => esc_html( $option['label'] ),
												'desc'  => isset( $option['desc'] ) ? esc_html( $option['desc'] ) : null,
												'value' => wpum_get_field_setting( $id, $option['name'] )
											)
										);
										break;
									case 'select':
										$output .= WPUM()->html->$option['type']( 
											array( 
												'name'             => esc_attr( $option['name'] ),
												'label'            => esc_html( $option['label'] ),
												'options'          => $option['choices'],
												'show_option_all'  => false,
												'show_option_none' => false,
												'selected'         => wpum_get_field_setting( $id, $option['name'] ),
												'desc'             => isset( $option['desc'] ) ? esc_html( $option['desc'] ) : null
											)
										);
										break;
									case 'checkbox':
										$output .= WPUM()->html->$option['type']( 
											array( 
												'name'    => esc_attr( $option['name'] ),
												'label'   => esc_html( $option['label'] ),
												'current' => wpum_get_field_setting( $id, $option['name'] ),
												'desc'    => isset( $option['desc'] ) ? esc_html( $option['desc'] ) : null
											)
										);
										break;
								}

								$output .= '</div>';

							}
						} else {
							$output .= '<p class="wpum-no-options">'.__( 'This field has no options.' ).'</p>';
						} // End options generation

						$output .= '<div id="major-publishing-actions">';
							$output .= '<div id="delete-action">';
								$output .= '<a class="button wpum-cancel-field" href="#">'.__('Cancel').'</a>';
							$output .= '</div>';

							$output .= '<div id="publishing-action">';
								if( $field_options )
									$output .= '<input type="submit" class="button-primary wpum-save-field" value="'.__('Update Field').'" />';
								$output .= wp_nonce_field( 'wpum_single_field', '_wpnonce', true, false );
							$output .= '</div>';
							$output .= '<div class="clear"></div>';
						$output .= '</div>';
				$output .= '</div>';
			$output .= '</form></td>';

		$output .= '</tr>';

		return $output;

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
