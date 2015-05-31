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

		if ( !empty( $custom_redirect ) ) {
			$redirect = esc_url( $custom_redirect );
		} else if ( wpum_get_option( 'logout_redirect' ) ) {
				$redirect = esc_url( get_permalink( wpum_get_option( 'logout_redirect' ) ) );
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

		$label = __( 'Username' );

		if ( wpum_get_option( 'login_method' ) == 'email' ) {
			$label = __( 'Email' );
		} else if ( wpum_get_option( 'login_method' ) == 'username_email' ) {
				$label = __( 'Username or email' );
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
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

		// Send notification to admin if not disabled.
		if ( !wpum_get_option( 'disable_admin_register_email' ) ) {
			$message  = sprintf( __( 'New user registration on your site %s:' ), $blogname ) . "\r\n\r\n";
			$message .= sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
			$message .= sprintf( __( 'E-mail: %s' ), $user->user_email ) . "\r\n";
			wp_mail( get_option( 'admin_email' ), sprintf( __( '[%s] New User Registration' ), $blogname ), $message );
		}

		/* == Send notification to the user now == */

		if ( empty( $plaintext_pass ) )
			return;

		// Check if email exists first
		if ( wpum_email_exists( 'register' ) ) {

			// Retrieve the email from the database
			$register_email = wpum_get_email( 'register' );

			$message = wpautop( $register_email['message'] );
			$message = wpum_do_email_tags( $message, $user_id, $plaintext_pass );

			WPUM()->emails->__set( 'heading', __( 'Your account', 'wpum' ) );
			WPUM()->emails->send( $user->user_email, $register_email['subject'], $message );

		}

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
			'label_password' => __( 'Password' ),
			'label_remember' => __( 'Remember Me' ),
			'label_log_in'   => __( 'Login' ),
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
		if ( is_user_logged_in() ) :

			get_wpum_template( 'already-logged-in.php', array( 'args' => $args ) );

		// Show login form if not logged in
		else :

			get_wpum_template( 'forms/login-form.php', array( 'args' => $args ) );

		// Display helper links
		do_action( 'wpum_do_helper_links', $args['login_link'], $args['register_link'], $args['psw_link'] );

		endif;

	}
endif;

if ( ! function_exists( 'wpum_directory_sort_dropdown' ) ) :
	/**
	 * Display or retrieve the HTML dropdown list of sorting options.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param string|array $args Optional. Override default arguments.
	 * @return string HTML.
	 */
	function wpum_directory_sort_dropdown( $args = '' ) {

		$defaults = array(
			'exclude'  => '',
			'selected' => '',
			'class' => 'wpum-dropdown-sort',
		);

		$args = wp_parse_args( $args, $defaults );

		// Get css class
		$class = $args['class'];

		// Get options
		$sorting_methods = wpum_get_directory_sorting_methods();

		// Exclude methods if any
		if ( !empty( $args['exclude'] ) ) {

			// Check if it's only one value that we need to exclude
			if ( is_string( $args['exclude'] ) ) :

				unset( $sorting_methods[ $args['exclude'] ] );

			// Check if there's more than one value to exclude
			elseif ( is_array( $args['exclude'] ) ) :

				foreach ( $args['exclude'] as $method_to_exclude ) {
					unset( $sorting_methods[ $method_to_exclude ] );
				}

			endif;

		}

		$sorting_methods = apply_filters( 'wpum_sort_dropdown_methods', $sorting_methods, $args );
		$selected = isset( $_GET['sort'] ) ? $selected = $_GET['sort'] : $selected = $args['selected'];

		$output = "<select name='wpum-dropdown' id='wpum-dropdown' class='$class'>\n";

		foreach ( $sorting_methods as $value => $label ) {

			$method_url = add_query_arg( array( 'sort' => $value ), get_permalink() );

			if ( $selected == $value ) {
				$output .= "\t<option value='" . esc_url( $method_url ) . "' selected='selected' >$label</option>\n";
			} else {
				$output .= "\t<option value='" . esc_url( $method_url ) . "'>$label</option>\n";
			}

		}

		$output .= "</select>\n";

		return $output;

	}
endif;

if ( ! function_exists( 'wpum_directory_results_amount_dropdown' ) ) :
	/**
	 * Display or retrieve the HTML dropdown list of results amount options.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param string|array $args Optional. Override default arguments.
	 * @return string HTML content.
	 */
	function wpum_directory_results_amount_dropdown( $args = '' ) {

		$defaults = array(
			'exclude'  => '',
			'class' => 'wpum-results-dropdown-sort',
		);

		$args = wp_parse_args( $args, $defaults );

		// Get css class
		$class = $args['class'];

		// Get options
		$results_options = wpum_get_directory_amount_options();
		$selected = isset( $_GET['amount'] ) ? $_GET['amount'] : false;

		$output = "<select name='wpum-amount-dropdown' id='wpum-amount-dropdown' class='$class'>\n";

		foreach ( $results_options as $value => $label ) {

			$result_url = add_query_arg( array( 'amount' => $value ), get_permalink() );

			if ( $selected == $value ) {
				$output .= "\t<option value='" . esc_url( $result_url ) . "' selected='selected' >$label</option>\n";
			} else {
				$output .= "\t<option value='" . esc_url( $result_url ) . "'>$label</option>\n";
			}
		}

		$output .= "</select>\n";

		return $output;

	}
endif;