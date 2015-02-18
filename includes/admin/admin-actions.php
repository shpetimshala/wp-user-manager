<?php
/**
 * Admin Messages
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function wpum_add_links_to_settings_title() {
	echo '<a href="http://support.wp-user-manager.com" class="add-new-h2" target="_blank">'.__('Documentation').'</a>';
	echo '<a href="http://wp-user-manager.com/addons" class="add-new-h2" target="_blank">'.__('Add Ons').'</a>';
}
add_action('wpum_next_to_settings_title','wpum_add_links_to_settings_title');