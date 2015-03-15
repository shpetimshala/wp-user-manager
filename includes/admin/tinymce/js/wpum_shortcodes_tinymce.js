(function() {

	// Default Values
	var yes_no = [
		{text: 'Yes', value: 'yes'},
		{text: 'No', value: 'no'},
	];
	var no_yes = [
		{text: 'No', value: 'no'},
		{text: 'Yes', value: 'yes'},
	];
	var true_false = [
		{text: 'Yes', value: 'true'},
		{text: 'No', value: 'false'},
	];
	var false_true = [
		{text: 'No', value: 'false'},
		{text: 'Yes', value: 'true'},
	];

	tinymce.PluginManager.add( 'wpum_shortcodes_mce_button', function( editor, url ) {
		editor.addButton( 'wpum_shortcodes_mce_button', {
			title: 'WP User Manager Shortcodes',
			type: 'menubutton',
			icon: 'icon wpum-shortcodes-icon',
			menu: [

				/* Login Form */
				{
					text: 'Login Form',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Login Form Shortcode',
							body: [ 
								{
									type: 'textbox', 
									name: 'id', 
									label: 'Form ID (optional)',
									value: ''
								},
								{
									type: 'textbox', 
									name: 'redirect', 
									label: 'Redirect URL (optional)',
									value: ''
								},
								{
									type: 'textbox', 
									name: 'label_username', 
									label: 'Username label (optional)',
									value: ''
								},
								{
									type: 'textbox', 
									name: 'label_password', 
									label: 'Password label (optional)',
									value: ''
								},
								{
									type: 'textbox', 
									name: 'label_remember', 
									label: 'Remember label (optional)',
									value: ''
								},
								{
									type: 'textbox', 
									name: 'label_log_in', 
									label: 'Login label (optional)',
									value: ''
								},
								{
									type: 'listbox',
									name: 'profile',
									label: 'Show Profile Info',
									'values': no_yes
								}
							],
							onsubmit: function( e ) {
								editor.insertContent( '[wpum_login_form id="' + e.data.id + '" redirect="' + e.data.redirect + '" label_username="' + e.data.label_username + '" label_password="' + e.data.label_password + '" label_remember="' + e.data.label_remember + '" label_log_in="' + e.data.label_log_in + '" profile="' + e.data.profile + '" ]');
							}
						});
					}
				}, // End Login Form

				/* Logout Link */
				{
					text: 'Logout Link',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Logout Link',
							body: [ 
								{
									type: 'textbox', 
									name: 'redirect', 
									label: 'Redirect after logout (optional)',
									value: ''
								},
								{
									type: 'textbox', 
									name: 'label', 
									label: 'Link Label',
									value: 'Logout'
								},
							],
							onsubmit: function( e ) {
								editor.insertContent( '[wpum_logout redirect="' + e.data.redirect + '" label="' + e.data.label + '" ]');
							}
						});
					}
				}, // End Logout Link

				/* Registration form */
				{
					text: 'Registration Form',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Registration Form',
							body: [ 
								{
									type: 'textbox', 
									name: 'form_id', 
									label: 'Form ID (optional)',
									value: ''
								},
							],
							onsubmit: function( e ) {
								editor.insertContent( '[wpum_register form_id="' + e.data.form_id + '" ]');
							}
						});
					}
				}, // End Registration form

				/* Password Recovery Form */
				{
					text: 'Password Recovery Form',
					onclick: function() {
						editor.windowManager.open( {
							title: 'Password Recovery Form',
							body: [ 
								{
									type: 'textbox', 
									name: 'form_id', 
									label: 'Form ID (optional)',
									value: ''
								},
							],
							onsubmit: function( e ) {
								editor.insertContent( '[wpum_password_recovery form_id="' + e.data.form_id + '" ]');
							}
						});
					}
				}, // End Password Recovery Form

				/* Profile Edit */
				{
					text: 'Profile Edit Form',
					onclick: function() {
						editor.insertContent( '[wpum_profile_edit]');
					}
				}, // End Profile Edit

			]
		});
	});
})();