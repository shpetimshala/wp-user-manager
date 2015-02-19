/**
 * WP User Manager
 * http://wp-user-manager.com
 *
 * Copyright (c) 2015 Alessandro Tesoro
 * Licensed under the GPLv2+ license.
 */

jQuery(document).ready(function ($) {

	/**
	 * Frontend Scripts
	 */
	var WPUM_Frontend = {

		init : function() {
			this.ajax_login();
		},

		// Handle Ajax Login
		ajax_login : function() {

			$('.wpum-login-form form').on('submit', function(e) {

				// Stop the form from submitting so we can use ajax.
				e.preventDefault();

				var wpum_form = this; // form element
				var wpum_username = $(this).find('p.login-username').children('input[type=text]').val();
				var wpum_password = $(this).find('p.login-password').children('input[type=password]').val();
				var wpum_rememberme = $(this).find('p.login-remember label').children('input[type=checkbox]').is(":checked");
				var wpum_nonce = $(this).find('#wpum_nonce_login_security').val();
				var wpum_redirect = $(this).parent('div.wpum-login-form').data('redirect');

				// Check if we are trying to login. If so, process all the needed form fields and return a faild or success message.
				$.ajax({
					type: 'GET',
					dataType: 'json',
					url: wpum_frontend_js.ajax,
					data: {
						'action'     : 'wpum_ajax_login', // Calls the ajax action
						'username' : wpum_username,
						'password' : wpum_password,
						'rememberme' : wpum_rememberme,
						'wpum_nonce_login_security' : wpum_nonce
					},
					beforeSend: function() {
						$( wpum_form ).find('p.wpum-message').remove();
						$(wpum_form).prepend('<p class="wpum-message notice">processing</p>');
					},
					success: function(results) {

						// Check the response
						if(results.loggedin === true) {
							$( wpum_form ).find('p.wpum-message').removeClass('notice').addClass('success').text(results.message);
							window.location.href = wpum_redirect;
						} else {
							$( wpum_form ).find('p.wpum-message').removeClass('notice').addClass('error').text(results.message);
						}

					}
				});

			});

		}

	};

	WPUM_Frontend.init();

});