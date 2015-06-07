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
 * WPUM_password_Email Class
 * This class registers a new email for the editor.
 * 
 * @since 1.0.0
 */
class WPUM_password_Email extends WPUM_Emails {
	
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
	public static function subject() {

		$subject = sprintf( __('Reset Your %s Password'), get_option( 'blogname' ) );

		return $subject;

	}

	/**
	 * The default message of the email.
	 *
	 * @since 1.0.0
	 * @return  void
	 */
	public static function message() {

		$message = __( "Hello {username}, \n\n" );
		$message .= __( "You are receiving this message because you or somebody else has attempted to reset your password on {sitename}.\n\n" );
		$message .= __( "If this was a mistake, just ignore this email and nothing will happen.\n\n" );
		$message .= __( "To reset your password, visit the following address:\n\n" );
		$message .= __( "{recovery_url}" );
		
		return $message;

	}
	
}

new WPUM_password_Email();