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
 * @param bool    $force Force the pages to be loaded even if not on settings
 * @return array $pages_options An array of the pages
 */
function wpum_get_pages( $force = false ) {

	$pages_options = array( 0 => '' ); // Blank option

	if ( ( ! isset( $_GET['page'] ) || 'wpum-settings' != $_GET['page'] ) && ! $force ) {
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
 * @param bool    $force Force the roles to be loaded even if not on settings
 * @return array $roles An array of the roles
 */
function wpum_get_roles( $force = false ) {

	$roles_options = array( 0 => '' ); // Blank option

	if ( ( ! isset( $_GET['page'] ) || 'wpum-settings' != $_GET['page'] ) && ! $force ) {
		return $roles_options;
	}

	global $wp_roles;

	$roles = $wp_roles->get_names();

	// Remove administrator role for safety
	unset( $roles['administrator'] );

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
	$selected_roles = wpum_get_option( 'register_roles' );
	$allowed_user_roles = is_array( $selected_roles ) ? $selected_roles : array( $selected_roles );

	foreach ( $allowed_user_roles as $role ) {
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

	if ( wpum_get_option( 'exclude_usernames' ) ) {

		$list = trim( wpum_get_option( 'exclude_usernames' ) );
		$list = explode( "\n", str_replace( "\r", "", $list ) );

		foreach ( $list as $username ) {
			$usernames[] = $username;
		}

	}

	return array_flip( $usernames );

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

	if ( array_key_exists( $email_id, $emails ) )
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
function wpum_sort_default_fields_table( $a, $b ) {
	return ( $a['order'] < $b['order'] ) ? -1 : 1;
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
		'title'          => __( 'Username' ),
		'type'           => 'text',
		'meta'           => 'username',
		'required'       => true,
		'show_on_signup' => true
	);
	$fields['first_name'] = array(
		'order'          => 1,
		'title'          => __( 'First Name' ),
		'type'           => 'text',
		'meta'           => 'first_name',
		'required'       => false,
		'show_on_signup' => false
	);
	$fields['last_name'] = array(
		'order'          => 2,
		'title'          => __( 'Last Name' ),
		'type'           => 'text',
		'meta'           => 'last_name',
		'required'       => false,
		'show_on_signup' => false
	);
	$fields['nickname'] = array(
		'order'          => 3,
		'title'          => __( 'Nickname' ),
		'type'           => 'text',
		'meta'           => 'nickname',
		'required'       => true,
		'show_on_signup' => false
	);
	$fields['display_name'] = array(
		'order'          => 4,
		'title'          => __( 'Display Name' ),
		'type'           => 'select',
		'meta'           => 'display_name',
		'required'       => true,
		'show_on_signup' => false
	);
	$fields['user_email'] = array(
		'order'          => 5,
		'title'          => __( 'Email' ),
		'type'           => 'email',
		'meta'           => 'user_email',
		'required'       => true,
		'show_on_signup' => true
	);
	$fields['user_url'] = array(
		'order'          => 6,
		'title'          => __( 'Website' ),
		'type'           => 'text',
		'meta'           => 'user_url',
		'required'       => false,
		'show_on_signup' => false
	);
	$fields['description'] = array(
		'order'          => 7,
		'title'          => __( 'Description' ),
		'type'           => 'textarea',
		'meta'           => 'description',
		'required'       => false,
		'show_on_signup' => false
	);
	$fields['password'] = array(
		'order'          => 8,
		'title'          => __( 'Password' ),
		'type'           => 'password',
		'meta'           => 'password',
		'required'       => true,
		'show_on_signup' => true
	);
	$fields['user_avatar'] = array(
		'order'          => 9,
		'title'          => __( 'Profile Picture' ),
		'type'           => 'file',
		'meta'           => 'user_avatar',
		'required'       => false,
		'show_on_signup' => false
	);

	$fields = apply_filters( 'wpum_default_fields_list_', $fields );
	return $fields;

}

/**
 * Defines the list of registration and profile fields.
 *
 * @since 1.0.0
 * @return void
 */
function wpum_default_fields_list() {

	$fields = array();
	
	$fields['username'] = array(
		'order'          => 0,
		'title'          => __( 'Username' ),
		'type'           => 'text',
		'meta'           => 'username',
		'required'       => true,
		'options' => array(
			'test' => array(
				'type' => 'text',
				'name' => 'test',
				'label' => __('Some label'),
				'desc' => 'something goes here'
			),
			'new' => array(
				'type' => 'select',
				'name' => 'new',
				'label' => __('Some select'),
				'choices' => array( 'asd' => 'test', 'asd2' => 'option' ),
				'desc' => 'something goes here'
			),
			'check' => array(
				'type' => 'checkbox',
				'name' => 'check',
				'label' => __('Some select'),
				'desc' => 'something goes here'
			)
		),
	);
	$fields['first_name'] = array(
		'order'          => 1,
		'title'          => __( 'First Name' ),
		'type'           => 'text',
		'meta'           => 'first_name',
		'required'       => false,
	);
	$fields['last_name'] = array(
		'order'          => 2,
		'title'          => __( 'Last Name' ),
		'type'           => 'text',
		'meta'           => 'last_name',
		'required'       => false,
	);
	$fields['nickname'] = array(
		'order'          => 3,
		'title'          => __( 'Nickname' ),
		'type'           => 'text',
		'meta'           => 'nickname',
		'required'       => true,
	);
	$fields['display_name'] = array(
		'order'          => 4,
		'title'          => __( 'Display Name' ),
		'type'           => 'select',
		'meta'           => 'display_name',
		'required'       => true,
	);
	$fields['user_email'] = array(
		'order'          => 5,
		'title'          => __( 'Email' ),
		'type'           => 'email',
		'meta'           => 'user_email',
		'required'       => true,
	);
	$fields['user_url'] = array(
		'order'          => 6,
		'title'          => __( 'Website' ),
		'type'           => 'text',
		'meta'           => 'user_url',
		'required'       => false,
	);
	$fields['description'] = array(
		'order'          => 7,
		'title'          => __( 'Description' ),
		'type'           => 'textarea',
		'meta'           => 'description',
		'required'       => false,
	);
	$fields['password'] = array(
		'order'          => 8,
		'title'          => __( 'Password' ),
		'type'           => 'password',
		'meta'           => 'password',
		'required'       => true,
	);
	$fields['user_avatar'] = array(
		'order'          => 9,
		'title'          => __( 'Profile Picture' ),
		'type'           => 'file',
		'meta'           => 'user_avatar',
		'required'       => false,
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
 * @param string  $name the name of the page. Supports: login, register, password, account, profile.
 * @return int $id of the core page.
 */
function wpum_get_core_page_id( $page ) {

	$id = null;

	switch ( $page ) {
	case 'login':
		$id = wpum_get_option( 'login_page' );
		break;
	case 'register':
		$id = wpum_get_option( 'registration_page' );
		break;
	case 'password':
		$id = wpum_get_option( 'password_recovery_page' );
		break;
	case 'account':
		$id = wpum_get_option( 'account_page' );
		break;
	case 'profile':
		$id = wpum_get_option( 'profile_page' );
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
 * @param string  $name the name of the page. Supports: login, register, password, account, profile.
 * @return string $url of the core page.
 */
function wpum_get_core_page_url( $page ) {

	$url = null;

	switch ( $page ) {
	case 'login':
		$url = esc_url( get_permalink( wpum_get_core_page_id( 'login' ) ) );
		break;
	case 'register':
		$url = esc_url( get_permalink( wpum_get_core_page_id( 'register' ) ) );
		break;
	case 'password':
		$url = esc_url( get_permalink( wpum_get_core_page_id( 'password' ) ) );
		break;
	case 'account':
		$url = esc_url( get_permalink( wpum_get_core_page_id( 'account' ) ) );
		break;
	case 'profile':
		$url = esc_url( get_permalink( wpum_get_core_page_id( 'profile' ) ) );
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

	if ( wpum_get_option( 'guests_can_view_profiles' ) )
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

	if ( wpum_get_option( 'members_can_view_profiles' ) )
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

	$who = ( get_query_var( 'user' ) ) ? get_query_var( 'user' ) : false;

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
	if ( !is_user_logged_in() && !wpum_is_single_profile() ) {
		// Display error message
		$args = array(
			'id'   => 'wpum-guests-disabled',
			'type' => 'notice',
			'text' => sprintf( __( 'This content is available to members only. Please <a href="%s">login</a> or <a href="%s">register</a> to view this area.' ), wpum_get_core_page_url( 'login' ), wpum_get_core_page_url( 'register' )  )
		);
		wpum_message( $args );
		$pass = false;
	}

	// Block guests on single profile page if option disabled
	if ( !is_user_logged_in() && wpum_is_single_profile() && !wpum_guests_can_view_profiles() ) {
		// Display error message
		$args = array(
			'id'   => 'wpum-guests-disabled',
			'type' => 'notice',
			'text' => sprintf( __( 'This content is available to members only. Please <a href="%s">login</a> or <a href="%s">register</a> to view this area.' ), wpum_get_core_page_url( 'login' ), wpum_get_core_page_url( 'register' )  )
		);
		wpum_message( $args );
		$pass = false;
	}

	// Block members on single profile page if option disabled
	if ( is_user_logged_in() && wpum_is_single_profile() && !wpum_members_can_view_profiles() ) {
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

	$tab = ( get_query_var( 'tab' ) ) ? get_query_var( 'tab' ) : null;
	return $tab;

}

/**
 * Checks the given profile tab is registered.
 *
 * @since 1.0.0
 * @param string  $tab the key value of the array in wpum_get_user_profile_tabs() must match slug
 * @return bool
 */
function wpum_profile_tab_exists( $tab ) {

	$exists = false;

	if ( array_key_exists( $tab, wpum_get_user_profile_tabs() ) )
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
 * @param string  $id   html ID attribute.
 * @param string  $type message type: success/notice/error.
 * @param string  $text the text of the message.
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
 * @param int     $amount amount of users to load.
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
 * @param string  $nickname
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

	if ( !empty( $users ) )
		$exists = true;

	return $exists;

}

/**
 * Force 404 error headers.
 *
 * @since 1.0.0
 * @return void
 */
function wpum_trigger_404() {

	global $wp_query;

	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();

}

/**
 * Given $user_data checks against $method_type if the user exists.
 *
 * @since 1.0.0
 * @param string  $user_data   Either ID/Username/Nickname
 * @param string  $method_type Either user_id/username/nickname - usually retrieve thorugh get_option('wpum_permalink')
 * @return bool
 */
function wpum_user_exists( $user_data, $method_type ) {

	$exists = false;

	// Check if user exists by ID
	if ( !empty( $user_data ) && $method_type == 'user_id' && get_user_by( 'id', intval( $user_data ) ) ) {
		$exists = true;
	}

	// Check if user exists by username
	if ( !empty( $user_data ) && $method_type == 'username' && get_user_by( 'login', esc_attr( $user_data ) ) ) {
		$exists = true;
	}

	// Check if user exists by nickname
	if ( !empty( $user_data ) && $method_type == 'nickname' && wpum_nickname_exists( $user_data ) ) {
		$exists = true;
	}

	return $exists;

}

/**
 * Triggers the mechanism to upload files.
 *
 * @copyright mikejolley
 * @since 1.0.0
 * @param array   $file_data Array of $_FILE data to upload.
 * @return array|WP_Error Array of objects containing either file information or an error
 */
function wpum_trigger_upload_file( $field_key, $field ) {

	if ( isset( $_FILES[ $field_key ] ) && ! empty( $_FILES[ $field_key ] ) && ! empty( $_FILES[ $field_key ]['name'] ) ) {

		if ( ! empty( $field['allowed_mime_types'] ) ) {
			$allowed_mime_types = $field['allowed_mime_types'];
		} else {
			$allowed_mime_types = get_allowed_mime_types();
		}

		$file_urls       = array();
		$files_to_upload = wpum_prepare_uploaded_files( $_FILES[ $field_key ] );

		foreach ( $files_to_upload as $file_key => $file_to_upload ) {

			if ( !in_array( $file_to_upload['type'] , $allowed_mime_types ) )
				return new WP_Error( 'validation-error', sprintf( __( 'Allowed files types are: %s' ), implode( ', ', array_keys( $field['allowed_mime_types'] ) ) ) );

			if ( defined( 'WPUM_MAX_AVATAR_SIZE' ) && $field_key == 'user_avatar' && $file_to_upload['size'] > WPUM_MAX_AVATAR_SIZE )
				return new WP_Error( 'avatar-too-big', __( 'The uploaded file is too big.' ) );

			$uploaded_file = wpum_upload_file( $file_to_upload, array( 'file_key' => $file_key ) );

			if ( is_wp_error( $uploaded_file ) ) {
				return new WP_Error( 'validation-error', $uploaded_file->get_error_message() );
			} else {

				$file_urls[] = array(
					'url' => $uploaded_file->url,
					'path' => $uploaded_file->path,
					'size' => $uploaded_file->size
				);
			}

		}

		if ( ! empty( $field['multiple'] ) ) {
			return $file_urls;
		} else {
			return current( $file_urls );
		}

		return $files_to_upload;
	}

}

/**
 * Prepare the files to upload.
 *
 * @copyright mikejolley
 * @since 1.0.0
 * @param array   $file_data Array of $_FILE data to upload.
 * @return array|WP_Error Array of objects containing either file information or an error
 */
function wpum_prepare_uploaded_files( $file_data ) {
	$files_to_upload = array();

	if ( is_array( $file_data['name'] ) ) {
		foreach ( $file_data['name'] as $file_data_key => $file_data_value ) {

			if ( $file_data['name'][ $file_data_key ] ) {
				$files_to_upload[] = array(
					'name'     => $file_data['name'][ $file_data_key ],
					'type'     => $file_data['type'][ $file_data_key ],
					'tmp_name' => $file_data['tmp_name'][ $file_data_key ],
					'error'    => $file_data['error'][ $file_data_key ],
					'size'     => $file_data['size'][ $file_data_key ]
				);
			}
		}
	} else {
		$files_to_upload[] = $file_data;
	}

	return $files_to_upload;
}

/**
 * Upload a file using WordPress file API.
 *
 * @since 1.0.0
 * @copyright mikejolley
 * @param array   $file_data Array of $_FILE data to upload.
 * @param array   $args      Optional arguments
 * @return array|WP_Error Array of objects containing either file information or an error
 */
function wpum_upload_file( $file, $args = array() ) {
	global $wpum_upload, $wpum_uploading_file;

	include_once ABSPATH . 'wp-admin/includes/file.php';
	include_once ABSPATH . 'wp-admin/includes/media.php';

	$args = wp_parse_args( $args, array(
			'file_key'           => '',
			'file_label'         => '',
			'allowed_mime_types' => get_allowed_mime_types()
		) );

	$wpum_upload         = true;
	$wpum_uploading_file = $args['file_key'];
	$uploaded_file              = new stdClass();

	if ( ! in_array( $file['type'], $args['allowed_mime_types'] ) ) {
		if ( $args['file_label'] ) {
			return new WP_Error( 'upload', sprintf( __( '"%s" (filetype %s) needs to be one of the following file types: %s' ), $args['file_label'], $file['type'], implode( ', ', array_keys( $args['allowed_mime_types'] ) ) ) );
		} else {
			return new WP_Error( 'upload', sprintf( __( 'Uploaded files need to be one of the following file types: %s' ), implode( ', ', array_keys( $args['allowed_mime_types'] ) ) ) );
		}
	} else {
		$upload = wp_handle_upload( $file, apply_filters( 'submit_wpum_handle_upload_overrides', array( 'test_form' => false ) ) );
		if ( ! empty( $upload['error'] ) ) {
			return new WP_Error( 'upload', $upload['error'] );
		} else {
			$uploaded_file->url       = $upload['url'];
			$uploaded_file->name      = basename( $upload['file'] );
			$uploaded_file->path      = $upload['file'];
			$uploaded_file->type      = $upload['type'];
			$uploaded_file->size      = $file['size'];
			$uploaded_file->extension = substr( strrchr( $uploaded_file->name, '.' ), 1 );
		}
	}

	$wpum_upload         = false;
	$wpum_uploading_file = '';

	return $uploaded_file;
}

/**
 * Retrieve the avatar for a user who provided a user ID or email address.
 *
 * This is a pluggable WP function, WPUM, is overriding the core get_avatar function,
 * to prevent extra calls to gravatar if a custom avatar already exists.
 * This wouldn't have been possible through the usage of the get_avatar filter.
 *
 * @since 1.0.0
 *
 * @param int|string|object $id_or_email A user ID,  email address, or comment object
 * @param int     $size        Size of the avatar image
 * @param string  $default     URL to a default image to use if no avatar is available
 * @param string  $alt         Alternative text to use in image tag. Defaults to blank
 * @return false|string `<img>` tag for the user's avatar.
 */
function get_avatar( $id_or_email, $size = '96', $default = '', $alt = false ) {
	if ( ! get_option( 'show_avatars' ) )
		return false;

	if ( false === $alt )
		$safe_alt = '';
	else
		$safe_alt = esc_attr( $alt );

	if ( !is_numeric( $size ) )
		$size = '96';

	// Detect whether the user has a custom avatar - loaded through ID
	// if so - stop everything else and load custom avatar only.
	if ( is_numeric( $id_or_email ) ) {

		$custom_avatar = get_user_meta( $id_or_email, 'current_user_avatar', true );

		if ( !empty( $custom_avatar ) ) {
			$out = "<img alt='{$safe_alt}' src='{$custom_avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
			return $out;
		}

		// Detect whether this is a comment object
		// if is so, check whether the user exists
		// grab the avatar and stop everything else.
	} else if ( is_object( $id_or_email ) ) {

			$user_object_id = $id_or_email->user_id;

			if ( $user_object_id > 0 ) {
				$custom_avatar = get_user_meta( $user_object_id, 'current_user_avatar', true );
				if ( !empty( $custom_avatar ) ) {
					$out = "<img alt='{$safe_alt}' src='{$custom_avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
					return $out;
				}
			}

		}

	$email = '';
	if ( is_numeric( $id_or_email ) ) {
		$id = (int) $id_or_email;
		$user = get_userdata( $id );
		if ( $user )
			$email = $user->user_email;
	} elseif ( is_object( $id_or_email ) ) {
		// No avatar for pingbacks or trackbacks

		/**
		 * Filter the list of allowed comment types for retrieving avatars.
		 *
		 * @since 3.0.0
		 *
		 * @param array   $types An array of content types. Default only contains 'comment'.
		 */
		$allowed_comment_types = apply_filters( 'get_avatar_comment_types', array( 'comment' ) );
		if ( ! empty( $id_or_email->comment_type ) && ! in_array( $id_or_email->comment_type, (array) $allowed_comment_types ) )
			return false;

		if ( ! empty( $id_or_email->user_id ) ) {
			$id = (int) $id_or_email->user_id;
			$user = get_userdata( $id );
			if ( $user )
				$email = $user->user_email;
		}

		if ( ! $email && ! empty( $id_or_email->comment_author_email ) )
			$email = $id_or_email->comment_author_email;
	} else {
		$email = $id_or_email;
	}

	if ( empty( $default ) ) {
		$avatar_default = get_option( 'avatar_default' );
		if ( empty( $avatar_default ) )
			$default = 'mystery';
		else
			$default = $avatar_default;
	}

	if ( !empty( $email ) )
		$email_hash = md5( strtolower( trim( $email ) ) );

	if ( is_ssl() ) {
		$host = 'https://secure.gravatar.com';
	} else {
		if ( !empty( $email ) )
			$host = sprintf( "http://%d.gravatar.com", ( hexdec( $email_hash[0] ) % 2 ) );
		else
			$host = 'http://0.gravatar.com';
	}

	if ( 'mystery' == $default )
		$default = "$host/avatar/ad516503a11cd5ca435acc9bb6523536?s={$size}"; // ad516503a11cd5ca435acc9bb6523536 == md5('unknown@gravatar.com')
	elseif ( 'blank' == $default )
		$default = $email ? 'blank' : includes_url( 'images/blank.gif' );
	elseif ( !empty( $email ) && 'gravatar_default' == $default )
		$default = '';
	elseif ( 'gravatar_default' == $default )
		$default = "$host/avatar/?s={$size}";
	elseif ( empty( $email ) )
		$default = "$host/avatar/?d=$default&amp;s={$size}";
	elseif ( strpos( $default, 'http://' ) === 0 )
		$default = add_query_arg( 's', $size, $default );

	if ( !empty( $email ) ) {
		$out = "$host/avatar/";
		$out .= $email_hash;
		$out .= '?s='.$size;
		$out .= '&amp;d=' . urlencode( $default );

		$rating = get_option( 'avatar_rating' );
		if ( !empty( $rating ) )
			$out .= "&amp;r={$rating}";

		$out = str_replace( '&#038;', '&amp;', esc_url( $out ) );
		$avatar = "<img alt='{$safe_alt}' src='{$out}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
	} else {
		$out = esc_url( $default );
		$avatar = "<img alt='{$safe_alt}' src='{$out}' class='avatar avatar-{$size} photo avatar-default' height='{$size}' width='{$size}' />";
	}

	/**
	 * Filter the avatar to retrieve.
	 *
	 * @since 2.5.0
	 *
	 * @param string  $avatar      Image tag for the user's avatar.
	 * @param int|object|string $id_or_email A user ID, email address, or comment object.
	 * @param int     $size        Square avatar width and height in pixels to retrieve.
	 * @param string  $alt         Alternative text to use in the avatar image tag.
	 *                                       Default empty.
	 */
	return apply_filters( 'get_avatar', $avatar, $id_or_email, $size, $default, $alt );
}

/**
 * Wrapper function for size_format - checks the max size of the avatar field.
 *
 * @since 1.0.0
 * @param array   $field
 * @param string  $size  in bytes
 * @return string
 */
function wpum_max_upload_size( $field_name ) {

	// Default max upload size
	$output = size_format( wp_max_upload_size() );

	// Check if the field is the avatar upload field and max size is defined
	if ( $field_name == 'user_avatar' && defined( 'WPUM_MAX_AVATAR_SIZE' ) )
		$output = size_format( WPUM_MAX_AVATAR_SIZE );

	return $output;
}

/**
 * Displays a button to check uploads folder permissions.
 *
 * @since 1.0.0
 * @return void
 */
function wpum_check_permissions_button() {

	$output = '<br/><br/>';
	$output .= '<a class="button" href="'.admin_url( 'users.php?page=wpum-settings&tab=profile&wpum_action=check_folder_permission' ).'">'.__( 'Verify upload permissions' ).'</a>';
	$output .= '<p class="description">'.__( 'Press the button above if avatar uploads does not work.' ).'</p>';

	return $output;

}

/**
 * Available templates for user directories.
 * Developers can use the filter wpum_get_directory_templates
 * to customize add and remove templates.
 *
 * @since 1.0.0
 * @return array $templates list of the templates - the key is the file name without extension.
 */
function wpum_get_directory_templates() {

	// Default template has empty key.
	$templates = array( '' => __( 'Default template' ) );

	return apply_filters( 'wpum_get_directory_templates', $templates );

}

/**
 * List of fields to retrieve during the WP_User_Query for user directories.
 * Limiting the query to certain fields, speeds it up.
 *
 * @since 1.0.0
 * @see https://codex.wordpress.org/Class_Reference/WP_User_Query#Return_Fields_Parameter
 * @return array $fields - https://codex.wordpress.org/Class_Reference/WP_User_Query#Return_Fields_Parameter
 */
function wpum_get_user_query_fields() {

	$fields = array( 'ID', 'display_name', 'user_login', 'user_nicename', 'user_email', 'user_url', 'user_registered' );

	return apply_filters( 'wpum_get_user_query_fields', $fields );

}

/**
 * Checks whether a directory has a search form.
 *
 * @since 1.0.0
 * @param int     $directory_id the ID of a directory custom post type, post.
 * @return bool
 */
function wpum_directory_has_search_form( $directory_id = 0 ) {

	if ( get_post_meta( $directory_id, 'display_search_form', true ) )
		return true;

	return false;

}

/**
 * Checks whether a directory has a custom template.
 *
 * @since 1.0.0
 * @param int     $directory_id the ID of a directory custom post type, post.
 * @return bool|string Boolean if no custom template is assigned.
 *                     Returns template name excluding extension if has custom template.
 */
function wpum_directory_has_custom_template( $directory_id = 0 ) {

	$custom_template = get_post_meta( $directory_id, 'directory_template', true );

	if ( !empty( $custom_template ) )
		return $custom_template;

	return false;
}

/**
 * Grabs the amount of users to display into the directory.
 *
 * @since 1.0.0
 * @param int     $directory_id the ID of a directory custom post type, post.
 * @return string amount of users to display.
 */
function wpum_directory_profiles_per_page( $directory_id = 0 ) {

	$amount = get_post_meta( $directory_id, 'profiles_per_page', true );

	if ( empty( $amount ) )
		return 10;

	return $amount;

}

/**
 * Grabs user roles for the directory if any.
 *
 * @since 1.0.0
 * @param int     $directory_id the ID of a directory custom post type, post.
 * @return array|bool list of roles or false if no roles is set.
 */
function wpum_directory_get_roles( $directory_id = 0 ) {

	$roles = get_post_meta( $directory_id, 'directory_roles', true );

	if ( empty( $roles ) || !is_array( $roles ) )
		return false;

	return $roles;

}

/**
 * Grabs excluded users for the directory if any.
 *
 * @since 1.0.0
 * @param int     $directory_id the ID of a directory custom post type, post.
 * @return array|bool list of excluded users ids or false if no ids are set.
 */
function wpum_directory_get_excluded_users( $directory_id = 0 ) {

	$users = get_post_meta( $directory_id, 'excluded_ids', true );

	// Process string to array
	if ( $users ) {

		$list = explode( ',', $users );
		$users = $list;

	} else {
		$users = false;
	}

	return $users;

}

/**
 * Grabs the currently selected sorting method for the directory
 *
 * @since 1.0.0
 * @param int     $directory_id the ID of a directory custom post type, post.
 * @return string|bool sorting method or false if no ids are set.
 */
function wpum_directory_get_sorting_method( $directory_id = 0 ) {

	$method = get_post_meta( $directory_id, 'default_sorting_method', true );

	return $method;

}

/**
 * Produces the list of sorting methods.
 * Developers can use the filter wpum_get_directory_sorting_methods
 * to add new methods.
 *
 * @since 1.0.0
 * @return array list sorting methods.
 */
function wpum_get_directory_sorting_methods() {

	// Let's add the default sorting methods
	$methods = array(
		'user_nicename' => __( 'By nickname' ),
		'newest'        => __( 'Newest users first' ),
		'oldest'        => __( 'Oldest users first' ),
		'name'          => __( 'First name' ),
		'last_name'     => __( 'Last Name' )
	);

	return apply_filters( 'wpum_get_directory_sorting_methods', $methods );

}

/**
 * Produces the list of results per page options.
 * Developers can use the filter wpum_get_directory_amount_options
 * to add new options.
 *
 * @since 1.0.0
 * @return array list amount options.
 */
function wpum_get_directory_amount_options() {

	// Let's add the default results options
	$amounts = array(
		''   => '',
		'10' => '10',
		'15' => '15',
		'20' => '20',
	);

	return apply_filters( 'wpum_get_directory_amount_options', $amounts );

}

/**
 * Checks whether a directory has the sorting form enabled.
 *
 * @since 1.0.0
 * @param int     $directory_id the ID of a directory custom post type, post.
 * @return bool
 */
function wpum_directory_display_sorter( $directory_id = 0 ) {

	if ( get_post_meta( $directory_id, 'display_sorter', true ) )
		return true;

	return false;

}

/**
 * Checks whether a directory has the amount results form enabled.
 *
 * @since 1.0.0
 * @param int     $directory_id the ID of a directory custom post type, post.
 * @return bool
 */
function wpum_directory_display_amount_sorter( $directory_id = 0 ) {

	if ( get_post_meta( $directory_id, 'display_amount', true ) )
		return true;

	return false;

}

/**
 * Generates core pages and updates settings panel with the newly created pages.
 *
 * @since 1.0.0
 * @return void
 */
function wpum_generate_pages( $redirect = false ) {

	// Generate login page
	if ( ! wpum_get_option( 'login_page' ) ) {

		$login = wp_insert_post(
			array(
				'post_title'     => __( 'Login' ),
				'post_content'   => '[wpum_login_form]',
				'post_status'    => 'publish',
				'post_author'    => 1,
				'post_type'      => 'page',
				'comment_status' => 'closed'
			)
		);

		wpum_update_option( 'login_page', $login );

	}

	// Generate password recovery page
	if ( ! wpum_get_option( 'password_recovery_page' ) ) {

		$psw = wp_insert_post(
			array(
				'post_title'     => __( 'Password Reset' ),
				'post_content'   => '[wpum_password_recovery form_id="" login_link="yes" psw_link="no" register_link="yes" ]',
				'post_status'    => 'publish',
				'post_author'    => 1,
				'post_type'      => 'page',
				'comment_status' => 'closed'
			)
		);

		wpum_update_option( 'password_recovery_page', $psw );

	}

	// Generate password recovery page
	if ( ! wpum_get_option( 'registration_page' ) ) {

		$register = wp_insert_post(
			array(
				'post_title'     => __( 'Register' ),
				'post_content'   => '[wpum_register form_id="" login_link="yes" psw_link="yes" register_link="no" ]',
				'post_status'    => 'publish',
				'post_author'    => 1,
				'post_type'      => 'page',
				'comment_status' => 'closed'
			)
		);

		wpum_update_option( 'registration_page', $register );

	}

	// Generate account page
	if ( ! wpum_get_option( 'account_page' ) ) {

		$account = wp_insert_post(
			array(
				'post_title'     => __( 'Account' ),
				'post_content'   => '[wpum_account]',
				'post_status'    => 'publish',
				'post_author'    => 1,
				'post_type'      => 'page',
				'comment_status' => 'closed'
			)
		);

		wpum_update_option( 'account_page', $account );

	}

	// Generate password recovery page
	if ( ! wpum_get_option( 'profile_page' ) ) {

		$profile = wp_insert_post(
			array(
				'post_title'     => __( 'Profile' ),
				'post_content'   => '[wpum_profile]',
				'post_status'    => 'publish',
				'post_author'    => 1,
				'post_type'      => 'page',
				'comment_status' => 'closed'
			)
		);

		wpum_update_option( 'profile_page', $profile );

	}

	if ( $redirect ) {
		wp_redirect( admin_url( 'users.php?page=wpum-settings&tab=general&setup_done=true' ) );
		exit;
	}
}

/**
 * Generates tabs for the account page.
 * Tabs are needed to split content in multiple parts,
 * and not produce a very long form.
 *
 * @since 1.0.0
 * @todo  sort by priority for addon.
 * @return void
 */
function wpum_get_account_page_tabs() {

	$tabs = array();

	$tabs['details'] = array(
		'id'    => 'details',
		'title' => __('Edit Account'),
	);
	$tabs['change-password'] = array(
		'id'    => 'change-password',
		'title' => __('Change Password'),
	);

	return apply_filters( 'wpum_get_account_page_tabs', $tabs );

}

/**
 * Generates url of a single account tab.
 *
 * @since 1.0.0
 * @return string $tab_url url of the tab.
 */
function wpum_get_account_tab_url( $tab ) {

	if( get_option( 'permalink_structure' ) == '' ) :
		$tab_url = add_query_arg( 'account_tab', $tab, wpum_get_core_page_url( 'account' ) );
	else :
		$tab_url = wpum_get_core_page_url( 'account' ) . $tab;
	endif;

	return $tab_url;

}

/**
 * Checks the current active account tab (if any).
 *
 * @since 1.0.0
 * @return bool|string
 */
function wpum_get_current_account_tab() {

	$tab = ( get_query_var( 'account_tab' ) ) ? get_query_var( 'account_tab' ) : null;
	return $tab;

}

/**
 * Checks the given account tab is registered.
 *
 * @since 1.0.0
 * @param string  $tab the key value of the array in wpum_get_account_page_tabs() must match slug
 * @return bool
 */
function wpum_account_tab_exists( $tab ) {

	$exists = false;

	if ( array_key_exists( $tab, wpum_get_account_page_tabs() ) )
		$exists = true;

	return $exists;

}

/**
 * Get a given field by meta.
 *
 * @since 1.0.0
 * @param string $meta meta parameter from an array in wpum_default_fields_list() function.
 * @return array $custom_field
 */
function wpum_get_field_by_meta( $meta = null ) {

	$all_fields = wpum_default_fields_list();
	$custom_field = array();

	if( $meta )
		$custom_field = $all_fields[ $meta ];

	return $custom_field;

}

/**
 * Get a given field by meta.
 *
 * @since 1.0.0
 * @param string $meta meta parameter from an array in wpum_default_fields_list() function.
 * @return bool|array $options false if no options.
 */
function wpum_get_field_options( $meta = null ) {

	$options = false;
	$custom_field = wpum_get_field_by_meta( $meta );

	if( array_key_exists( 'options', $custom_field ) && !empty( $custom_field['options'] ) )
		$options = $custom_field['options'];

	return $options;
}

/**
 * Display fields editor in admin panel.
 *
 * @since 1.0.0
 * @param string $id meta parameter of a field from the array in wpum_default_fields_list() function.
 * @return mixed $output
 */
function wpum_display_fields_editor( $id ) {

	$field         = wpum_get_field_by_meta( $id );
	$field_options = wpum_get_field_options( $id );

	$output = '<tr id="wpum-edit-field-'.esc_attr($id).'" class="wpum-fields-editor field-'.esc_attr($id).'">';
		$output .= '<td colspan="5">';
			$output .= '<div id="postbox-'.esc_attr($id).'" class="postbox wpum-editor-postbox">';
				$output .= '<h3 class="hndle ui-sortable-handle"><span>'. sprintf( __( 'Editing "%s" field.' ), $field['title'] ) .'</span></h3>';
					$output .= '<div class="inside">';

					// Generate options if any
					if( $field_options ) {
						foreach ($field_options as $key => $option) {

							$output .= '<div class="wpum-editor-field">';

							// Check field type - generate option accordingly
							switch ( $option['type'] ) {
								case 'text':
									$output .= WPUM()->html->$option['type']( 
										array( 
											'name'  => esc_attr( $option['name'] ),
											'label' => esc_html( $option['label'] ),
											'desc' => $option['desc']
										)
									);
									break;
								case 'select':
									$output .= WPUM()->html->$option['type']( 
										array( 
											'name'  => esc_attr( $option['name'] ),
											'label' => esc_html( $option['label'] ),
											'options' => $option['choices'],
											'show_option_all'  => false,
											'show_option_none' => false,
											'desc' => $option['desc']
										)
									);
									break;
								case 'checkbox':
									$output .= WPUM()->html->$option['type']( 
										array( 
											'name'  => esc_attr( $option['name'] ),
											'label' => esc_html( $option['label'] ),
											'desc' => $option['desc']
										)
									);
									break;
							}

							$output .= '</div>';

						}
					} else {
						$output .= __( 'This field has no options.' );
					} // End options generation

					$output .= '<div id="major-publishing-actions">';
						$output .= '<div id="delete-action">';
							$output .= '<a class="button wpum-cancel-field" href="">'.__('Cancel').'</a>';
						$output .= '</div>';

						$output .= '<div id="publishing-action">';
							$output .= '<a class="button-primary wpum-save-field" href="">'.__('Update field').'</a>';
						$output .= '</div>';
						$output .= '<div class="clear"></div>';
					$output .= '</div>';
			$output .= '</div>';
		$output .= '</td>';

	$output .= '</tr>';

	return $output;

}
