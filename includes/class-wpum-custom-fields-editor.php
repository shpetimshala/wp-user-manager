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
	 * Links of the editor navbar
	 *
	 * @var object
	 * @since 1.0.0
	 */
	public static $nav_links;

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

		// Navbar links
		self::$nav_links = array(
			array(
				'type' => 'registration',
				'title' => __('Registration fields')
			),
			array(
				'type' => 'profile',
				'title' => __('Profile fields')
			),
		);

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

			<?php echo self::navbar(); ?>

			<div id="nav-menus-frame">

				<!-- Sidebar -->
				<div id="menu-settings-column" class="metabox-holder">
					
					<div class="clear"></div>

					<?php do_accordion_sections( self::Hook, 'side', null ); ?>
						
				</div>
				<!-- End Sidebar -->

				<div id="menu-management-liquid">
				
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
	 * Handles the display of the editor page navbar in the backend.
	 *
	 * @access public
	 * @return void
	 */
	public static function navbar() {
		
		// Define base url
		$url = admin_url( 'users.php?page=wpum-custom-fields-editor' );

		$output = '<div class="wp-filter">';
			$output .= '<ul class="filter-links">';
				
				foreach ( self::$nav_links as $link ) {
					$link_url = add_query_arg( array( 'editor' => $link['type'] ), $url );
					if( isset( $_GET['editor'] ) && $_GET['editor'] == $link['type'] || !isset( $_GET['editor'] ) && $link['type'] == 'registration' ) :
						$output .= '<li><a href="'.$link_url.'" class="current">'. $link['title'] .'</a></li>';
					else : 
						$output .= '<li><a href="'.$link_url.'">'. $link['title'] .'</a></li>';
					endif;
				}

			$output .= '</ul>';
		$output .= '</div>';

		return $output;
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
			$output .= sprintf( __('Click and drag the %s button or the field order number to change the order of the fields.'), '<span class="dashicons dashicons-sort"></span>');
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
