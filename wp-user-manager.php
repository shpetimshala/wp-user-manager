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
	 * Main WP_User_Manager Class
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
		 * Forms Object
		 *
		 * @var object
		 * @since 1.0.0
		 */
		public $forms;

		/**
		 * WPUM Emails Object
		 *
		 * @var object
		 * @since 1.0.0
		 */
		public $emails;

		/**
		 * WPUM Email Template Tags Object
		 *
		 * @var object
		 * @since 1.0.0
		 */
		public $email_tags;

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
				self::$instance->emails     = new WPUM_Emails();
				self::$instance->email_tags = new WPUM_Email_Template_Tags();
				self::$instance->forms      = new WPUM_Forms();

				// load admin assets css and scripts
				add_action( 'admin_enqueue_scripts', array( self::$instance, 'admin_enqueue_scripts' ) );
				// load frontend assets css and scripts
				add_action( 'wp_enqueue_scripts', array( self::$instance, 'wp_enqueue_scripts' ) );

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

			// Plugin Slug
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

			global $wpum_options;

			require_once WPUM_PLUGIN_DIR . 'includes/admin/settings/register-settings.php';
			$wpum_options = wpum_get_settings();

			// Load General Functions
			require_once WPUM_PLUGIN_DIR . 'includes/functions.php';
			// Load Misc Functions
			require_once WPUM_PLUGIN_DIR . 'includes/misc-functions.php';
			// Templates
			require_once WPUM_PLUGIN_DIR . 'includes/templates.php';
			// Plugin's filters
			require_once WPUM_PLUGIN_DIR . 'includes/filters.php';
			// Plugin's actions
			require_once WPUM_PLUGIN_DIR . 'includes/actions.php';
			// Forms
			require_once WPUM_PLUGIN_DIR . 'includes/class-wpum-forms.php';
			// Shortcodes
			require_once WPUM_PLUGIN_DIR . 'includes/class-wpum-shortcodes.php';
			// Emails
			require_once WPUM_PLUGIN_DIR . 'includes/class-wpum-emails.php';
			// Emails Tags
			require_once WPUM_PLUGIN_DIR . 'includes/class-wpum-emails-tags.php';
			// Directory for WPUM
			require_once WPUM_PLUGIN_DIR . 'includes/class-wpum-directory.php';
			
			// Files loaded only on the admin side
			if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
				// Load Welcome Page
				require_once WPUM_PLUGIN_DIR . 'includes/admin/welcome.php';
				// Load Settings Pages
				require_once WPUM_PLUGIN_DIR . 'includes/admin/admin-pages.php';
				// Load Admin Notices
				require_once WPUM_PLUGIN_DIR . 'includes/admin/admin-notices.php';
				// Load Admin Actions
				require_once WPUM_PLUGIN_DIR . 'includes/admin/admin-actions.php';
				// Display Settings Page
				require_once WPUM_PLUGIN_DIR . 'includes/admin/settings/display-settings.php';
				// Load Emails Editor
				require_once WPUM_PLUGIN_DIR . 'includes/class-wpum-emails-editor.php';
				// Load Emails List Table
				require_once WPUM_PLUGIN_DIR . 'includes/class-wpum-emails-list.php';
				// Load Default Fields List
				require_once WPUM_PLUGIN_DIR . 'includes/class-wpum-default-fields-list.php';
				// Load Default Fields Editor
				require_once WPUM_PLUGIN_DIR . 'includes/class-wpum-default-fields-editor.php';
				// Custom Fields Framework
				require_once WPUM_PLUGIN_DIR . 'includes/lib/wp-pretty-fields/wp-pretty-fields.php';
			}

			// Ajax Handler
			require_once WPUM_PLUGIN_DIR . 'includes/class-wpum-ajax-handler.php';
			// Permalinks for WPUM
			require_once WPUM_PLUGIN_DIR . 'includes/class-wpum-permalinks.php';
			// Template actions
			require_once WPUM_PLUGIN_DIR . 'includes/template-actions.php';
			// Load Profiles template system
			require_once WPUM_PLUGIN_DIR . 'includes/profiles/profile-actions.php';
			// Load Profiles template system
			require_once WPUM_PLUGIN_DIR . 'includes/profiles/profile-tabs.php';
			
		}

		/**
		 * Loads the plugin admin assets files
		 *
		 * @access public
		 * @since 1.0.0
		 * @return void
		 */
		public function admin_enqueue_scripts() {

			$js_dir  = WPUM_PLUGIN_URL . 'assets/js/';
			$css_dir = WPUM_PLUGIN_URL . 'assets/css/';

			// Use minified libraries if SCRIPT_DEBUG is turned off
			$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			// Styles & scripts
			wp_register_style( 'wpum-admin', $css_dir . 'wp_user_manager' . $suffix . '.css', WPUM_VERSION );
			wp_register_style( 'wpum-shortcode-manager', WPUM_PLUGIN_URL . 'includes/admin/tinymce/css/wpum_shortcodes_tinymce_style.css', WPUM_VERSION );
			wp_register_style( 'wpum-select2', WPUM_PLUGIN_URL . 'assets/select2/css/select2.css', WPUM_VERSION );
			wp_register_script( 'wpum-select2', WPUM_PLUGIN_URL . 'assets/select2/js/select2.min.js', 'jQuery', WPUM_VERSION, true );
			wp_register_script( 'wpum-admin-js', $js_dir . 'wp_user_manager_admin' . $suffix . '.js', 'jQuery', WPUM_VERSION, true );

			// Enquery styles and scripts anywhere needed
			wp_enqueue_style( 'wpum-shortcode-manager' );

			// Enqueue styles & scripts on admin page only
			$screen = get_current_screen();

			if ( $screen->base !== 'users_page_wpum-settings' )
				return;

			wp_enqueue_script( 'wpum-select2' );
			wp_enqueue_script( 'wpum-admin-js' );
			wp_enqueue_style( 'wpum-admin' );
			wp_enqueue_style( 'wpum-select2' );

			if( isset($_GET['tab']) && $_GET['tab'] == 'default_fields' && $screen->base == 'users_page_wpum-settings' )
				wp_enqueue_script('jquery-ui-sortable');

			// Backend JS Settings
			wp_localize_script( 'wpum-admin-js', 'wpum_admin_js', array(
				'ajax'    => admin_url( 'admin-ajax.php' ),
				'confirm' => __('Are you sure you want to do this? This action cannot be reversed.'),
			) );

			wp_enqueue_media();

		}

		/**
		 * Loads the plugin frontend assets files
		 *
		 * @access public
		 * @since 1.0.0
		 * @return void
		 */
		public function wp_enqueue_scripts() {

			$js_dir  = WPUM_PLUGIN_URL . 'assets/js/';
			$css_dir = WPUM_PLUGIN_URL . 'assets/css/';

			// Use minified libraries if SCRIPT_DEBUG is turned off
			$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			// Styles & scripts registration
			wp_register_script( 'wpum-frontend-js', $js_dir . 'wp_user_manager' . $suffix . '.js', array( 'jquery' ), WPUM_VERSION, true );
			wp_register_style( 'wpum-frontend-css', $css_dir . 'wp_user_manager_frontend' . $suffix . '.css' , WPUM_VERSION );

			// Enqueue everything
			wp_enqueue_script( 'jQuery' );
			wp_enqueue_script( 'wpum-frontend-js' );
			
			// Allows developers to disable the frontend css in case own file is needed.
			if( !defined( 'WPUM_DISABLE_CSS' ) )
				wp_enqueue_style( 'wpum-frontend-css' );

			// Display password meter only if enabled
			if( wpum_get_option('display_password_meter_registration') && wpum_get_option('custom_passwords') ) :
				wp_enqueue_script( 'password-strength-meter' );
				wp_localize_script( 'password-strength-meter', 'pwsL10n', array(
					'empty'  => __( 'Strength indicator' ),
					'short'  => __( 'Very weak' ),
					'bad'    => __( 'Weak' ),
					'good'   => _x( 'Medium', 'password strength' ),
					'strong' => __( 'Strong' )
				) );
			endif;

			// Frontend jS Settings
			wp_localize_script( 'wpum-frontend-js', 'wpum_frontend_js', array(
				'ajax'                   => admin_url( 'admin-ajax.php' ),
				'checking_credentials'   => __('Checking credentials...'),
				'pwd_meter'              => wpum_get_option('display_password_meter_registration'),
				'disable_ajax'           => wpum_get_option('disable_ajax')
			) );

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