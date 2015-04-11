<?php
/**
 * WPUM Template: Directory Top Bar.
 * This template is usually used within a user directory.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

$default_sorting = wpum_directory_get_sorting_method( $directory_id );

?>

<div class="wpum-directory-top-bar">
	
	<div class="wpum_one_third">

		<?php echo sprintf( __( 'Found %s users.' ), $users_found ) ?>

	</div>

	<div class="wpum_one_third">

		<p><?php _e( 'Sort by:' ); ?> <?php echo wpum_directory_sort_dropdown( "selected=$default_sorting" ); ?></p>

	</div>

	<div class="wpum_one_third last">

	</div>

	<div class="wpum-clearfix"></div>

</div>