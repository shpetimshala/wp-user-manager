<?php
/**
 * WP User Manager Permalinks
 *
 * @package     wp-user-manager
 * @copyright   Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_Permalinks Class
 *
 * @since 1.0.0
 */
class WPUM_Permalinks {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		add_action('init', array( $this, 'profile_rewrite_rules' ) );
		
	}

	/**
	 * Adds new rewrite rules to create pretty permalinks for users profiles.
	 *
	 * @access public
	 * @since 1.0.0
	 * @global object $wp
	 * @return void
	 */
	public function profile_rewrite_rules() {

		global $wp; 
	    
	    // Define args
	    $wp->add_query_var('args');   
	    $wp->add_query_var('arg_username');
	    
	    // Add rewrite rule
	    add_rewrite_rule('profile/([0-9]+)/([^/]*)/page/([0-9]+)','index.php?pagename=profile&args=$matches[1]&arg_username=$matches[2]&paged=$matches[3]','top');
	    add_rewrite_rule('profile/([0-9]+)/([^/]*)','index.php?pagename=profile&args=$matches[1]&arg_username=$matches[2]','top');

	}

}

return new WPUM_Permalinks;