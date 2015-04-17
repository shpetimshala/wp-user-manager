<?php
/**
 * WPUM Template: Account page tabs.
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

echo get_query_var( 'account_tab' );

?>

<div id="wpum-account-tabs" class="wpum-account-tabs">

	<?php if( $tabs && is_array( $tabs ) ) : ?>

		<ul>

		<?php foreach ($tabs as $key => $tab) : ?>
			<li class="wpum-account-tab tab-<?php echo $key; ?>">
				<a href="<?php echo esc_url( wpum_get_account_tab_url( $tab['id'] ) ); ?>"><?php echo $tab['title']; ?></a>
			</li>
		<?php endforeach; ?>

		</ul>

	<?php endif; ?>

</div>