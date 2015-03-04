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
			this.ajax_psw_recovery();
			this.ajax_psw_reset();
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
						$( wpum_form ).prepend('<p class="wpum-message wpum-notice">' + wpum_frontend_js.checking_credentials + '</p>');
					},
					success: function(results) {

						// Check the response
						if(results.loggedin === true) {
							$( wpum_form ).find('p.wpum-message').removeClass('wpum-notice').addClass('wpum-success').text(results.message);
							window.location.href = wpum_redirect;
						} else {
							$( wpum_form ).find('p.wpum-message').removeClass('wpum-notice').addClass('wpum-error').text(results.message);
						}

					}
				});

			});

		}, 

		// Check password strenght function
		checkPasswordStrength : function( $pass1, $strengthResult, $submitButton, blacklistArray ) {
	       
	        var pass1 = $pass1.val();
	 
	    	// Reset the form & meter
	        $strengthResult.removeClass( 'short bad good strong' );
	 
		    // Extend our blacklist array with those from the inputs & site data
		    blacklistArray = blacklistArray.concat( wp.passwordStrength.userInputBlacklist() )
		 
		    // Get the password strength
		    var strength = wp.passwordStrength.meter( pass1, blacklistArray );
		 
		    // Add the strength meter results
		    switch ( strength ) {
		 
		        case 2:
		            $strengthResult.addClass( 'bad' ).html( pwsL10n.bad );
		            break;
		 
		        case 3:
		            $strengthResult.addClass( 'good' ).html( pwsL10n.good );
		            break;
		 
		        case 4:
		            $strengthResult.addClass( 'strong' ).html( pwsL10n.strong );
		            break;
		 
		        case 5:
		            $strengthResult.addClass( 'short' ).html( pwsL10n.mismatch );
		            break;
		 
		        default:
		            $strengthResult.addClass( 'short' ).html( pwsL10n.short );
		 
		    }
		 
		    return strength;

		},

		// Process ajax psw recovery
		ajax_psw_recovery : function() {

			$('.wpum-password-form-wrapper-recover form').on('submit', function(e) {

				// Stop the form from submitting so we can use ajax.
				e.preventDefault();

				var wpum_psw_recovery_form = this; // form element
				var wpum_psw_username      = $(this).find('#username_email').val();
				var wpum_psw_nonce         = $(this).find('#_wpnonce').val();
				var wpum_psw_status        = $(this).find('#wpum_password_form_status').val();

				// Process psw recovery form through ajax
				$.ajax({
					type: 'GET',
					dataType: 'json',
					url: wpum_frontend_js.ajax,
					data: {
						'action'     : 'wpum_ajax_psw_recovery', // Calls the ajax action
						'username' : wpum_psw_username,
						'form_status' : wpum_psw_status,
						'wpum_nonce_psw_security' : wpum_psw_nonce
					},
					beforeSend: function() {
						$( wpum_psw_recovery_form ).find('p.wpum-message').remove();
						$( wpum_psw_recovery_form ).prepend('<p class="wpum-message wpum-notice">' + wpum_frontend_js.checking_credentials + '</p>');
					},
					success: function(results) {

						// Check the response
						if(results.valid === true) {
							$( wpum_psw_recovery_form ).find('p.wpum-message').removeClass('wpum-notice').addClass('wpum-success').text(results.message);
						} else {
							$( wpum_psw_recovery_form ).find('p.wpum-message').removeClass('wpum-notice').addClass('wpum-error').text(results.message);
						}

					}
				});

			});

		},

		// Process ajax psw reset
		ajax_psw_reset : function() {

			$('.wpum-password-form-wrapper-reset form').on('submit', function(e) {

				// Stop the form from submitting so we can use ajax.
				e.preventDefault();

				var wpum_psw_reset_form   = this; // form element
				var wpum_psw_password_1   = $(this).find('#password_1').val();
				var wpum_psw_password_2   = $(this).find('#password_2').val();
				var wpum_psw_reset_nonce  = $(this).find('#_wpnonce').val();
				var wpum_psw_reset_status = $(this).find('#wpum_password_form_status').val();
				var wpum_psw_reset_key    = $(this).find("input:hidden[name='wpum_psw_reset_key']").val();
				var wpum_psw_reset_login  = $(this).find("input:hidden[name='wpum_psw_reset_login']").val();

				// Process psw recovery form through ajax
				$.ajax({
					type: 'GET',
					dataType: 'json',
					url: wpum_frontend_js.ajax,
					data: {
						'action'     : 'wpum_ajax_psw_reset', // Calls the ajax action
						'password_1' : wpum_psw_password_1,
						'password_2' : wpum_psw_password_2,
						'form_status' : wpum_psw_reset_status,
						'key' : wpum_psw_reset_key,
						'login' : wpum_psw_reset_login,
						'wpum_nonce_psw_security' : wpum_psw_reset_nonce
					},
					beforeSend: function() {
						$( wpum_psw_reset_form ).find('p.wpum-message').remove();
						$( wpum_psw_reset_form ).prepend('<p class="wpum-message wpum-notice wpum-lost-psw-message">' + wpum_frontend_js.checking_credentials + '</p>');
					},
					success: function(results) {

						// Check the response
						if(results.completed === true) {
							$( wpum_psw_reset_form ).find('p.wpum-message').removeClass('wpum-notice').addClass('wpum-success wpum-lost-psw-message').text(results.message);
						} else {
							$( wpum_psw_reset_form ).find('p.wpum-message').removeClass('wpum-notice').addClass('wpum-error wpum-lost-psw-message').text(results.message);
						}

					}
				});

			});

		}

	};

	WPUM_Frontend.init();

	// Run pwd meter if enabled
	if( wpum_frontend_js.pwd_meter == 1 ) {
		$( 'body' ).on( 'keyup', 'input[name=password]',
	        function( event ) {
	            WPUM_Frontend.checkPasswordStrength(
	                $('.wpum-default-registration-form-wrapper input[name=password]'),         // First password field
	                $('.wpum-default-registration-form-wrapper #password-strength'),           // Strength meter
	                $('#submit_wpum_register'),           // Submit button
	                ['admin', 'administrator', 'test', 'user', 'demo']        // Blacklisted words
	            );
	        }
	    );
	}

});