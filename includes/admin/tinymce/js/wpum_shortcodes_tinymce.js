(function() {

	// Default Values
	var yes_no = [
		{text: 'Yes', value: 'yes'},
		{text: 'No', value: 'no'},
	];
	var true_false = [
		{text: 'Yes', value: 'true'},
		{text: 'No', value: 'false'},
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
									name: 'remember',
									label: 'Show remember option',
									'values': true_false
								},
								{
									type: 'listbox',
									name: 'set_remember',
									label: 'Default remember option',
									'values': true_false
								},
							],
							onsubmit: function( e ) {
								editor.insertContent( '[wpum_login_form id="' + e.data.id + '" redirect="' + e.data.redirect + '" label_username="' + e.data.label_username + '" label_password="' + e.data.label_password + '" label_remember="' + e.data.label_remember + '" label_log_in="' + e.data.label_log_in + '" remember="' + e.data.remember + '" set_remember="' + e.data.set_remember + '" ]');
							}
						});
					}
				}, // End Login Form

			]
		});
	});
})();