<?php
/**
 * WPUM Template: "Overview" profile tab.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */
?>

<div class="wpum-user-details-list">

	<?php do_action( 'wpum_before_user_details_list', $user_data, $tabs, $slug ); ?>

	<dl>
		<?php if ( $user_data->first_name || $user_data->last_name ) : ?>
	    <dt><?php _e( 'Name', 'wpum' );?>:</dt>
	    <dd><?php echo $user_data->first_name; ?> <?php echo $user_data->last_name; ?></dd>
		<?php endif; ?>

	    <?php if ( $user_data->user_nicename ) : ?>
	    <dt><?php _e( 'Nickname', 'wpum' );?>:</dt>
	    <dd><?php echo $user_data->user_nicename; ?></dd>
		<?php endif; ?>

		<?php if ( $user_data->user_email ) : ?>
	    <dt><?php _e( 'Email', 'wpum' );?>:</dt>
	    <dd><a href="mailto:<?php echo antispambot( $user_data->user_email );?>"><?php echo antispambot( $user_data->user_email ); ?></a></dd>
		<?php endif; ?>

		<?php if ( $user_data->user_url ) : ?>
	    <dt><?php _e( 'Website', 'wpum' );?>:</dt>
	    <dd><a href="<?php echo esc_url( $user_data->user_url ); ?>" rel="nofollow" target="_blank"><?php echo esc_url( $user_data->user_url ); ?></a></dd>
		<?php endif; ?>

	    <dt><?php _e( 'Registered', 'wpum' );?>:</dt>
	    <dd><?php echo date( get_option( 'date_format' ), strtotime( $user_data->user_registered ) ); ?></dd>
	</dl>

	<?php do_action( 'wpum_after_user_details_list', $user_data, $tabs, $slug ); ?>

	<!-- Start fields loop -->
	<?php if ( wpum_has_profile_fields() ) : ?>

		<?php while ( wpum_profile_field_groups() ) : wpum_the_profile_field_group(); ?>

			<?php if ( wpum_field_group_has_fields() ) : ?>

				<?php if( wpum_get_field_group_name() ) : ?>
					<h3 class="group-title"><?php echo esc_html( wpum_get_field_group_name() ); ?></h3>
				<?php endif; ?>

				<?php if( wpum_get_field_group_description() ) : ?>
					<p class="group-description"><?php echo esc_html( wpum_get_field_group_description() ); ?><p>
				<?php endif; ?>

				<!-- loop through each field -->
				<dl>
				<?php while ( wpum_profile_fields() ) : wpum_the_profile_field(); ?>

					<?php if ( wpum_field_has_data() ) : ?>
					<dt class="<?php wpum_the_field_css_class(); ?>"><?php wpum_the_field_name(); ?>:</dt>
			    <dd>test</dd>
					<?php endif; ?>
					
				<?php endwhile; ?>
				</dl>
				<!-- end loop through each field -->

			<?php endif; ?>

		<?php endwhile; ?>

	<?php endif; ?>
	<!-- end fields loop -->

</div>
