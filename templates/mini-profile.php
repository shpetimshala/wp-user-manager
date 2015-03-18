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

// Display error message if no user has been found.
if( !is_object( $user_data ) ) {
	get_wpum_template( 'profile-not-found.php' );
	return;
}

?>

<div class="wpum-single-profile" id="wpum-profile-<?php echo $user_data->ID;?>">

	<div class="wpum-user-details wpum_one_half">
		<div class="wpum-avatar-img wpum_one_fifth">
			<a href=""><?php echo get_avatar( $user_data->ID , 64 ); ?></a>
		</div>
		<div class="wpum-inner-details wpum_four_fith last">
			<div class="wpum-user-display-name">
				<a href=""><?php echo esc_attr( $user_data->display_name ); ?></a>
			</div>
			<div class="wpum-user-description">
				<?php echo wpautop( esc_attr( get_user_meta( $user_data->ID, 'description', true) ), true ); ?>
			</div>
		</div>
	</div>

	<div class="wpum-user-details wpum-align-right wpum_one_half last">
			
		<ul class="wpum-user-links">
			<li><a href="mailto:<?php echo antispambot( $user_data->user_email );?>" class="wpum-button"><?php _e('Send Email');?></a></li>
			<?php if( !empty( $user_data->user_url ) ) : ?>
			<li><a href="<?php echo esc_url( $user_data->user_url );?>" class="wpum-button" rel="nofollow" target="_blank"><?php _e('Visit website');?></a></li>
			<?php endif; ?>
		</ul>

	</div>

	<div class="wpum-clearfix"></div>

</div>