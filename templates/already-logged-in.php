<?php
/**
 * WPUM Template: Already Logged In.
 *
 * Displays a message telling the user he is already logged in.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

global $current_user;
get_currentuserinfo();

?>

<?php do_action( 'wpum_before_already_loggedin_template', $current_user, $args ); ?>

<div id="wpum-form-<?php echo $args['form_id'];?>" class="wpum-login-form loggedin">

	<?php do_action( 'wpum_before_inside_already_loggedin_template', $current_user, $args ); ?>

	<p><?php printf( __('Your are currently logged in as %s. <a href="%s">Logout &raquo;</a>'), $current_user->display_name, wpum_logout_url() );?></p>

	<?php do_action( 'wpum_after_inside_already_loggedin_template', $current_user, $args ); ?>

</div>

<?php do_action( 'wpum_after_already_loggedin_template', $current_user, $args ); ?>