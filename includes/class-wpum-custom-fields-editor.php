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
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

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

			<h2><span class="dashicons dashicons-admin-settings"></span> <?php _e( 'WP User Manager - Custom Fields Editor' ); ?></h2>
			
			<?php echo self::navbar(); ?>

			<div id="nav-menus-frame">

				<?php echo self::sidebar(); ?>

			</div>
		</div><!-- .wrap -->
		<?php
		echo ob_get_clean();

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
	 * Handles the display of the editor page sidebar in the backend.
	 *
	 * @access public
	 * @return void
	 */
	public static function sidebar() { 

		ob_start();
		?>

		<div id="menu-settings-column" class="metabox-holder">
			<div class="clear"></div>
			<div id="side-sortables" class="accordion-container">
				<ul class="outer-border">
					<li class="control-section accordion-section  add-page open" id="add-page">
						<h3 class="accordion-section-title hndle" tabindex="0"></h3>
						<div class="accordion-section-content " style="display: block;">

							<div class="inside"></div>

						</div>
					</li>
				</ul>
			</div>
		</div>
		<?php 
		
		return ob_get_clean();
	}

}

new WPUM_Custom_Fields_Editor;
