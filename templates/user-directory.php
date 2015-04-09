<?php
/**
 * WPUM Template: User Directory.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */
?>

<!-- start directory -->
<div id="wpum-user-directory-<?php echo $directory_id; ?>" class="wpum-user-directory directory-<?php echo $directory_id; ?>">

	<!-- Start Users list -->
	<?php if ( ! empty( $user_data ) ) {
		
		echo '<ul class="wpum-user-listings">';

		foreach ( $user_data as $user ) {

			// Load single-user.php template to display each user individually
			get_wpum_template( "single-user.php", array( 'user' => $user ) );

		}

		echo "</ul>";

	} else {
	
		$args = array( 
			'id'   => 'wpum-no-user-found', 
			'type' => 'notice', 
			'text' => __( 'No users have been found' )
		);
		$warning = wpum_message( $args, true );

	} ?>

	<!-- end users list -->

</div>
<!-- end directory -->