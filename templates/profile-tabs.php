<?php
/**
 * WPUM Template: Profile tabs.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

$current_tab_slug = wpum_get_current_profile_tab();

?>

<div class="wpum-profile-tabs-holder">

	<ul class="wpum-profile-tabs">
		<?php foreach ($tabs as $tab) : ?>
			<li class="wpum-tab-<?php echo $tab['id'];?>"><a href="<?php echo wpum_get_profile_tab_permalink( $user_data, $tab );?>"><?php echo $tab['title'];?></a></li>
		<?php endforeach; ?>
	</ul>

</div>

<div class="wpum-profile-tabs-content">

	<?php 

	switch ( $current_tab_slug ) {
		case null:
			echo "dasda";
			break;
		case $current_tab_slug:
			do_action( "wpum_profile_tab_content_{$current_tab_slug}" );
			break;
		default:
			# display no tab error message
			break;
	}

	?>	

</div>