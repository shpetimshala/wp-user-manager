<?php
/**
 * Password Recovery Email
 *
 * @package     wp-user-manager
 * @author      Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_PSW_Recovery_Email Class
 * This class registers a new email for the editor.
 * 
 * @since 1.0.0
 */
class WPUM_PSW_Recovery_Email extends WPUM_Emails {
	
	/**
	 * This function sets up a custom email.
	 *
	 * @since 1.0.0
	 * @return  void
	 */
	function __construct() {
		
		// Configure Email
		$this->name        = 'password';
		$this->title       = __( "Password Recovery Email" );
		$this->description = __( "This is the email that is sent to the visitor upon password reset request." );
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

		$subject = sprintf( __('Reset Your %s Password'), get_option( 'blogname' ) );

		return apply_filters( 'wpum_default_password_mail_subject', $subject );

	}

	/**
	 * The default message of the email.
	 *
	 * @since 1.0.0
	 * @return  void
	 */
	function message() {

		$message = 'Hello {username},

You are receiving this message because you or somebody else has attempted to reset your password on {sitename}.

If this was a mistake, just ignore this email and nothing will happen.

To reset your password, visit the following address:

{recovery_url}
';
		
		return apply_filters( 'wpum_default_password_mail_message', $message );

	}
	
}

new WPUM_PSW_Recovery_Email();