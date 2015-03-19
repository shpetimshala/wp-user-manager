<?php
/**
 * WPUM Template: Guests Warning.
 * Displays a message telling the user that guests cannot access this page.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */
?>
<div id="wpum-guests-disabled" class="wpum-message notice">

	<p><?php printf( __('This content is available to members only. Please <a href="%s">login</a> or <a href="%s">register</a> to view this area.'), wpum_get_core_page_url('login'), wpum_get_core_page_url('register')  );?></p>

</div>