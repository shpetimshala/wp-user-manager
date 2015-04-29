<?php
/**
 * Getting Started Page Class
 *
 * @package     wp-user-manager
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WPUM_Getting_Started Class
 *
 * A general class for About and Credits page.
 *
 * @since 1.0.0
 */
class WPUM_Getting_Started {

	/**
	 * @var string The capability users should have to view the page
	 */
	public $minimum_capability = 'manage_options';

	/**
	 * Get things started
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menus') );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_init', array( $this, 'welcome'    ) );
	}

	/**
	 * Register the Dashboard Pages which are later hidden but these pages
	 * are used to render the Welcome and Credits pages.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function admin_menus() {
		// About Page
		add_dashboard_page(
			__( 'Welcome to WP User Manager', 'wpum' ),
			__( 'Welcome to WP User Manager', 'wpum' ),
			$this->minimum_capability,
			'wpum-about',
			array( $this, 'about_screen' )
		);

		// Changelog Page
		add_dashboard_page(
			__( 'WP User Manager Changelog', 'wpum' ),
			__( 'WP User Manager Changelog', 'wpum' ),
			$this->minimum_capability,
			'wpum-changelog',
			array( $this, 'changelog_screen' )
		);

		// Getting Started Page
		add_dashboard_page(
			__( 'Getting started with WP User Manager', 'wpum' ),
			__( 'Getting started with WP User Manager', 'wpum' ),
			$this->minimum_capability,
			'wpum-getting-started',
			array( $this, 'getting_started_screen' )
		);

	}

	/**
	 * Hide Individual Dashboard Pages
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function admin_head() {
		remove_submenu_page( 'index.php', 'wpum-about' );
		remove_submenu_page( 'index.php', 'wpum-changelog' );
		remove_submenu_page( 'index.php', 'wpum-getting-started' );
		remove_submenu_page( 'index.php', 'wpum-credits' );

		// Badge for welcome page
		$badge_url = WPUM_PLUGIN_URL . 'images/badge.png';
		?>
		<style type="text/css" media="screen">
		/*<![CDATA[*/
		.wpum-badge {
			background: url('<?php echo $badge_url; ?>') center 24px/85px 85px no-repeat #404448;
			-webkit-background-size: 85px 85px;
			color: #7ec276;
			font-size: 14px;
			text-align: center;
			font-weight: 600;
			margin: 5px 0 0;
			padding-top: 120px;
			height: 40px;
			display: inline-block;
			width: 150px;
			text-rendering: optimizeLegibility;
			-webkit-box-shadow: 0 1px 3px rgba(0,0,0,.2);
			box-shadow: 0 1px 3px rgba(0,0,0,.2);
		}

		.about-wrap .wpum-badge {
			position: absolute;
			top: 0;
			right: 0;
		}

		.wpum-welcome-screenshots {
			float: right;
			margin-left: 10px!important;
		}

		.about-wrap .feature-section {
			margin-top: 20px;
		}

		/*]]>*/
		</style>
		<?php
	}

	/**
	 * Navigation tabs
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function tabs() {
		$selected = isset( $_GET['page'] ) ? $_GET['page'] : 'wpum-about';
		?>
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php echo $selected == 'wpum-about' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpum-about' ), 'index.php' ) ) ); ?>">
				<?php _e( "What's New", 'wpum' ); ?>
			</a>
			<a class="nav-tab <?php echo $selected == 'wpum-getting-started' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpum-getting-started' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Getting Started', 'wpum' ); ?>
			</a>
			<a class="nav-tab <?php echo $selected == 'wpum-changelog' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpum-changelog' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Changelog', 'wpum' ); ?>
			</a>
		</h2>
		<?php
	}

	/**
	 * Render About Screen
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function about_screen() {
		?>
		<div class="wrap about-wrap">
			
			<h1><?php printf( __( 'Welcome to WP User Manager %s', 'wpum' ), WPUM_VERSION ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version! WP User Manager %s is ready to provide improved control over your WordPress users.', 'wpum' ), WPUM_VERSION ); ?></div>
			<div class="wpum-badge"><?php printf( __( 'Version %s', 'wpum' ), WPUM_VERSION ); ?></div>

			<?php $this->tabs(); ?>

		</div>
		<?php
	}

	/**
	 * Render Getting Started Screen
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function getting_started_screen() {
		?>
		<div class="wrap about-wrap">
			
			<h1><?php printf( __( 'Welcome to WP User Manager %s', 'wpum' ), WPUM_VERSION ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for installing the latest version! WP User Manager %s is ready to provide improved control over your WordPress users.', 'wpum' ), WPUM_VERSION ); ?></div>
			<div class="wpum-badge"><?php printf( __( 'Version %s', 'wpum' ), WPUM_VERSION ); ?></div>

			<?php $this->tabs(); ?>

			<p class="about-description"><?php _e('Use the tips below to get started using WP User Manager. You will be up and running in no time!'); ?></p>

			<div id="welcome-panel" class="welcome-panel" style="padding-top:0px;">
					<div class="welcome-panel-content">
						<div class="welcome-panel-column-container">
							<div class="welcome-panel-column">
								
								<h4><?php _e( 'Configure WP User Manager' );?></h4>
								<a class="button button-primary button-hero" href="<?php echo admin_url( 'users.php?page=wpum-settings&tab=general&wpum_action=install_pages' ); ?>"><?php _e('Install required pages'); ?></a>

								<ul>
									<li><a href="#" class="welcome-icon dashicons-lock" target="_blank"><?php _e('Strengthen your passwords'); ?></a></li>
									<li><a href="#" class="welcome-icon dashicons-admin-network" target="_blank"><?php _e('Setup login method'); ?></a></li>
									<li><a href="#" class="welcome-icon dashicons-email-alt" target="_blank"><?php _e('Customize notifications'); ?></a></li>
								</ul>

							</div>
							<div class="welcome-panel-column">
								<h4><?php _e( 'Administration Tools' ); ?></h4>
								<ul>
									<li><a href="#" class="welcome-icon dashicons-admin-users" target="_blank"><?php _e('Customize profiles'); ?></a></li>
									<li><a href="#" class="welcome-icon dashicons-admin-settings" target="_blank"><?php _e('Customize fields'); ?></a></li>
									<li><a href="#" class="welcome-icon dashicons-groups" target="_blank"><?php _e('Create user directories'); ?></a></li>
									<li><a href="#" class="welcome-icon dashicons-minus" target="_blank"><?php _e('Manage admin bar'); ?></a></li>
									<li><a href="#" class="welcome-icon dashicons-migrate" target="_blank"><?php _e('Configure redirects'); ?></a></li>
								</ul>
							</div>
							<div class="welcome-panel-column welcome-panel-last">
								<h4><?php _e('Documentation &amp; Support'); ?></h4>
								<p class="welcome-icon welcome-learn-more"><?php echo sprintf( __( 'Looking for help? <a href="%s" target="_blank">WP User Manager documentation</a> has got you covered.' ), '#' ); ?> <br/><br/><a href="#" class="button" target="_blank"><?php _e('Read documentation') ;?></a></p>
								
								<p class="welcome-icon welcome-learn-more"><?php echo sprintf( __('We do all we can to provide every user with the best support possible. If you encounter a problem or have a question, please <a href="#" target="_blank">contact us.</a>'), '#' ); ?></p>
							</div>
						</div>
					</div>
				</div>

		</div>
		<?php
	}

	/**
	 * Render Getting Started Screen
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function changelog_screen() {
		?>
		<div class="wrap about-wrap">
			
			<h1><?php printf( __( 'Welcome to WP User Manager %s', 'wpum' ), WPUM_VERSION ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version! WP User Manager %s is ready to provide improved control over your WordPress users.', 'wpum' ), WPUM_VERSION ); ?></div>
			<div class="wpum-badge"><?php printf( __( 'Version %s', 'wpum' ), WPUM_VERSION ); ?></div>

			<?php $this->tabs(); ?>

		</div>
		<?php
	}

	/**
	 * Sends user to the Welcome page on first activation of WPUM as well as each
	 * time WPUM is upgraded to a new version
	 *
	 * @access public
	 * @since 1.0
	 * @global $wpum_options Array of all the WPUM Options
	 * @return void
	 */
	public function welcome() {
		global $wpum_options;

		// Bail if no activation redirect
		if ( ! get_transient( '_wpum_activation_redirect' ) )
			return;

		// Delete the redirect transient
		delete_transient( '_wpum_activation_redirect' );

		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) )
			return;

		$upgrade = get_option( 'wpum_version_upgraded_from' );

		if( ! $upgrade ) { // First time install
			wp_safe_redirect( admin_url( 'index.php?page=wpum-getting-started' ) ); exit;
		} else { // Update
			wp_safe_redirect( admin_url( 'index.php?page=wpum-about' ) ); exit;
		}
	}

}

new WPUM_Getting_Started();