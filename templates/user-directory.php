<?php
/**
 * WPUM Template: User Directory.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Return different template if set.
if( $template ) {
	get_wpum_template( "user-directory-{$template}.php", array( 
			'user_data'    => $user_data,
			'users_found'  => $users_found,
			'directory_id' => $directory_id,
			'search_form'  => $search_form,
			'template'     => $template
		) 
	);
	return;
}

?>

<!-- start directory -->
<div id="wpum-user-directory-<?php echo $directory_id; ?>" class="wpum-user-directory directory-<?php echo $directory_id; ?>">

	<!-- Start Users list -->
	<?php if ( ! empty( $user_data ) ) {

		do_action( 'wpum_before_user_directory', $directory_id );
		
		echo '<ul class="wpum-user-listings">';

		foreach ( $user_data as $user ) {

			// Load single-user.php template to display each user individually
			get_wpum_template( "single-user.php", array( 'user' => $user ) );

		}

		echo "</ul>";

		do_action( 'wpum_after_user_directory', $directory_id );

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