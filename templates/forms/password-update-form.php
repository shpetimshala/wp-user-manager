<?php
/**
 * WPUM Template: Password update form template.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */
?>

<div id="wpum-form-update-password" class="wpum-update-password-form-wrapper">

	<?php do_action( 'wpum_before_password_update_form' ); ?>

	<form action="#" method="post" id="wpum-update-password" class="wpum-update-password-form" name="wpum-update-password">

		<?php do_action( 'wpum_before_inside_password_update_form' ); ?>

		<!-- Start Name Fields -->
		<?php foreach ( $password_fields as $key => $field ) : ?>
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

		<?php do_action( 'wpum_after_inside_password_update_form' ); ?>

		<?php wp_nonce_field( $form ); ?>

		<p class="wpum-submit">
			<input type="hidden" name="wpum_submit_form" value="<?php echo $form; ?>" />
			<input type="submit" id="submit_wpum_update_password" name="submit_wpum_update_password" class="button" value="<?php _e( 'Update Password' ); ?>" />
		</p>

	</form>

	<?php do_action( 'wpum_after_password_update_form' ); ?>

</div>
