<?php
/**
 * Handles management of user status specific menu items.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2016, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_Menu_Controller
 *
 * @since 1.0.0
 */
class WPUM_Menu_Controller {

	public function init() {

		// Change admin walker.
		add_filter( 'wp_edit_nav_menu_walker', array( $this, 'edit_nav_menu_walker' ) );

		// Add fields via hook.
		add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'add_custom_fields' ), 10, 4 );

	}

	public function edit_nav_menu_walker( $walker ) {

		return 'Walker_WPUM_Nav_Menu_Roles_Controller';

	}

	public function add_custom_fields( $item_id, $item, $depth, $args ) {

		echo "test";

	}


}

$wpum_menu_controller = new WPUM_Menu_Controller;
$wpum_menu_controller->init();
