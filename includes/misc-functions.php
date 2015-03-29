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
			'name'   => 'user_id',
			'label'  => _x( 'Display user ID', 'Permalink structure' ),
			'sample' => '123'
		),
		'username' => array(
			'name'   => 'username',
			'label'  => _x( 'Display username', 'Permalink structure' ),
			'sample' => _x( 'username', 'Example of permalink setting' )
		),
		'nickname' => array(
			'name'   => 'nickname',
			'label'  => _x( 'Display nickname', 'Permalink structure' ),
			'sample' => _x( 'nickname', 'Example of permalink setting' )
		),
	);

	return apply_filters( 'wpum_get_permalink_structures', $structures );
}

/**
 * Get ID of a core page.
 *
 * @since 1.0.0
 * @param string $name the name of the page. Supports: login, register, password, profile_edit, profile.
 * @return int $id of the core page.
 */
function wpum_get_core_page_id( $page ) {

	$id = null;

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

	$url = null;

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

/**
 * Checks if guests can view profiles.
 *
 * @since 1.0.0
 * @return bool
 */
function wpum_guests_can_view_profiles() {

	$pass = false;

	if( wpum_get_option('guests_can_view_profiles') )
		$pass = true;

	return $pass;
}

/**
 * Checks if members can view profiles.
 *
 * @since 1.0.0
 * @return bool
 */
function wpum_members_can_view_profiles() {

	$pass = false;

	if( wpum_get_option('members_can_view_profiles') )
		$pass = true;

	return $pass;

}

/**
 * Checks if viewing single profile page.
 *
 * @since 1.0.0
 * @return bool
 */
function wpum_is_single_profile() {

	$who = (get_query_var('user')) ? get_query_var('user') : false;

	return $who;

}

/**
 * Checks if profiles are available.
 *
 * @since 1.0.0
 * @return bool
 */
function wpum_can_access_profile() {

	$pass = true;

	// Check if not logged in and on profile page - no given user
	if( !is_user_logged_in() && !wpum_is_single_profile() ) {
		// Display error message
		$args = array( 
					'id'   => 'wpum-guests-disabled', 
					'type' => 'notice', 
					'text' => sprintf( __('This content is available to members only. Please <a href="%s">login</a> or <a href="%s">register</a> to view this area.'), wpum_get_core_page_url('login'), wpum_get_core_page_url('register')  )
				);
		wpum_message( $args );
		$pass = false;
	}

	// Block guests on single profile page if option disabled
	if( !is_user_logged_in() && wpum_is_single_profile() && !wpum_guests_can_view_profiles() ) {
		// Display error message
		$args = array( 
					'id'   => 'wpum-guests-disabled', 
					'type' => 'notice', 
					'text' => sprintf( __('This content is available to members only. Please <a href="%s">login</a> or <a href="%s">register</a> to view this area.'), wpum_get_core_page_url('login'), wpum_get_core_page_url('register')  )
				);
		wpum_message( $args );
		$pass = false;
	}

	// Block members on single profile page if option disabled
	if( is_user_logged_in() && wpum_is_single_profile() && !wpum_members_can_view_profiles() ) {
		// Display error message
		$args = array( 
					'id'   => 'wpum-no-access', 
					'type' => 'notice', 
					'text' => __( 'You are not authorized to access this area.' )
				);
		wpum_message( $args );
		$pass = false;
	}

	return apply_filters( 'wpum_can_access_profile', $pass );

}

/**
 * Checks the current active tab (if any).
 *
 * @since 1.0.0
 * @return bool|string
 */
function wpum_get_current_profile_tab() {

	$tab = ( get_query_var('tab') ) ? get_query_var('tab') : null;
	return $tab;

}

/**
 * Checks the given profile tab is registered.
 *
 * @since 1.0.0
 * @param string $tab the key value of the array in wpum_get_user_profile_tabs() must match slug
 * @return bool
 */
function wpum_profile_tab_exists( $tab ) {

	$exists = false;

	if( array_key_exists( $tab, wpum_get_user_profile_tabs() ) )
		$exists = true;

	return $exists;

}

/**
 * Returns the permalink of a profile tab.
 *
 * @since 1.0.0
 * @return bool|string
 */
function wpum_get_profile_tab_permalink( $user_data, $tab ) {

	$tab_slug = $tab['slug'];
	$base_link = wpum_get_user_profile_url( $user_data );

	$tab_permalink = $base_link . '/' . $tab_slug;

	return $tab_permalink;
}

/**
 * Display a message loading the message.php template file.
 *
 * @since 1.0.0
 * @param string $id html ID attribute.
 * @param string $type message type: success/notice/error.
 * @param string $text the text of the message.
 * @return void
 */
function wpum_message( $args ) {

	$defaults = array(
		'id'   => 'wpum-notice', // html ID attribute
		'type' => 'success', // message type: success/notice/error.
		'text' => '' // the text of the message.
	);

	// Parse incoming $args into an array and merge it with $defaults
	$args = wp_parse_args( $args, $defaults );

	echo get_wpum_template( 'message.php', array( 
				'id'   => $args['id'], 
				'type' => $args['type'], 
				'text' => $args['text']
			)
		);

}

/**
 * Gets a list of users orderded by most recent registration date.
 *
 * @since 1.0.0
 * @param int $amount amount of users to load.
 * @return void
 */
function wpum_get_recent_users( $amount ) {

	$args = array(
		'number'  => $amount,
		'order'   => 'DESC',
		'orderby' => 'registered'
	);

	// The Query
	$user_query = new WP_User_Query( apply_filters( 'wpum_get_recent_users', $args ) );

	// Get the results
	$users = $user_query->get_results();

	return $users;
}

/**
 * Check if a given nickname already exists.
 *
 * @since 1.0.0
 * @param string $nickname
 * @return bool
 */
function wpum_nickname_exists( $nickname ) {

	$exists = false;

	$args = array(
		'fields'         => 'user_nicename',
		'search'         => $nickname,
		'search_columns' => array( 'user_nicename' )
	);

	// The Query
	$user_query = new WP_User_Query( $args );

	// Get the results
	$users = $user_query->get_results();

	if( !empty( $users ) )
		$exists = true;

	return $exists;

}
