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

do_action( "wpum_before_profile", $user_data );

?>

<div class="wpum-single-profile" id="wpum-profile-<?php echo $user_data->ID;?>">

	<?php do_action( "wpum_before_profile_details", $user_data ); ?>

	<div class="wpum-user-details">
		
		<div class="wpum-avatar-img wpum_one_sixth">
			<a href="<?php echo wpum_get_user_profile_url( $user_data ); ?>"><?php echo get_avatar( $user_data->ID , 128 ); ?></a>
			<?php do_action( "wpum_profile_after_avatar", $user_data ); ?>
		</div>
		
		<div class="wpum-inner-details wpum_five_sixth last">
			
			<div class="wpum-user-display-name">
				<a href="<?php echo wpum_get_user_profile_url( $user_data ); ?>"><?php echo esc_attr( $user_data->display_name ); ?></a>
				<?php do_action( "wpum_profile_after_name", $user_data ); ?>
			</div>
			
			<div class="wpum-user-description">
				<?php echo wpautop( esc_attr( get_user_meta( $user_data->ID, 'description', true) ), true ); ?>
				<?php do_action( "wpum_profile_after_description", $user_data ); ?>
			</div>

			<?php do_action( "wpum_profile_before_links", $user_data ); ?>

			<ul class="wpum-user-links">
				<li class="wpum-profile-link view-profile"><a href="<?php echo wpum_get_user_profile_url( $user_data ); ?>"><?php _e('View Profile'); ?></a></li>
				<li class="wpum-profile-link send-email"><a href="mailto:<?php echo antispambot( $user_data->user_email );?>" class="wpum-button"><?php _e('Send Email');?></a></li>
				<?php if( !empty( $user_data->user_url ) ) : ?>
				<li class="wpum-profile-link view-website"><a href="<?php echo esc_url( $user_data->user_url );?>" class="wpum-button" rel="nofollow" target="_blank"><?php _e('Visit website');?></a></li>
				<?php endif; ?>
				<?php if( $user_data->ID == get_current_user_id() ) : ?>
				<li><a href="<?php echo wpum_get_core_page_url('profile_edit'); ?>"><?php _e('Edit Account');?></a></li>
				<li><a href="<?php echo wpum_logout_url(); ?>"><?php _e('Logout');?></a></li>
				<?php endif; ?>
			</ul>

			<?php do_action( "wpum_profile_after_links", $user_data ); ?>

		</div>
		
		<div class="wpum-clearfix"></div>

	</div>

	<?php do_action( "wpum_after_profile_details", $user_data ); ?>

</div>

<?php do_action( "wpum_after_profile", $user_data ); ?>