<?php
/**
 * WPUM Template: Recently Registered users list.
 * Displays a list of recently registered users.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

$args = array(
	'number'  => $amount,
	'orderby' => 'registered'
);

// The Query
$user_query = new WP_User_Query( $args );

// Get the results
$users = $user_query->get_results();

?>

<div class="wpum-recent-users">

<?php foreach ( $users as $user ) : ?>

	<?php echo $user->display_name; ?>

<?php endforeach; ?>

</div>