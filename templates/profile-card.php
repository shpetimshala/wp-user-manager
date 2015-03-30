<?php
/**
 * WPUM Template: Profile Card.
 * Displays a preview of the user profile.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */
?>

<div id="wpum-profile-card<?php echo $wrapper_id; ?>" class="wpum-profile-card">

	<div class="wpum-profile-img wpum_one_sixth">
		<?php echo wpum_profile_avatar( $user_data ); ?>
	</div>

	<div class="wpum-card-details wpum_five_sixth last">
		
		<p><?php echo wpum_profile_display_name( $user_data ); ?></p>
		<?php do_action( 'wpum_profile_card_details', $user_data ); ?>

	</div>

	<div class="wpum-clearfix"></div>

</div>