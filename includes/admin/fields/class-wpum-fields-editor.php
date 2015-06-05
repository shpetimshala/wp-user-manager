<?php
/**
 * WP User Manager: Fields Editor
 *
 * @package     wp-user-manager
 * @author      Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_Fields_Editor Class
 *
 * @since 1.0.0
 */
class WPUM_Fields_Editor {

	/**
	 * Holds the editor page id.
	 *
	 * @since 1.0.0
	 */
	const hook = 'users_page_wpum-profile-fields';

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		add_action( 'load-'.self::hook, array( $this, 'add_screen_meta_boxes' ) );
		add_action( 'add_meta_boxes_'.self::hook, array( $this, 'add_meta_box' ) );
		add_action( 'admin_footer-'.self::hook, array( $this, 'print_script_in_footer' ) );

		// Load WP_List_Table
		if( ! class_exists( 'WP_List_Table' ) ) {
		    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
		}

		require_once WPUM_PLUGIN_DIR . 'includes/admin/fields/class-wpum-groups-fields.php';

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

		<div class="wrap wpum-fields-editor-wrap">

			<h2 class="wpum-page-title">
				<?php _e( 'WP User Manager - Fields Editor' ); ?>
				<?php do_action( 'wpum/fields/editor/title' ); ?>
			</h2>

			<?php echo self::navbar(); ?>

			<?php echo self::primary_message(); ?>

			<div id="nav-menus-frame">

				<!-- Sidebar -->
				<div id="menu-settings-column" class="metabox-holder">
					
					<div class="clear"></div>

					<?php do_accordion_sections( self::hook, 'side', null ); ?>
						
				</div>
				<!-- End Sidebar -->

				<div id="menu-management-liquid" class="wpum-editor-container">
					
					<?php echo self::group_table(); ?>

				</div>

			</div>

		</div>

		<?php
		
		echo ob_get_clean();

	}

	/**
	 * Displays the groups navigation bar
	 *
	 * @access private
	 * @return void
	 */
	private static function navbar() {

		// Get all groups
		$groups = WPUM()->field_groups->get_groups( array( 'order' => 'ASC' ) );

		$output = '<div class="wp-filter">';
			$output .= '<ul class="filter-links">';
				
				foreach ( $groups as $key => $group ) {

					$url = add_query_arg( array( 'action' => 'edit', 'group' => absint( $group->id ) ), admin_url( 'users.php?page=wpum-profile-fields' ) );
					$selected = ( isset( $_GET['group'] ) && $_GET['group'] == $group->id || !isset( $_GET['group'] ) && $key == 0 ) ? 'current' : null;
					
					$output .= '<li><a class="'. $selected .'" href="'. esc_url( $url ) .'">'. esc_html( $group->name ) .'</a></li>';

				}

			$output .= '</ul>';
		$output .= '</div>';

		return $output;

	}

	/**
	 * Displays the table to manage each single group.
	 *
	 * @access private
	 * @return void
	 */
	private static function group_table() {

		$custom_fields_table = new WPUM_Groups_Fields();
		$custom_fields_table->prepare_items();
		$custom_fields_table->display();
		
		wp_nonce_field( 'wpum_fields_editor' );

	}

	/**
	 * Trigger the add_meta_boxes hooks to allow meta boxes to be added.
	 *
	 * @access public
	 * @return void
	 */
	public function add_screen_meta_boxes() {
 
	    do_action( 'add_meta_boxes_'.self::hook, null );
	    do_action( 'add_meta_boxes', self::hook, null );
	 
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
		add_meta_box( 'wpum_fields_editor_help', __( 'How it works' ), array( $this, 'help_text' ), self::hook, 'side' );
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
	 * Display a message about the primary group.
	 *
	 * @access private
	 */
	private static function primary_message() {

		if( isset( $_GET['group'] ) )
			return;
		?>

		<p><span class="dashicons dashicons-info"></span> <?php _e('Fields in the "Primary" group will appear on the signup page.') ;?></p>

		<?php
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

new WPUM_Fields_Editor;
