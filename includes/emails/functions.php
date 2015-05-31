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