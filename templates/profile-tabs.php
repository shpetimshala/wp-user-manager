<?php
/**
 * WPUM Template: Profile tabs.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */
?>

<div class="wpum-profile-tabs-holder">

	<ul class="wpum-profile-tabs">
		<?php foreach ($tabs as $tab) : ?>
			<li class="wpum-tab-<?php echo $tab['id'];?>"><a href="<?php get_permalink();?>"><?php echo $tab['title'];?></a></li>
		<?php endforeach; ?>
	</ul>

</div>

<div class="wpum-profile-tabs-content">

	<?php 

	echo get_query_var('tab');

	?>

</div>