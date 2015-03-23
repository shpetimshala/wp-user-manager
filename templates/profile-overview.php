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
	<dl class="">          
	    <dt><?php _e('Name');?>:</dt>
	    <dd><?php echo $user_data->first_name; ?> <?php echo $user_data->last_name; ?></dd>
	             
	    <dt><?php _e('Nickname');?>:</dt>
	    <dd><?php echo $user_data->user_nicename; ?></dd>

	    <dt><?php _e('Email');?>:</dt>
	    <dd><a href="mailto:<?php echo antispambot( $user_data->user_email );?>"><?php echo antispambot( $user_data->user_email ); ?></a></dd>

	    <dt><?php _e('Website');?>:</dt>
	    <dd><a href="<?php echo esc_url( $user_data->user_url ); ?>" rel="nofollow" target="_blank"><?php echo esc_url( $user_data->user_url ); ?></a></dd>
	</dl>
</div>