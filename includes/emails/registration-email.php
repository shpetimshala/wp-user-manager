<?php
/**
 * Registration Email
 *
 * @package     wp-user-manager
 * @author      Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_Registration_Email Class
 * This class registers a new email for the editor.
 * 
 * @since 1.0.0
 */
class WPUM_Registration_Email extends WPUM_Emails {
	
	/**
	 * This function sets up a custom email.
	 *
	 * @since 1.0.0
	 * @return  void
	 */
	function __construct() {
		
		// Configure Email
		$this->name        = 'register';
		$this->title       = __( "Registration Email" );
		$this->description = __( "This is the email that is sent to the user upon successful registration." );
		$this->subject     = $this->subject();
		$this->message     = $this->message();
		
		// do not delete!
    	parent::__construct();
	}

	/**
	 * The default subject of the email.
	 *
	 * @since 1.0.0
	 * @return  void
	 */
	function subject() {

		$subject = sprintf( __('Your %s Account'), get_option( 'blogname' ) );

		return apply_filters( "wpum/email/subject={$this->name}", $subject );

	}

	/**
	 * The default message of the email.
	 *
	 * @since 1.0.0
	 * @return  void
	 */
	function message() {

		$message = 'Hello {username},

Welcome to {sitename},

These are your account details

Username: {username},
Password: {password}';
		
		return apply_filters( "wpum/email/message={$this->name}", $message );

	}
	
}

new WPUM_Registration_Email();