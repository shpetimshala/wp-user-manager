<?php
/**
 * WPUM Template: Profile Form Template.
 *
 * Displays profile edit form.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */
?>

<div id="wpum-form-profile" class="wpum-profile-form-wrapper">

	<form action="#" method="post" id="wpum-profile" class="wpum-profile-form" name="wpum-profile">


		<?php wp_nonce_field( $form ); ?>

		<p class="wpum-submit">
			<input type="hidden" name="wpum_submit_form" value="<?php echo $form; ?>" />
			<input type="submit" id="submit_wpum_profile" name="submit_wpum_profile" class="button" value="<?php _e('Update Profile'); ?>" />
		</p>

	</form>

</div>