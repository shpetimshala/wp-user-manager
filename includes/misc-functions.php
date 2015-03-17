<?php
/**
 * Misc Functions
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Retrieve a list of all published pages
 *
 * On large sites this can be expensive, so only load if on the settings page or $force is set to true
 *
 * @since 1.0.0
 * @param bool $force Force the pages to be loaded even if not on settings
 * @return array $pages_options An array of the pages
 */
function wpum_get_pages( $force = false ) {

	$pages_options = array( 0 => '' ); // Blank option

	if( ( ! isset( $_GET['page'] ) || 'wpum-settings' != $_GET['page'] ) && ! $force ) {
		return $pages_options;
	}

	$pages = get_pages();
	if ( $pages ) {
		foreach ( $pages as $page ) {
			$pages_options[ $page->ID ] = $page->post_title;
		}
	}

	return $pages_options;
}

/**
 * Retrieve a list of all user roles
 *
 * On large sites this can be expensive, so only load if on the settings page or $force is set to true
 *
 * @since 1.0.0
 * @param bool $force Force the roles to be loaded even if not on settings
 * @return array $roles An array of the roles
 */
function wpum_get_roles( $force = false ) {

	$roles_options = array( 0 => '' ); // Blank option

	if( ( ! isset( $_GET['page'] ) || 'wpum-settings' != $_GET['page'] ) && ! $force ) {
		return $roles_options;
	}

	global $wp_roles;

	$roles = $wp_roles->get_names();

	// Remove administrator role for safety
	unset($roles['administrator']);

	return apply_filters( 'wpum_get_roles', $roles );
}

/**
 * Retrieve a list of allowed users role on the registration page
 *
 * @since 1.0.0
 * @return array $roles An array of the roles
 */
function wpum_get_allowed_user_roles() {

	global $wp_roles;

	if ( ! isset( $wp_roles ) ) 
		$wp_roles = new WP_Roles();

	$user_roles = array();
	$selected_roles = wpum_get_option('register_roles');
	$allowed_user_roles = is_array($selected_roles) ? $selected_roles : array($selected_roles);

    foreach ($allowed_user_roles as $role) {
		$user_roles[ $role ] = $wp_roles->roles[ $role ]['name'];
    }

	return $user_roles;

}

/**
 * Retrieve a list of disabled usernames
 *
 * @since 1.0.0
 * @return array $usernames An array of the usernames
 */
function wpum_get_disabled_usernames() {

	$usernames = array();

	if( wpum_get_option('exclude_usernames') ) {

		$list = trim(wpum_get_option('exclude_usernames'));
		$list = explode("\n", str_replace("\r", "", $list));

		foreach ($list as $username) {
			$usernames[] = $username;
		}

	}

	return array_flip($usernames);

}

/**
 * Gets all the email templates that have been registerd. The list is extendable
 * and more templates can be added.
 *
 * @since 1.0.0
 * @return array $templates All the registered email templates
 */
function wpum_get_email_templates() {
	$templates = new WPUM_Emails;
	return $templates->get_templates();
}

/**
 * Checks whether a given email id exists into the database.
 *
 * @since 1.0.0
 * @return bool
 */
function wpum_email_exists( $email_id ) {

	$exists = false;
	$emails = get_option( 'wpum_emails', array() );

	if( array_key_exists($email_id, $emails) ) 
		$exists = true;

	return $exists;
}

/**
 * Get an email from the database.
 *
 * @since 1.0.0
 * @return array email details containing subject and message
 */
function wpum_get_email( $email_id ) {

	$emails = get_option( 'wpum_emails', array() );

	return $emails[ $email_id ];

}

/**
 * Sort default fields table in the admin panel
 *
 * @since 1.0.0
 * @return array of all the fields correctly ordered.
 */
function wpum_sort_default_fields_table($a, $b) {
	return ($a['order'] < $b['order']) ? -1 : 1;
}

/**
 * Defines the list of default fields
 *
 * @since 1.0.0
 * @return void
*/
function wpum_default_user_fields_list() {	
	
	$fields = array();
	$fields['username'] = array(
	    'order'          => 0,
	    'title'          => __('Username'),
	    'type'           => 'text',
	    'meta'           => 'username',
	    'required'       => true,
	    'show_on_signup' => true
	);
	$fields['first_name'] = array(
	    'order'          => 1,
	    'title'          => __('First Name'),
	    'type'           => 'text',
	    'meta'           => 'first_name',
	    'required'       => false,
	    'show_on_signup' => false
	);
	$fields['last_name'] = array(
	    'order'          => 2,
	    'title'          => __('Last Name'),
	    'type'           => 'text',
	    'meta'           => 'last_name',
	    'required'       => false,
	    'show_on_signup' => false
	);
	$fields['nickname'] = array(
	    'order'          => 3,
	    'title'          => __('Nickname'),
	    'type'           => 'text',
	    'meta'           => 'nickname',
	    'required'       => true,
	    'show_on_signup' => false
	);
	$fields['display_name'] = array(
	    'order'          => 4,
	    'title'          => __('Display Name'),
	    'type'           => 'select',
	    'meta'           => 'display_name',
	    'required'       => true,
	    'show_on_signup' => false
	);
	$fields['user_email'] = array(
	    'order'          => 5,
	    'title'          => __('Email'),
	    'type'           => 'email',
	    'meta'           => 'user_email',
	    'required'       => true,
	    'show_on_signup' => true
	);
	$fields['user_url'] = array(
	    'order'          => 6,
	    'title'          => __('Website'),
	    'type'           => 'text',
	    'meta'           => 'user_url',
	    'required'       => false,
	    'show_on_signup' => false
	);
	$fields['description'] = array(
	    'order'          => 7,
	    'title'          => __('Description'),
	    'type'           => 'textarea',
	    'meta'           => 'description',
	    'required'       => false,
	    'show_on_signup' => false
	);
	$fields['password'] = array(
	    'order'          => 8,
	    'title'          => __('Password'),
	    'type'           => 'password',
	    'meta'           => 'password',
	    'required'       => true,
	    'show_on_signup' => true
	);
	
	$fields = apply_filters( 'wpum_default_fields_list', $fields );
	return $fields;

}

/**
 * Get a list of available permalink structures.
 *
 * @since 1.0.0
 * @return array of all the structures.
 */
function wpum_get_permalink_structures() {

	$structures = array(
		'user_id' => array(
			'name' => 'user_id',
			'label' => __('Display user ID'),
			'description' => 'Description goes here'
		),
		'username' => array(
			'name' => 'username',
			'label' => __('Display username'),
			'description' => 'Description goes here'
		),
	);

	return apply_filters( 'wpum_get_permalink_structures', $structures);
}

/**
 * Get ID of a core page.
 *
 * @since 1.0.0
 * @param string $name the name of the page. Supports: login, register, password, profile_edit, profile.
 * @return int $id of the core page.
 */
function wpum_get_core_page_id( $page ) {

	$id = 0;

	switch ( $page ) {
		case 'login':
			$id = wpum_get_option('login_page');
			break;
		case 'register':
			$id = wpum_get_option('registration_page');
			break;
		case 'password':
			$id = wpum_get_option('password_recovery_page');
			break;
		case 'profile_edit':
			$id = wpum_get_option('profile_edit_page');
			break;
		case 'profile':
			$id = wpum_get_option('profile_page');
			break;
		default:
			// nothing
			break;
	}

	return $id;
}

/**
 * Get URL of a core page.
 *
 * @since 1.0.0
 * @param string $name the name of the page. Supports: login, register, password, profile_edit, profile.
 * @return string $url of the core page.
 */
function wpum_get_core_page_url( $page ) {

	$url = 0;

	switch ( $page ) {
		case 'login':
			$url = esc_url( get_permalink( wpum_get_core_page_id('login') ) );
			break;
		case 'register':
			$url = esc_url( get_permalink( wpum_get_core_page_id('register') ) );
			break;
		case 'password':
			$url = esc_url( get_permalink( wpum_get_core_page_id('password') ) );
			break;
		case 'profile_edit':
			$url = esc_url( get_permalink( wpum_get_core_page_id('profile_edit') ) );
			break;
		case 'profile':
			$url = esc_url( get_permalink( wpum_get_core_page_id('profile') ) );
			break;
		default:
			// nothing
			break;
	}

	return $url;
}
