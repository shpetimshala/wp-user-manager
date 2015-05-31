<?php
/**
 * Handles Emails Functions
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Gets the default registration mail subject.
 *
 * @since 1.0.0
 * @return string - The default mail subject.
 */
function wpum_default_register_mail_subject() {
		
	$subject = sprintf( __('Your %s Account'), get_option( 'blogname' ) );

	return apply_filters( 'wpum_default_register_mail_subject', $subject );

}

/**
 * Gets the default registration mail message.
 *
 * @since 1.0.0
 * @return string - The default mail message.
 */
function wpum_default_register_mail_message() {
		
	$message = 'Hello {username},

Welcome to {sitename},

These are your account details

Username: {username},
Password: {password}';
		
	return apply_filters( 'wpum_default_register_mail_message', $message );

}

/**
 * Gets the default password recovery mail subject.
 *
 * @since 1.0.0
 * @return string - The default mail subject.
 */
function wpum_default_password_mail_subject() {
		
	$subject = sprintf( __('Reset Your %s Password'), get_option( 'blogname' ) );

	return apply_filters( 'wpum_default_password_mail_subject', $subject );

}

/**
 * Gets the default password recovery mail message.
 *
 * @since 1.0.0
 * @return string - The default mail message.
 */
function wpum_default_password_mail_message() {
		
	$message = 'Hello {username},

You are receiving this message because you or somebody else has attempted to reset your password on {sitename}.

If this was a mistake, just ignore this email and nothing will happen.

To reset your password, visit the following address:

<a href="{recovery_url}">{recovery_url}</a>
';
		
	return apply_filters( 'wpum_default_password_mail_message', $message );

}

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