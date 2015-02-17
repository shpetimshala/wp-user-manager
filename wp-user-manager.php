<?php
/**
 * Plugin Name: WP User Manager
 * Plugin URI:  http://wp-user-manager.com
 * Description: Create customized user profiles and easily add custom user registration, login and password recovery forms to your WordPress website. WP User Manager is the best solution to manage your users.
 * Version:     1.0.0
 * Author:      Alessandro Tesoro
 * Author URI:  http://alessandrotesoro.me
 * License:     GPLv2+
 * Text Domain: wpum
 * Domain Path: /languages
 * 
 * @package wp-user-manager
 * @author Alessandro Tesoro
 * @version 1.0.0
 */

/**
 * Copyright (c) 2015 Alessandro Tesoro
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_User_Manager' ) ) :

	/**
	 * Main WP_Restaurant_Manager Class
	 *
	 * @since 1.0.0
	 */
	class WP_User_Manager {

		/** Singleton *************************************************************/
		/**
		 * @var WP_User_Manager.
		 * @since 1.0.0
		 */
		private static $instance;

		/**
		 * Main WP_User_Manager Instance
		 *
		 * Insures that only one instance of WP_User_Manager exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 * @uses WP_User_Manager::setup_constants() Setup the constants needed
		 * @uses WP_User_Manager::includes() Include the required files
		 * @uses WP_User_Manager::load_textdomain() load the language files
		 * @see WPUM()
		 * @return WP_User_Manager
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WP_User_Manager ) ) {
				self::$instance = new WP_User_Manager;
				self::$instance->setup_constants();
				self::$instance->includes();
				self::$instance->load_textdomain();
			}
			return self::$instance;

		}

		/**
		 * Throw error on object clone
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wpum' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class
		 *
		 * @since 1.0.0
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wpum' ), '1.0.0' );
		}

		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @since 1.0.0
		 * @return void
		 */
		private function setup_constants() {
			
			// Plugin version
			if ( ! defined( 'WPUM_VERSION' ) ) {
				define( 'WPUM_VERSION', '1.0.0' );
			}

			// Plugin Folder Path
			if ( ! defined( 'WPUM_PLUGIN_DIR' ) ) {
				define( 'WPUM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL
			if ( ! defined( 'WPUM_PLUGIN_URL' ) ) {
				define( 'WPUM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File
			if ( ! defined( 'WPUM_PLUGIN_FILE' ) ) {
				define( 'WPUM_PLUGIN_FILE', __FILE__ );
			}

			if ( ! defined( 'WPUM_SLUG' ) ) {
				define( 'WPUM_SLUG', plugin_basename(__FILE__));
			}

		}

		/**
		 * Include required files
		 *
		 * @access private
		 * @since 1.0.0
		 * @return void
		 */
		private function includes() {

			require_once WPUM_PLUGIN_DIR . 'includes/filters.php';
			
		}

		/**
		 * Loads the plugin language files
		 *
		 * @access public
		 * @since 1.0.0
		 * @return void
		 */
		public function load_textdomain() {

		}

	}

endif;

/**
 * The main function responsible for returning WP_User_Manager
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $wpum = WPUM(); ?>
 *
 * @since 1.0.0
 * @return object WP_User_Manager Instance
 */
function WPUM() {
	return WP_User_Manager::instance();
}

// Get WPUM Running
WPUM();