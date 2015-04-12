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

	<?php if ( isset( $_GET['updated'] ) && $_GET['updated'] == 'success' ) : ?>
		<p class="wpum-message wpum-success wpum-profile-updated-message">
			<?php echo apply_filters( 'wpum_profile_update_success_message', __( 'Profile successfully updated.' ) ); ?>
		</p>
	<?php endif; ?>

	<?php if ( isset( $_GET['updated'] ) && $_GET['updated'] == 'error' ) : ?>
		<p class="wpum-message wpum-error wpum-profile-updated-message">
			<?php echo apply_filters( 'wpum_profile_update_error_message', __( 'Something went wrong.' ) ); ?>
		</p>
	<?php endif; ?>

	<?php do_action( 'wpum_before_profile_form_template', $atts ); ?>

	<form action="#" method="post" id="wpum-profile" class="wpum-profile-form" name="wpum-profile" enctype="multipart/form-data">

		<?php do_action( 'wpum_before_inside_profile_form_template', $atts ); ?>

		<!-- Start Name Fields -->
		<?php foreach ( $fields as $key => $field ) : ?>
			<fieldset class="fieldset-<?php esc_attr_e( $key ); ?>" data-type="<?php echo esc_attr( $field['type'] );?>" data-label="<?php echo esc_attr( $field['label'] );?>" data-required="<?php echo esc_attr( $field['required'] );?>" data-name="<?php esc_attr_e( $key ); ?>">
				<label for="<?php esc_attr_e( $key ); ?>"><?php echo $field['label']; ?></label>
				<div class="field <?php echo $field['required'] ? 'required-field' : ''; ?>">
					<?php do_action( "wpum_before_single_{$field['type']}_field", $form, $field ); ?>
					<?php get_wpum_template( 'form-fields/' . $field['type'] . '-field.php', array( 'key' => $key, 'field' => $field ) ); ?>
					<?php do_action( "wpum_after_single_{$field['type']}_field", $form, $field ); ?>
				</div>
			</fieldset>
		<?php endforeach; ?>
		<!-- End Name Fields -->

		<?php do_action( 'wpum_after_inside_profile_form_template', $atts ); ?>

		<?php wp_nonce_field( $form ); ?>

		<p class="wpum-submit">
			<input type="hidden" name="wpum_submit_form" value="<?php echo $form; ?>" />
			<input type="hidden" name="wpum_user_id" id="wpum_user_id" value="<?php echo $user_id; ?>" />
			<input type="submit" id="submit_wpum_profile" name="submit_wpum_profile" class="button" value="<?php _e( 'Update Profile' ); ?>" />
		</p>

	</form>

	<?php do_action( 'wpum_after_profile_form_template', $atts ); ?>

</div>
