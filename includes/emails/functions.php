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
 * Gets list of registered emails.
 *
 * @since 1.0.0
 * @return array $emails - list of emails.
 */
function wpum_get_emails() {

	return apply_filters( 'wpum/get_emails', array() );

}

/**
 * Run this function to reset/install registered emails.
 * This function should be used of plugin installation
 * or on addons installation if the addon adds new emails.
 *
 * @since 1.0.0
 * @return void.
 */
function wpum_register_emails() {

	$emails = wpum_get_emails();
	$default_emails = array();

	foreach ( $emails as $id => $settings ) {

		$default_emails[ $id ] = array(
			'subject' => $settings['subject'],
			'message' => $settings['message'],
		);

	}

	update_option( 'wpum_emails', $default_emails );

}
