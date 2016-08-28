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
 * @since 1.4.0
 */
class WPUM_Menu_Controller {

	/**
	 * Get things started.
	 *
	 * @return void
	 */
	public function init() {

		// Change admin walker.
		add_filter( 'wp_edit_nav_menu_walker', array( $this, 'edit_nav_menu_walker' ) );

		// Add fields via hook.
		add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'add_custom_fields' ), 10, 4 );

	}

	/**
	 * Set the name of the class for the new Walker.
	 *
	 * @param  string $walker existing walker.
	 * @return string         new walker.
	 */
	public function edit_nav_menu_walker( $walker ) {

		return 'Walker_WPUM_Nav_Menu_Roles_Controller';

	}

	/**
	 * Register all new fields for the menus.
	 *
	 * @param  string $item_id current item id.
	 * @return array          fields to display.
	 */
	private function get_custom_fields( $item_id ) {

		$fields = array(

			array(
				'type'             => 'select',
				'label'            => esc_html( 'Display to:' ),
				'name'             => 'wpum_nav_menu_status_' . $item_id,
				'desc'             => esc_html__( 'Set the visibility of this menu item.', 'wpum' ),
				'show_option_all'  => false,
				'show_option_none' => false,
				'class'            => 'wpum-menu-visibility-setter',
				'options'          => array(
					''    => esc_html( 'Everyone' ),
					'in'  => esc_html( 'Logged In Users' ),
					'out' => esc_html( 'Logged Out Users' ),
				)
			),

			array(
				'type'             => 'select',
				'label'            => esc_html( 'Select roles:' ),
				'name'             => 'wpum_nav_menu_status_roles_' . $item_id,
				'desc'             => esc_html__( 'Select the roles that should see this menu item. Leave blank for all roles.', 'wpum' ),
				'show_option_all'  => false,
				'show_option_none' => false,
				'multiple'         => true,
				'options'          => wpum_get_roles( true )
			),

		);

		return $fields;

	}

	/**
	 * Render all the fields within the menu editor.
	 * Right now they're all "select" fields, refactor will probably change this.
	 *
	 * @param string $item_id item id.
	 * @param object $item    details about the item.
	 * @param string $depth   item depth.
	 * @param array $args     settings.
	 */
	public function add_custom_fields( $item_id, $item, $depth, $args ) {

		$fields = $this->get_custom_fields( $item_id );

		echo '<p class="wpum-menu-controller">';

		echo '<input type="hidden" class="nav-menu-id" value="'. esc_attr( $item_id ) .'">';

		foreach ( $fields as $field ) {

			echo WPUM()->html->select( $field );

		}

		echo '</p>';

	}

}

$wpum_menu_controller = new WPUM_Menu_Controller;
$wpum_menu_controller->init();
