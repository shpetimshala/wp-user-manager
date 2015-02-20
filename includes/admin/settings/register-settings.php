<?php
/**
 * Register Settings
 *
 * @package     wp-user-manager
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Get an option
 * Looks to see if the specified setting exists, returns default if not
 *
 * @since 1.0.0
 * @return mixed
 */
function wpum_get_option( $key = '', $default = false ) {
	global $wpum_options;
	$value = ! empty( $wpum_options[ $key ] ) ? $wpum_options[ $key ] : $default;
	$value = apply_filters( 'wpum_get_option', $value, $key, $default );
	return apply_filters( 'wpum_get_option_' . $key, $value, $key, $default );
}

/**
 * Get Settings
 * Retrieves all plugin settings
 *
 * @since 1.0.0
 * @return array WPUM settings
 */
function wpum_get_settings() {

	$settings = get_option( 'wpum_settings' );
	return apply_filters( 'wpum_get_settings', $settings );

}

/**
 * Add all settings sections and fields
 *
 * @since 1.0.0
 * @return void
*/
function wpum_register_settings() {

	if ( false == get_option( 'wpum_settings' ) ) {
		add_option( 'wpum_settings' );
	}

	foreach( wpum_get_registered_settings() as $tab => $settings ) {

		add_settings_section(
			'wpum_settings_' . $tab,
			__return_null(),
			'__return_false',
			'wpum_settings_' . $tab
		);

		foreach ( $settings as $option ) {

			$name = isset( $option['name'] ) ? $option['name'] : '';

			add_settings_field(
				'wpum_settings[' . $option['id'] . ']',
				$name,
				function_exists( 'wpum_' . $option['type'] . '_callback' ) ? 'wpum_' . $option['type'] . '_callback' : 'wpum_missing_callback',
				'wpum_settings_' . $tab,
				'wpum_settings_' . $tab,
				array(
					'section' => $tab,
					'id'      => isset( $option['id'] )      ? $option['id']      : null,
					'desc'    => ! empty( $option['desc'] )  ? $option['desc']    : '',
					'name'    => isset( $option['name'] )    ? $option['name']    : null,
					'size'    => isset( $option['size'] )    ? $option['size']    : null,
					'options' => isset( $option['options'] ) ? $option['options'] : '',
					'std'     => isset( $option['std'] )     ? $option['std']     : '',
					'min'     => isset( $option['min'] )     ? $option['min']     : null,
					'max'     => isset( $option['max'] )     ? $option['max']     : null,
					'step'    => isset( $option['step'] )    ? $option['step']    : null
				)
			);
		}

	}

	// Creates our settings in the options table
	register_setting( 'wpum_settings', 'wpum_settings', 'wpum_settings_sanitize' );

}
add_action('admin_init', 'wpum_register_settings');

/**
 * Retrieve the array of plugin settings
 *
 * @since 1.0.0
 * @return array
*/
function wpum_get_registered_settings() {

	/**
	 * 'Whitelisted' WPUM settings, filters are provided for each settings
	 * section to allow extensions and other plugins to add their own settings
	 */
	$wpum_settings = array(
		/** General Settings */
		'general' => apply_filters( 'wpum_settings_general',
			array(
				'registration_status' => array(
					'id'   => 'registration_status',
					'name' => __( 'Registrations Status:', 'wpum' ),
					'type' => 'hook'
				),
				'login_method' => array(
					'id'      => 'login_method',
					'name'    => __( 'Allow users to login with:', 'wpum' ),
					'desc'    => __('Select how users can login to your site.'),
					'type'    => 'select',
					'options' => wpum_get_login_methods()
				),
				'password_strenght' => array(
					'id'      => 'password_strenght',
					'name'    => __( 'Minimum Password Strength:', 'wpum' ),
					'desc'    => __('Select how strong the password needs to be before users can register.'),
					'type'    => 'select',
					'options' => wpum_get_psw_lenghts()
				),
				'display_password_meter_registration' => array(
					'id'   => 'display_password_meter_registration',
					'name' => __( 'Display password meter on registration page:', 'wpum' ),
					'desc'    => __('Enable to display a password meter on registration page.'),
					'type' => 'checkbox'
				),
				'display_password_meter_profile' => array(
					'id'   => 'display_password_meter_profile',
					'name' => __( 'Display password meter on profile page:', 'wpum' ),
					'desc'    => __('Enable to display a password meter on profile page.'),
					'type' => 'checkbox'
				),
				'display_registration_link' => array(
					'id'   => 'display_registration_link',
					'name' => __( 'Display registration link:', 'wpum' ),
					'desc'    => __('Enable to display a registration link into the login form.'),
					'type' => 'checkbox'
				),
				'display_password_link' => array(
					'id'   => 'display_password_link',
					'name' => __( 'Display password recovery link:', 'wpum' ),
					'desc'    => __('Enable to display a password recovery link into the login form.'),
					'type' => 'checkbox'
				),
				'header1' => array(
					'id'   => 'header1',
					'name' => __( 'Redirects', 'wpum' ),
					'type' => 'header'
				),
				'logout_redirect' => array(
					'id'   => 'logout_redirect',
					'name' => __( 'Logout Redirect', 'wpum' ),
					'desc'    => __('Enter the url where you want to redirect users after they logout. If empty will return to wp-login.php'),
					'type' => 'text'
				),
			)
		),
	);

	return apply_filters( 'wpum_registered_settings', $wpum_settings );
}

/**
 * Settings Sanitization
 *
 * Adds a settings error (for the updated message)
 * At some point this will validate input
 *
 * @since 1.0.0
 * @param array $input The value inputted in the field
 * @return string $input Sanitizied value
 */
function wpum_settings_sanitize( $input = array() ) {

	global $wpum_options;

	if ( empty( $_POST['_wp_http_referer'] ) ) {
		return $input;
	}

	parse_str( $_POST['_wp_http_referer'], $referrer );

	$settings = wpum_get_registered_settings();
	$tab      = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';

	$input = $input ? $input : array();
	$input = apply_filters( 'wpum_settings_' . $tab . '_sanitize', $input );

	// Loop through each setting being saved and pass it through a sanitization filter
	foreach ( $input as $key => $value ) {

		// Get the setting type (checkbox, select, etc)
		$type = isset( $settings[$tab][$key]['type'] ) ? $settings[$tab][$key]['type'] : false;

		if ( $type ) {
			// Field type specific filter
			$input[$key] = apply_filters( 'wpum_settings_sanitize_' . $type, $value, $key );
		}

		// General filter
		$input[$key] = apply_filters( 'wpum_settings_sanitize', $input[$key], $key );
	}

	// Loop through the whitelist and unset any that are empty for the tab being saved
	if ( ! empty( $settings[$tab] ) ) {
		foreach ( $settings[$tab] as $key => $value ) {

			// settings used to have numeric keys, now they have keys that match the option ID. This ensures both methods work
			if ( is_numeric( $key ) ) {
				$key = $value['id'];
			}

			if ( empty( $input[$key] ) ) {
				unset( $wpum_options[$key] );
			}

		}
	}

	// Merge our new settings with the existing
	$output = array_merge( $wpum_options, $input );

	add_settings_error( 'wpum-notices', '', __( 'Settings successfully updated.', 'wpum' ), 'updated' );

	return $output;
}

/**
 * Sanitize text fields
 *
 * @since 1.0.0
 * @param array $input The field value
 * @return string $input Sanitizied value
 */
function wpum_sanitize_text_field( $input ) {
	return trim( $input );
}
add_filter( 'wpum_settings_sanitize_text', 'wpum_sanitize_text_field' );

/**
 * Retrieve settings tabs
 *
 * @since 1.0.0
 * @return array $tabs
 */
function wpum_get_settings_tabs() {

	$settings = wpum_get_registered_settings();

	$tabs             = array();
	$tabs['general']  = __( 'General', 'wpum' );

	return apply_filters( 'wpum_settings_tabs', $tabs );
}

/**
 * Header Callback
 * Renders the header.
 *
 * @since 1.0.0
 * @param array $args Arguments passed by the setting
 * @return void
 */
function wpum_header_callback( $args ) {
	echo '<hr/>';
}

/**
 * Checkbox Callback
 * Renders checkboxes.
 *
 * @since 1.0.0
 * @param array $args Arguments passed by the setting
 * @global $wpum_options Array of all the WPUM Options
 * @return void
 */
function wpum_checkbox_callback( $args ) {
	global $wpum_options;

	$checked = isset( $wpum_options[ $args[ 'id' ] ] ) ? checked( 1, $wpum_options[ $args[ 'id' ] ], false ) : '';
	$html = '<input type="checkbox" id="wpum_settings[' . $args['id'] . ']" name="wpum_settings[' . $args['id'] . ']" value="1" ' . $checked . '/>';
	$html .= '<label for="wpum_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Multicheck Callback
 * Renders multiple checkboxes.
 *
 * @since 1.0.0
 * @param array $args Arguments passed by the setting
 * @global $wpum_options Array of all the WPUM Options
 * @return void
 */
function wpum_multicheck_callback( $args ) {
	global $wpum_options;

	if ( ! empty( $args['options'] ) ) {
		foreach( $args['options'] as $key => $option ):
			if( isset( $wpum_options[$args['id']][$key] ) ) { $enabled = $option; } else { $enabled = NULL; }
			echo '<input name="wpum_settings[' . $args['id'] . '][' . $key . ']" id="wpum_settings[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked($option, $enabled, false) . '/>&nbsp;';
			echo '<label for="wpum_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
		endforeach;
		echo '<p class="description">' . $args['desc'] . '</p>';
	}
}

/**
 * Radio Callback
 * Renders radio boxes.
 *
 * @since 1.0.0
 * @param array $args Arguments passed by the setting
 * @global $wpum_options Array of all the WPUM Options
 * @return void
 */
function wpum_radio_callback( $args ) {
	global $wpum_options;

	foreach ( $args['options'] as $key => $option ) :
		$checked = false;

		if ( isset( $wpum_options[ $args['id'] ] ) && $wpum_options[ $args['id'] ] == $key )
			$checked = true;
		elseif( isset( $args['std'] ) && $args['std'] == $key && ! isset( $wpum_options[ $args['id'] ] ) )
			$checked = true;

		echo '<input name="wpum_settings[' . $args['id'] . ']"" id="wpum_settings[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked(true, $checked, false) . '/>&nbsp;';
		echo '<label for="wpum_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
	endforeach;

	echo '<p class="description">' . $args['desc'] . '</p>';
}

/**
 * Text Callback
 * Renders text fields.
 *
 * @since 1.0.0
 * @param array $args Arguments passed by the setting
 * @global $wpum_options Array of all the WPUM Options
 * @return void
 */
function wpum_text_callback( $args ) {
	global $wpum_options;

	if ( isset( $wpum_options[ $args['id'] ] ) )
		$value = $wpum_options[ $args['id'] ];
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="text" class="' . $size . '-text" id="wpum_settings[' . $args['id'] . ']" name="wpum_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<label for="wpum_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Number Callback
 *
 * Renders number fields.
 *
 * @since 1.0.0
 * @param array $args Arguments passed by the setting
 * @global $wpum_options Array of all the WPUM Options
 * @return void
 */
function wpum_number_callback( $args ) {
	global $wpum_options;
    
    if ( isset( $wpum_options[ $args['id'] ] ) )
		$value = $wpum_options[ $args['id'] ];
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	$max  = isset( $args['max'] ) ? $args['max'] : 999999;
	$min  = isset( $args['min'] ) ? $args['min'] : 0;
	$step = isset( $args['step'] ) ? $args['step'] : 1;

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="number" step="' . esc_attr( $step ) . '" max="' . esc_attr( $max ) . '" min="' . esc_attr( $min ) . '" class="' . $size . '-text" id="wpum_settings[' . $args['id'] . ']" name="wpum_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<label for="wpum_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Textarea Callback
 * Renders textarea fields.
 *
 * @since 1.0.0
 * @param array $args Arguments passed by the setting
 * @global $wpum_options Array of all the WPUM Options
 * @return void
 */
function wpum_textarea_callback( $args ) {
	global $wpum_options;

	if ( isset( $wpum_options[ $args['id'] ] ) )
		$value = $wpum_options[ $args['id'] ];
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	$html = '<textarea class="large-text" cols="50" rows="5" id="wpum_settings[' . $args['id'] . ']" name="wpum_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
	$html .= '<label for="wpum_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Missing Callback
 * If a function is missing for settings callbacks alert the user.
 *
 * @since 1.0.0
 * @param array $args Arguments passed by the setting
 * @return void
 */
function wpum_missing_callback($args) {
	printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'wpum' ), $args['id'] );
}

/**
 * Select Callback
 * Renders select fields.
 *
 * @since 1.0.0
 * @param array $args Arguments passed by the setting
 * @global $wpum_options Array of all the WPUM Options
 * @return void
 */
function wpum_select_callback($args) {
	global $wpum_options;

	if ( isset( $wpum_options[ $args['id'] ] ) )
		$value = $wpum_options[ $args['id'] ];
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	$html = '<select id="wpum_settings[' . $args['id'] . ']" name="wpum_settings[' . $args['id'] . ']"/>';

	foreach ( $args['options'] as $option => $name ) :
		$selected = selected( $option, $value, false );
		$html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
	endforeach;

	$html .= '</select>';
	$html .= '<label for="wpum_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Color select Callback
 *
 * Renders color select fields.
 *
 * @since 1.0.0
 * @param array $args Arguments passed by the setting
 * @global $wpum_options Array of all the WPUM Options
 * @return void
 */
function wpum_color_select_callback( $args ) {
	global $wpum_options;

	if ( isset( $wpum_options[ $args['id'] ] ) )
		$value = $wpum_options[ $args['id'] ];
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	$html = '<select id="wpum_settings[' . $args['id'] . ']" name="wpum_settings[' . $args['id'] . ']"/>';

	foreach ( $args['options'] as $option => $color ) :
		$selected = selected( $option, $value, false );
		$html .= '<option value="' . $option . '" ' . $selected . '>' . $color['label'] . '</option>';
	endforeach;

	$html .= '</select>';
	$html .= '<label for="wpum_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Rich Editor Callback
 *
 * Renders rich editor fields.
 *
 * @since 1.0.0
 * @param array $args Arguments passed by the setting
 * @global $wpum_options Array of all the WPUM Options
 * @global $wp_version WordPress Version
 */
function wpum_rich_editor_callback( $args ) {
	global $wpum_options, $wp_version;

	if ( isset( $wpum_options[ $args['id'] ] ) ) {
		$value = $wpum_options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$rows = isset( $args['size'] ) ? $args['size'] : 20;

	if ( $wp_version >= 3.3 && function_exists( 'wp_editor' ) ) {
		ob_start();
		wp_editor( stripslashes( $value ), 'wpum_settings_' . $args['id'], array( 'textarea_name' => 'wpum_settings[' . $args['id'] . ']', 'textarea_rows' => $rows ) );
		$html = ob_get_clean();
	} else {
		$html = '<textarea class="large-text" rows="10" id="wpum_settings[' . $args['id'] . ']" name="wpum_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
	}

	$html .= '<br/><label for="wpum_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Color picker Callback
 *
 * Renders color picker fields.
 *
 * @since 1.0.0
 * @param array $args Arguments passed by the setting
 * @global $wpum_options Array of all the WPUM Options
 * @return void
 */
function wpum_color_callback( $args ) {
	global $wpum_options;

	if ( isset( $wpum_options[ $args['id'] ] ) )
		$value = $wpum_options[ $args['id'] ];
	else
		$value = isset( $args['std'] ) ? $args['std'] : '';

	$default = isset( $args['std'] ) ? $args['std'] : '';

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="text" class="wpum-color-picker" id="wpum_settings[' . $args['id'] . ']" name="wpum_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $default ) . '" />';
	$html .= '<label for="wpum_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Descriptive text callback.
 *
 * Renders descriptive text onto the settings field.
 *
 * @since 1.0.0
 * @param array $args Arguments passed by the setting
 * @return void
 */
function wpum_descriptive_text_callback( $args ) {
	echo esc_html( $args['desc'] );
}

/**
 * Hook Callback
 *
 * Adds a do_action() hook in place of the field
 *
 * @since 1.0.0
 * @param array $args Arguments passed by the setting
 * @return void
 */
function wpum_hook_callback( $args ) {
	do_action( 'wpum_' . $args['id'] );
}

/**
 * Set manage_shop_settings as the cap required to save WPUM settings pages
 *
 * @since 1.0.0
 * @return string capability required
 */
function wpum_set_settings_cap() {
	return 'manage_options';
}
add_filter( 'option_page_capability_wpum_settings', 'wpum_set_settings_cap' );
