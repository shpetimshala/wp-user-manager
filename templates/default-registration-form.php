<?php
/**
 * WPUM Template: Default Registration Form Template.
 *
 * Displays login form.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

?>
<div id="wpum-form-register-<?php echo $atts['form_id'];?>" class="wpum-default-registration-form-wrapper" data-redirect="<?php echo $atts['redirect'];?>">

	<?php do_action( 'wpum_before_default_register_form_template', $atts ); ?>

	<form action="register" method="post" id="wpum-register-<?php echo $atts['form_id'];?>" class="wpum-default-registration-form" name="wpum-register-<?php echo $atts['form_id'];?>">

		<?php do_action( 'wpum_before_inside_default_register_form_template', $atts ); ?>
		Testing Form
		<?php do_action( 'wpum_after_inside_default_register_form_template', $atts ); ?>

		<input type="hidden" name="wpum_submit_form" value="<?php echo $form; ?>" />
		<input type="submit" name="submit_job" class="button" value="Submit" />

	</form>

	<?php do_action( 'wpum_after_default_register_form_template', $atts ); ?>

</div>