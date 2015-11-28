<?php
/**
 * WP User Manager Addons license handler.
 *
 * @package     wp-user-manager
 * @copyright   Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_License Class
 *
 * @since 1.2.4
 */
class WPUM_License {

  /**
	 * File path
	 *
	 * @var string
	 */
	private $file;

  /**
   * License stored.
   *
   * @var string
   */
  private $license;

  /**
   * Item name from the site.
   *
   * @var string
   */
  private $item_name;

  /**
   * Item id from the site.
   *
   * @var string
   */
  private $item_id;

  /**
   * Item shortname.
   *
   * @var string
   */
  private $item_shortname;

  /**
   * Item version.
   *
   * @var string
   */
  private $version;

  /**
   * The author of the plugin.
   *
   * @var string
   */
  private $author;

  /**
   * Api url.
   *
   * @var string
   */
  private $api_url = '';


  /**
   * Construction function.
   *
   * @param string $file    file path.
   * @param string $item_name    item name.
   * @param string $version version of the addon.
   * @param string $author  author of the addon.
   */
	public function __construct( $file, $item_name, $version, $author ) {

    $this->file      = $file;
    $this->item_name = $item_name;
    $this->version   = $version;
    $this->author    = $author;

    $this->item_shortname = 'wpum_' . preg_replace( '/[^a-zA-Z0-9_\s]/', '', str_replace( ' ', '_', strtolower( $this->item_name ) ) );
    $this->license        = trim( wpum_get_option( $this->item_shortname . '_license_key', '' ) );

    $this->includes();
    $this->hooks();

	}

  /**
   * Includes the EDD library.
   *
   * @since 1.2.4
   * @return void
   */
  private function includes() {

    if( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
      include( WPUM_PLUGIN_DIR . '/includes/updater/EDD_SL_Plugin_Updater.php' );
    }

  }

  /**
   * Setup hooks.
   *
   * @since 1.2.4
   * @return void
   */
  private function hooks() {

    // Register settings.
		add_filter( 'wpum_settings_licenses', array( $this, 'settings' ), 1 );

  }

  /**
   * Add new settings in admin panel.
   *
   * @since 1.2.4
   * @param  array $settings registered settings.
   * @return array           registered settings.
   */
  public function settings( $settings ) {

    $wpum_license_settings = array(
			array(
				'id'      => $this->item_shortname . '_license_key',
				'name'    => sprintf( __( '%1$s License Key' ), $this->item_name ),
				'desc'    => '',
				'type'    => 'license_key',
				'options' => array( 'is_valid_license_option' => $this->item_shortname . '_license_active' ),
				'size'    => 'regular'
			)
		);

    return array_merge( $settings, $wpum_license_settings );

  }

}
