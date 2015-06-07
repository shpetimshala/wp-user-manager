<?php
/**
 * WP User Manager: Fields Editor
 *
 * @package     wp-user-manager
 * @author      Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_Fields_Editor Class
 *
 * @since 1.0.0
 */
class WPUM_Fields_Editor {

	/**
	 * Holds the editor page id.
	 *
	 * @since 1.0.0
	 */
	const hook = 'users_page_wpum-profile-fields';

	/**
	 * The Database Abstraction
	 *
	 * @since  1.0.0
	 */
	protected $db;

	/**
	 * Holds the group id.
	 *
	 * @since 1.0.0
	 */
	var $group_id = null;

	/**
	 * Holds the group.
	 *
	 * @since 1.0.0
	 */
	var $group = null;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		$this->db = new WPUM_DB_Field_Groups;

		// Detect if a group is being edited
		if( isset( $_GET['group'] ) && is_numeric( $_GET['group'] ) )
			$this->groupd_id = intval( $_GET['group'] );

		// Get selected group - set it as primary if no group is selected
		if( isset( $_GET['group'] ) && is_numeric( $_GET['group'] ) ) {

			// Get primary group
			$this->group = $this->db->get_group_by( 'id', $_GET['group'] );

		} else {

			// Get primary group
			$this->group = $this->db->get_group_by( 'primary' );

		}

		// loads metaboxes functions
		add_action( 'load-'.self::hook, array( $this, 'load_editor' ) );
		add_action( 'add_meta_boxes_'.self::hook, array( $this, 'add_meta_box' ) );
		add_action( 'admin_footer-'.self::hook, array( $this, 'print_script_in_footer' ) );

		// Append group saving process
		add_action( 'wpum_edit_group', array( $this, 'process_group' ) );

		// Load WP_List_Table
		if( ! class_exists( 'WP_List_Table' ) ) {
		    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
		}

		// Extedn WP_List_Table
		require_once WPUM_PLUGIN_DIR . 'includes/admin/fields/class-wpum-groups-fields.php';

	}

	/**
	 * Handles the display of the editor page in the backend.
	 *
	 * @access public
	 * @return void
	 */
	public static function editor_page() {

		ob_start();
		
		?>

		<div class="wrap wpum-fields-editor-wrap">

			<h2 class="wpum-page-title">
				<?php _e( 'WP User Manager - Fields Editor' ); ?>
				<?php do_action( 'wpum/fields/editor/title' ); ?>
			</h2>

			<?php echo self::navbar(); ?>

			<?php echo self::primary_message(); ?>

			<div id="nav-menus-frame">

				<!-- Sidebar -->
				<div id="menu-settings-column" class="metabox-holder">
					
					<div class="clear"></div>

					<?php do_accordion_sections( self::hook, 'side', null ); ?>
						
				</div>
				<!-- End Sidebar -->

				<div id="menu-management-liquid" class="wpum-editor-container">
					<?php echo self::group_table(); ?>
				</div>

			</div>

		</div>

		<?php
		
		echo ob_get_clean();

	}

	/**
	 * Displays the groups navigation bar
	 *
	 * @access private
	 * @return void
	 */
	private static function navbar() {

		// Get all groups
		$groups = WPUM()->field_groups->get_groups( array( 'order' => 'ASC' ) );

		if( empty( $groups ) ) :

			$output = '<div class="message error"><p>';
				$output .= __('It seems you do not have any field groups. Please deactivate and re-activate the plugin.');
			$output .= '</p></div>';

		else:  
			$output = '<div class="wp-filter">';
				$output .= '<form method="get" action="'. admin_url( 'users.php?page=wpum-profile-fields' ) .'">';

					$output .= '<input type="hidden" name="page" value="wpum-profile-fields">';
					$output .= '<input type="hidden" name="action" value="edit">';

					// Get all groups into an array for the dropdown menu.
					$options = array();
					foreach ( $groups as $key => $group ) {
						$options += array( $group->id => $group->name );
					}
		 
					// Generate dropdown menu
					$args = array(
						'options'          => $options,
						'label'            => __('Select a field group to edit:'),
						'id'               => 'wpum-group-selector',
						'name'             => 'group',
						'selected'         => ( isset( $_GET['group'] ) && is_numeric( $_GET['group'] ) ) ? (int) $_GET['group'] : false,
						'multiple'         => false,
						'show_option_all'  => false,
						'show_option_none' => false
					);

					$output .= '<p>' . WPUM()->html->select( $args );
						$output .= '<span class="submit-btn"><input type="submit" class="button-secondary" value="'.__('Select').'"></span>';
					$output .= '</p>';

				$output .= '</form>';

			$output .= '</div>';
		endif;

		return $output;

	}

	/**
	 * Displays the table to manage each single group.
	 *
	 * @access private
	 * @return void
	 */
	private static function group_table() {

		$custom_fields_table = new WPUM_Groups_Fields();
		$custom_fields_table->prepare_items();
		$custom_fields_table->display();
		
		wp_nonce_field( 'wpum_fields_editor' );

	}

	/**
	 * Trigger the add_meta_boxes hooks to allow meta boxes to be added.
	 *
	 * @access public
	 * @return void
	 */
	public function load_editor() {
 
	    do_action( 'add_meta_boxes_'.self::hook, null );
	    do_action( 'add_meta_boxes', self::hook, null );
	 
	    /* Enqueue WordPress' script for handling the meta boxes */
	    wp_enqueue_script('postbox');

	    // Process group settings update
	    $this->process_group();

	}
	/**
	 * Register metaboxes.
	 *
	 * @access public
	 * @return void
	 */
	public function add_meta_box() {
		add_meta_box( 'wpum_fields_editor_edit_group', __( 'Group Settings' ), array( $this, 'group_settings' ), self::hook, 'side' );
		add_meta_box( 'wpum_fields_editor_help', __( 'Fields Order' ), array( $this, 'help_text' ), self::hook, 'side' );
	}

	/**
	 * Content of the first metabox.
	 *
	 * @access public
	 * @return mixed content of the "how it works" metabox.
	 */
	public static function help_text( $current_menu = null ) {

		$output = '<p>';
			$output .= sprintf( __('Click and drag the %s button to change the order of the fields.'), '<span class="dashicons dashicons-sort"></span>');
		$output .= '</p>';

		echo $output;

	}

	/**
	 * Display a message about the primary group.
	 *
	 * @access private
	 */
	public static function primary_message() {

		if( isset( $_GET['group'] ) && !WPUM()->field_groups->is_primary( intval( $_GET['group'] ) ) )
			return;
		?>

		<p>
			<span class="dashicons dashicons-info"></span>
			<?php _e('Fields into this group will appear on the signup page.') ;?>
		</p>

		<?php
	}

	/**
	 * Display the interface to edit the group.
	 *
	 * @access private
	 */
	public function group_settings() {

		// Name Field Args
		$name_args = array(
			'name'         => 'name',
			'value'        => esc_html( $this->group->name ),
			'label'        => __('Group name'),
			'class'        => 'text',
		);

		// Description field args
		$description_args = array(
			'name'         => 'description',
			'value'        => esc_html( $this->group->description ),
			'label'        => __('Group description'),
			'class'        => 'textarea',
		);

		// Prepare delete url
		$delete_url = wp_nonce_url( add_query_arg( array( 'action' => 'delete', 'group' => (int) $this->group->id ), admin_url( 'users.php?page=wpum-profile-fields' ) ), 'delete', 'nonce' );

		?>

		<form method="post" action="<?php echo admin_url( 'users.php?page=wpum-profile-fields' ); ?>" id="wpum-group-settings-edit">

			<div class="wpum-group-settings">
				<?php echo WPUM()->html->text( $name_args ); ?>
				<?php echo WPUM()->html->textarea( $description_args ); ?>
			</div>

			<div id="major-publishing-actions">
				<div id="delete-action">
					<?php if( !$this->group->is_primary && $this->group->can_delete ) : ?>
						<a class="submitdelete deletion" href="<?php echo $delete_url; ?>"><?php _e('Delete Group'); ?></a>
					<?php endif; ?>
				</div>
				<div id="publishing-action">
					<input type="hidden" name="wpum-action" value="edit_group"/>
					<input type="hidden" name="group" value="<?php echo ( isset( $_GET['group'] ) ) ? (int) $_GET['group'] : (int) $this->group->id; ?>"/>
					<?php wp_nonce_field( 'wpum_group_settings' ); ?>
					<input type="submit" name="publish" id="publish" class="button button-primary button-large" value="<?php _e('Save Group Settings'); ?>">
				</div>
				<div class="clear"></div>
			</div>

		</form>

		<?php

	}

	/**
	 * Process the update of the group settings
	 *
	 * @access private
	 */
	public function process_group() {

		// Process the group delete action
		if( isset( $_GET['action'] ) && $_GET['action'] == 'delete' && isset( $_GET['group'] ) && is_numeric( $_GET['group'] ) ) {

			// nonce verification
			if ( ! wp_verify_nonce( $_GET['nonce'], 'delete' ) ) {
				return;
			}

			if( WPUM()->field_groups->delete( (int) $_GET['group'] ) ) {
				// Redirect now
				$admin_url = add_query_arg( array( 'message' => 'group_delete_success' ), admin_url( 'users.php?page=wpum-profile-fields' ) );
				wp_redirect( $admin_url );
				exit();
			}

		}

		// Check whether the group settings form has been submitted
		if( isset( $_POST['wpum-action'] ) && $_POST['wpum-action'] == 'edit_group' ) {

			// nonce verification
			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'wpum_group_settings' ) ) {
				return;
			}

			// bail if something is wrong
			if( !is_numeric( $_POST['group'] ) && !current_user_can( 'manage_options' ) )
				return;

			$args = array(
				'name'        => sanitize_text_field( $_POST['name'] ),
				'description' => wp_kses_post( $_POST['description'] )
			);

			WPUM()->field_groups->update( (int) $_POST['group'], $args );

			// Redirect now
			$admin_url = add_query_arg( array( 'message' => 'group_success' ), admin_url( 'users.php?page=wpum-profile-fields' ) );
			wp_redirect( $admin_url );
			exit();

		}

	}

	/**
	 * Print metabox scripts into the footer.
	 *
	 * @access public
	 * @return void
	 */
	public function print_script_in_footer() {
		?>
		<script>jQuery(document).ready(function(){ postboxes.add_postbox_toggles(pagenow); });</script>
		<?php
	}

}

new WPUM_Fields_Editor;
