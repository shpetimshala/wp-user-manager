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

				<div id="menu-settings-column" class="metabox-holder">
					
					<div class="clear"></div>

					<?php do_accordion_sections( self::Hook, 'side', null ); ?>
						
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
		
		$output = '<div class="wp-filter">';
			$output .= '<ul class="filter-links">';
				$output .= '<li><a href="" class=" current">'. __('Registration Fields') .'</a></li>';
				$output .= '<li><a href="" class="">'. __('Profile Fields') .'</a></li>';
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

		add_meta_box(
			'myplugin_sectionid',
			__( 'My Post Section Title', 'myplugin_textdomain' ),
			array( $this, 'test' ),
			self::Hook,
			'side'
		);

		add_meta_box(
			'myplugin_sectionid2',
			__( 'T1', 'myplugin_textdomain' ),
			array( $this, 'test' ),
			self::Hook,
			'side'
		);

	}

	function test() {

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
