<?php
/**
 * WPUM Template: Mini Profile.
 * Displays a preview of the user profile.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

$who = (get_query_var('user')) ? get_query_var('user') : 0;

?>

<div class="wpum-single-profile">

	<div class="wpum-user-avatar wpum-left">
		<div class="wpum-avatar-img">
			<?php echo get_avatar( $user_data->ID , 64 ); ?>
		</div>
		<div class="wpum-user-display-name">
			<?php echo $user_data->display_name; ?> 
		</div>
	</div>

	<div class="wpum-user-details wpum-right">
	Test <?php echo $who; echo $user_data->user_nicename; ?>
	</div>

	<div class="wpum-clearfix"></div>

</div>