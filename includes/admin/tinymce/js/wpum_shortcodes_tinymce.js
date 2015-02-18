(function() {
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
									name: 'item_id', 
									label: 'Item ID',
									value: ''
								},
								{
									type: 'listbox',
									name: 'hyperlink',
									label: 'Link to single item?',
									'values': [
										{text: 'Yes', value: 'true'},
										{text: 'No', value: 'false'}
									]
								},
							],
							onsubmit: function( e ) {
								editor.insertContent( '[wpum_login_form]');
							}
						});
					}
				}, // End Login Form

			]
		});
	});
})();