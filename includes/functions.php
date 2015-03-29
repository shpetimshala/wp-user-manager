<?php
/**
 * Main Functions
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'wpum_get_login_methods' ) ) :
/**
 * Define login methods for options panel
 * 
 * @since 1.0.0
 * @access public
 * @return array
 */
function wpum_get_login_methods() {
	return apply_filters( 'wpum_get_login_methods', array(
		'username'       => __( 'Username only' ),
		'email'          => __( 'Email only' ),
		'username_email' => __( 'Username or Email' ),
	) );
}
endif;

if ( ! function_exists( 'wpum_get_psw_lengths' ) ) :
/**
 * Define login methods for options panel
 *
 * @since 1.0.0
 * @access public
 * @return array
 */
function wpum_get_psw_lengths() {
	return apply_filters( 'wpum_get_psw_lengths', array(
		''       => __( 'Disabled' ),
		'weak'   => __( 'Weak' ),
		'medium' => __( 'Medium' ),
		'strong' => __( 'Strong' ),
	) );
}
endif;

if ( ! function_exists( 'wpum_logout_url' ) ) :
/**
 * A simple wrapper function for the wp_logout_url function
 * 
 * The function checks whether a custom url has been passed,
 * if not, looks for the settings panel option,
 * defaults to wp_logout_url
 * 
 * @since 1.0.0
 * @access public
 * @return string
 */
function wpum_logout_url( $custom_redirect = null ) {
		
	$redirect = null;

	if( !empty($custom_redirect) ) {
		$redirect = esc_url($custom_redirect);
	} else if( wpum_get_option('logout_redirect') ) {
		$redirect = esc_url( get_permalink( wpum_get_option('logout_redirect') ) );
	}

	return wp_logout_url( apply_filters( 'wpum_logout_url', $redirect, $custom_redirect ) );

}
endif;

if ( ! function_exists( 'wpum_get_username_label' ) ) :
/**
 * Returns the correct username label on the login form
 * based on the selected login method.
 *
 * @since 1.0.0
 * @access public
 * @return string
 */
function wpum_get_username_label() {
		
	$label = __('Username');

	if( wpum_get_option('login_method') == 'email' ) {
		$label = __('Email');
	} else if( wpum_get_option('login_method') == 'username_email' ) {
		$label = __('Username or email');
	}

	return $label;

}
endif;

if ( ! function_exists( 'wp_new_user_notification' ) ) :
/**
 * Replaces the default wp_new_user_notification function of the core.
 * 
 * Email login credentials to a newly-registered user.
 * A new user registration notification is also sent to admin email.
 *
 * @since 1.0.0
 * @access public
 * @return void
 */
function wp_new_user_notification( $user_id, $plaintext_pass ) {
		
	$user = get_userdata( $user_id );

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	// Send notification to admin if not disabled.
	if( !wpum_get_option('disable_admin_register_email') ) {
		$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
		$message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
		$message .= sprintf(__('E-mail: %s'), $user->user_email) . "\r\n";
		wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);
	}

	/* == Send notification to the user now == */

	if ( empty($plaintext_pass) )
		return;

	// Check if email exists first
	if( wpum_email_exists('register') ) {

		// Retrieve the email from the database
		$register_email = wpum_get_email('register');

		$message = wpautop( $register_email['message'] );
		$message = wpum_do_email_tags( $message, $user_id, $plaintext_pass );

		WPUM()->emails->send( $user->user_email, $register_email['subject'], $message );

	}

}
endif;

if ( ! function_exists( 'wpum_get_login_page_url' ) ) :
/**
 * Returns the URL of the login page.
 * 
 * @since 1.0.0
 * @access public
 * @return string
 * @uses wpum_get_option() To retrieve the selected page ID
 * @uses get_permalink To retrieve permalink given an ID.
 */
function wpum_get_login_page_url() {
		
	$redirect = null;

	if( wpum_get_option('login_page') )
		$redirect = esc_url( get_permalink( wpum_get_option('login_page') ) );

	return $redirect;

}
endif;

if ( ! function_exists( 'wpum_get_password_recovery_page_url' ) ) :
/**
 * Returns the URL of the password recovery page.
 * 
 * @since 1.0.0
 * @access public
 * @return string
 * @uses wpum_get_option() To retrieve the selected page ID
 * @uses get_permalink To retrieve permalink given an ID.
 */
function wpum_get_password_recovery_page_url() {
		
	$redirect = null;

	if( wpum_get_option('password_recovery_page') )
		$redirect = esc_url( get_permalink( wpum_get_option('password_recovery_page') ) );

	return $redirect;

}
endif;

if ( ! function_exists( 'wpum_get_registration_page_url' ) ) :
/**
 * Returns the URL of the registration page.
 * 
 * @since 1.0.0
 * @access public
 * @return string
 * @uses wpum_get_option() To retrieve the selected page ID
 * @uses get_permalink To retrieve permalink given an ID.
 */
function wpum_get_registration_page_url() {
		
	$redirect = null;

	if( wpum_get_option('registration_page') )
		$redirect = esc_url( get_permalink( wpum_get_option('registration_page') ) );

	return $redirect;

}
endif;

if ( ! function_exists( 'wpum_get_account_page_url' ) ) :
/**
 * Returns the URL of the profile edit page.
 * 
 * @since 1.0.0
 * @access public
 * @return string
 * @uses wpum_get_option() To retrieve the selected page ID
 * @uses get_permalink To retrieve permalink given an ID.
 */
function wpum_get_account_page_url() {
		
	$redirect = null;

	if( wpum_get_option('account_page') )
		$redirect = esc_url( get_permalink( wpum_get_option('account_page') ) );

	return $redirect;

}
endif;

if ( ! function_exists( 'wpum_get_profile_page_url' ) ) :
/**
 * Returns the URL of the users profile page.
 * 
 * @since 1.0.0
 * @access public
 * @return string
 * @uses wpum_get_option() To retrieve the selected page ID
 * @uses get_permalink To retrieve permalink given an ID.
 */
function wpum_get_profile_page_url() {
		
	$redirect = null;

	if( wpum_get_option('profile_page') )
		$redirect = esc_url( get_permalink( wpum_get_option('profile_page') ) );

	return $redirect;

}
endif;

if ( ! function_exists( 'wpum_get_user_by_data' ) ) :
/**
 * Returns a wp user object containg user's data.
 * The user is retrieved based on the current permalink structure.
 * This function is currently used only through the wpum_profile shortcode.
 * If no data is set, returns currently logged in user data.
 * 
 * @since 1.0.0
 * @access public
 * @return object
 */
function wpum_get_user_by_data() {
	
	$user_data = null;
	$permalink_structure = get_option( 'wpum_permalink', 'user_id' );
	$who = ( get_query_var('user') ) ? get_query_var('user') : null;

	// Checks we are on the profile page
	if( is_page( wpum_get_core_page_id('profile') ) ) {

		// Verify the user isset
		if( $who ) {

			switch ( $permalink_structure ) {
				case 'user_id':
					$user_data = get_user_by( 'id', intval( get_query_var('user') ) );
					break;
				case 'username':
					$user_data = get_user_by( 'login', esc_attr( get_query_var('user') ) );
					break;
				case 'nickname':

					// WP_User_Query arguments
					$args = array (
						'search'         => esc_attr( get_query_var('user') ),
						'search_columns' => array( 'user_nicename' ),
					);

					// The User Query
					$user_query = new WP_User_Query( $args );
					$user_query = $user_query->get_results();

					$user_data = $user_query[0];
					break;
				default:
					$user_data = apply_filters( "wpum_get_user_by_data", $permalink_structure, $who );
					break;
			}

		} else {

			$user_data = get_user_by( 'id', get_current_user_id() );

		}

	}

	return $user_data;

}
endif;

if ( ! function_exists( 'wpum_get_user_profile_url' ) ) :
/**
 * Returns the URL of the single user profile page.
 * 
 * @since 1.0.0
 * @access public
 * @param object $user_data WP_User Object.
 * @see https://codex.wordpress.org/Function_Reference/get_user_by
 * @return string
 */
function wpum_get_user_profile_url( $user_data ) {
		
	$url = null;
	
	$permalink_structure = get_option( 'wpum_permalink', 'user_id' );
	$base_url = wpum_get_core_page_url( 'profile' );

	if( empty( $base_url ) || !is_object( $user_data ) )
		return;

	// Define the method needed to grab the user url.
	switch ( $permalink_structure ) {
		case 'user_id':
			$url = $base_url . $user_data->ID;
			break;
		case 'username':
			$url = $base_url . $user_data->user_login;
			break;
		case 'nickname':
			$url = $base_url . $user_data->user_nicename;
			break;
		default:
			$url = apply_filters( 'wpum_get_user_profile_url', $user_data, $permalink_structure );
			break;
	}

	return esc_url( $url );

}
endif;

if ( ! function_exists( 'wpum_get_user_profile_tabs' ) ) :
/**
 * Returns registered tabs for the user profile page.
 * 
 * @since 1.0.0
 * @access public
 * @return string
 */
function wpum_get_user_profile_tabs() {

	$tabs = array();

	$tabs['about'] = array( 
		'id'       => 'profile_details',
		'title'    => __( 'Overview' ),
		'slug'     => 'about',
	);

	$tabs['posts'] = array( 
		'id'       => 'profile_posts',
		'title'    => __( 'Posts' ),
		'slug'     => 'posts',
	);

	$tabs['comments'] = array( 
		'id'       => 'profile_comments',
		'title'    => __( 'Comments' ),
		'slug'     => 'comments',
	);

	// Remove tabs if they're not active
	if( !wpum_get_option('profile_posts') ) // remove posts tab
		unset($tabs['posts']);

	if( !wpum_get_option('profile_comments') ) // Remove comments tab
		unset($tabs['comments']);

	return apply_filters( 'wpum_get_user_profile_tabs', $tabs );

}
endif;

if ( ! function_exists( 'wpum_login_form' ) ) :
/**
 * Display login form.
 * 
 * @since 1.0.0
 * @access public
 * @return string
 */
function wpum_login_form( $args = array() ) {

	$defaults = array(
		'echo'           => true,
		'redirect'       => esc_url( get_permalink() ),
		'form_id'        => null,
		'label_username' => wpum_get_username_label(),
		'label_password' => __('Password'),
		'label_remember' => __('Remember Me'),
		'label_log_in'   => __('Login'),
		'id_username'    => 'user_login',
		'id_password'    => 'user_pass',
		'id_remember'    => 'rememberme',
		'id_submit'      => 'wp-submit',
		'login_link'     => 'yes',
		'psw_link'       => 'yes',
		'register_link'  => 'yes'
	);

	// Parse incoming $args into an array and merge it with $defaults
	$args = wp_parse_args( $args, $defaults );

	// Show already logged in message
	if( is_user_logged_in() ) :

		get_wpum_template( 'already-logged-in.php', array( 'args' => $args ) );

	// Show login form if not logged in
	else :

		get_wpum_template( 'forms/login-form.php', array( 'args' => $args ) );

		// Display helper links
		do_action( 'wpum_do_helper_links', $args['login_link'], $args['register_link'], $args['psw_link'] );

	endif;

}
endif;