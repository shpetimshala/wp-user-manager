<?php
/**
 * WPUM Template: "Posts" profile tab.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Query arguments
$args = array( 'author' => $user_data->ID );

// The Query
$posts_query = new WP_Query( $args );
?>

<div class="wpum-user-posts-list">
	
	<!-- the loop -->
	<?php 

		if ( $posts_query->have_posts() ) :

			while ( $posts_query->have_posts() ) : $posts_query->the_post();

				echo the_title();

			endwhile;

		else :

			// Display error message
			$args = array( 
						'id'   => 'wpum-posts-not-found', 
						'type' => 'notice', 
						'text' => printf( __( '%s did not submit any posts yet.' ), $user_data->display_name )
					);
			wpum_message( $args );

		endif;

		// Reset the original query - do not remove this.
		wp_reset_postdata();

	?>
	<!-- end loop -->

</div>