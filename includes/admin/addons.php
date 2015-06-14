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
	protected $api = 'http://wpusermanager.com/edd-api/products/';

	/**
	 * All addons
	 */
	var $addons = null;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		if( is_admin() && isset( $_GET['tab'] ) && $_GET['tab'] == 'wpum_addons' ) {

			// Get the transient
			$cached_feed = get_transient( 'wpum_addons_feed' );

			// Check if feed exist -
			// if feed exists get content from cached feed.
			if ($cached_feed) {
				
				$this->addons = json_decode( $cached_feed );

			// Feed is not cached, get content from live api.
			} else {

				$feed = wp_remote_get( $this->api, array( 'sslverify' => false ) );

				if ( ! is_wp_error( $feed ) ) {

					$feed_content = wp_remote_retrieve_body( $feed );
					set_transient( 'wpum_addons_feed', $feed_content, 3600 );
					$this->addons = json_decode( $feed_content );

				}

			}

		}

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

			<?php if( empty( $this->addons ) ) : ?>
			
				<p><?php echo sprintf( __('Looks like there was a problem while retrieving the list of addons. Please visit <a href="%s">%s</a> if you are looking for the WP User Manager addons.'), 'http://wpusermanager.com/addons/', 'http://wpusermanager.com/addons/' ); ?></p>
			
			<?php else : ?>
				
				<br/>
				
				<div id="the-list">

					<?php foreach ( $this->addons->products as $addon ) {
						$this->display_addon( $addon->info );
					} ?>

				</div>

			<?php endif; ?>

		</div>

		<?php
			
	}

	/**
	 * Handles the display of each single addon.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function display_addon( $addon ) {

		echo '<div class="plugin-card">';
			echo '<div class="plugin-card-top">';
				echo '<a href="'.$addon->link.'" target="_blank" class="plugin-icon"><img src="'. $addon->thumbnail .'"></a>';
				echo '<div class="name column-name" style="margin-right:0px">';
					echo '<h4><a href="'.$addon->link.'" target="_blank">'. $addon->title .'</a></h4>';
				echo '</div>';
				echo '<div class="desc column-description" style="margin-right:0px">';
					echo '<p>'. wp_trim_words( $addon->content, 35 ) .'</p>';
				echo '</div>';
			echo '</div>';
			echo '<div class="plugin-card-bottom">';
				echo '<a target="_blank" href="'.$addon->link.'" class="button" style="display:block; text-align:center;">'.__('Read More').'</a>';
			echo '</div>';
		echo '</div>';

	}

}

new WPUM_Addons;