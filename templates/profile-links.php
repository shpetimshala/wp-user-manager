<?php
/**
 * WPUM Template: User profile links.
 * Displays links related to the user.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */
?>
<ul class="wpum-user-links">
	
	<?php do_action( 'wpum_top_user_profile_links',  $user_data ); ?>

	<li class="wpum-profile-link view-profile">
		<a href="<?php echo wpum_get_user_profile_url( $user_data ); ?>"><?php _e('View Profile'); ?></a>
	</li>
	<li class="wpum-profile-link send-email">
		<a href="mailto:<?php echo antispambot( $user_data->user_email );?>" class="wpum-button"><?php _e('Send Email');?></a>
	</li>
	<?php if( !empty( $user_data->user_url ) ) : ?>
	<li class="wpum-profile-link view-website">
		<a href="<?php echo esc_url( $user_data->user_url );?>" class="wpum-button" rel="nofollow" target="_blank"><?php _e('Visit website');?></a>
	</li>
	<?php endif; ?>

	<?php do_action( 'wpum_bottom_user_profile_links',  $user_data ); ?>
	
</ul>