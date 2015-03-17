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

		// Execute only on admin panel.
		if( is_admin() ) {
			add_action( 'admin_init', array( $this, 'add_new_permalink_settings' ) );
		}
		
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

	/**
	 * Adds new settings section to the permalink options page.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function add_new_permalink_settings() {
		// Add a section to the permalinks page
		add_settings_section( 'wpum-permalink', __( 'User profiles permalink base', 'wpum' ), array( $this, 'display_settings' ), 'permalink' );
	}

	/**
	 * Displays the new settings section into the permalinks page.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function display_settings() {

		$structures = wpum_get_permalink_structures();

		ob_start();
		?>

		<p><?php _e('These settings control the permalinks used for users profiles. These settings only apply when <strong>not using "default" permalinks above</strong>.'); ?></p>

		<table class="form-table">
			<tbody>
				<?php foreach ($structures as $key => $settings) : ?>
					<tr>
						<th>
							<label>
								<input name="user_permalink" type="radio" value="<?php echo $settings['name']; ?>" <?php checked( $settings['name'], null ); ?> />
								<?php echo $settings['label']; ?>
							</label>
						</th>
						<td>
							<code>
								<?php echo esc_url( get_permalink( wpum_get_option('profile_page') ) ); ?>
							</code>
						</td>
					</tr>

				<?php endforeach; ?>
			</tbody>
		</table>

		<?php
		echo ob_get_clean();
	} 

}

return new WPUM_Permalinks;