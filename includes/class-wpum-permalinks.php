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
			add_action( 'wpum_save_permalink_structure', array( $this, 'save_structure' ) );
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
		$saved_structure = get_option( 'wpum_permalink', 'user_id' );

		ob_start();
		?>

		<p><?php _e('These settings control the permalinks used for users profiles. These settings only apply when <strong>not using "default" permalinks above</strong>.'); ?></p>

		<table class="form-table">
			<tbody>
				<?php foreach ($structures as $key => $settings) : ?>
					<tr>
						<th>
							<label>
								<input name="user_permalink" type="radio" value="<?php echo $settings['name']; ?>" <?php checked( $settings['name'], $saved_structure ); ?> />
								<?php echo $settings['label']; ?>
							</label>
						</th>
						<td>
							<code>
								<?php echo wpum_get_core_page_url( 'profile' ); ?><?php echo $settings['sample']; ?>
							</code>
						</td>
					</tr>
				<?php endforeach; ?>
				<input type="hidden" name="wpum-action" value="save_permalink_structure"/>
			</tbody>
		</table>

		<?php
		echo ob_get_clean();
	}

	/**
	 * Saves the permalink structure.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function save_structure() {

		// Check everything is correct.
		if( ! is_admin() ) {
			return;
		}

		// Bail if no cap
		if( ! current_user_can( 'manage_options' ) ) {
			_doing_it_wrong( __FUNCTION__ , _x( 'You have no rights to access this page', '_doing_it_wrong error message' ), '1.0.0' );
			return;
		}

		// Check that the saved permalink method is one of the registered structures.
		if( isset( $_POST['user_permalink'] ) && array_key_exists( $_POST['user_permalink'] , wpum_get_permalink_structures() ) ) {
		
			$user_permalink = sanitize_text_field( $_POST['user_permalink'] );
			update_option( 'wpum_permalink', $user_permalink );

		}

	}

}

return new WPUM_Permalinks;