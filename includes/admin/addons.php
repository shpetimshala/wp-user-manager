<?php
/**
 * Handles the display of the addons page.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_Addons Class
 *
 * @since 1.0.0
 */
class WPUM_Addons {

	/**
	 * API URL
	 */
	protected $api = 'http://dev:8888/wpum/edd-api/products/';

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		add_filter( 'install_plugins_tabs', array( $this, 'wpum_add_addon_tab' ) );
		add_action( 'install_plugins_wpum_addons', array( $this, 'wpum_addons_page' ) );

	}

	/**
	 * Adds a new tab to the install plugins page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function wpum_add_addon_tab( $tabs ) {
		
		$tabs['wpum_addons'] = __( 'WP User Manager ' ) . '<span class="wpum-addons">'.__('Addons').'</span>' ;
		return $tabs;

	}

	/**
	 * Handles the display of the content of the new tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function wpum_addons_page() {

		?>

		<div class="wp-list-table widefat plugin-install">

		</div>

		<?php
			
	}

	/**
	 * Handles the display of each single addon.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function display_addon() {

		echo '<div id="the-list">';
				echo '<div class="plugin-card">';
					echo '<div class="plugin-card-top">';
						echo '<a href="" target="_blank" class="thickbox plugin-icon"><img src=""></a>';
						echo '<div class="name column-name" style="margin-right:0px">';
							echo '<h4><a href="" target="_blank" class="thickbox"></a></h4>';
						echo '</div>';
						echo '<div class="desc column-description">';
							echo '<p></p>';
						echo '</div>';
					echo '</div>';
					echo '<div class="plugin-card-bottom">';
						echo '<a target="_blank" href="" class="button-primary" style="display:block; text-align:center;"></a>';
					echo '</div>';
				echo '</div>';
		echo '</div>';

	}

}

new WPUM_Addons;