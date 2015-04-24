/*! WP User Manager - v0.1.0
 * http://wp-user-manager.com
 * Copyright (c) 2015; * Licensed GPLv2+ */
jQuery(document).ready(function ($) {

	/**
	 * Frontend Scripts
	 */
	var WPUM_Admin = {

		init : function() {
			this.general();
			this.restore_emails();
			this.order_default_fields();
			this.restore_default_fields();
			this.custom_fields_editor();
		},

		// General Functions
		general : function() {
			jQuery("select.select2, .wppf-multiselect").select2();
		},

		// Ajax Function to restore emails
		restore_emails : function() {

			$('#wpum-restore-emails').on('click', function(e) {

				e.preventDefault();

				if( confirm( wpum_admin_js.confirm ) ) {

					var wpum_nonce = $('#wpum_backend_security').val();

					$.ajax({
						type: 'GET',
						dataType: 'json',
						url: wpum_admin_js.ajax,
						data: {
							'action' : 'wpum_restore_emails', // Calls the ajax action
							'wpum_backend_security' : wpum_nonce
						},
						beforeSend: function() {
							$( '.wpum-spinner' ).remove();
							$( '.wpum-ajax-done-message' ).remove();
							$( '#wpum-restore-emails' ).after('<span id="wpum-spinner" class="spinner wpum-spinner is-active"></span>');
						},
						success: function(results) {
							$( '#wpum-restore-emails' ).after( '<p class="wpum-ajax-done-message"> <span class="dashicons dashicons-yes"></span> ' + results.data.message + '</p>' );
							$( '.wpum-spinner' ).remove();
						},
						error: function(xhr, status, error) {
						    alert(xhr.responseText);
						}
					});

			    } else {
			        
			        return false;
			    
			    }

			});

		},

		// Re-order the fields into the admin panel
		order_default_fields : function() {

			if ( $.isFunction($.fn.sortable) ) {

				$(".users_page_wpum-custom-fields-editor tbody").sortable({
					helper: this.sortable_table_fix,
					axis: "y",
					cursor: 'pointer',
					opacity: 0.5,
					placeholder: "row-dragging",
					delay: 150,
					handle: ".column-order, .move-field",
					update: function(event, ui) {
		                
		                // Update TR data
						$(this).children('tr').each(function() {
				            $(this).children('td:first-child').html($(this).index());
				            $(this).data('priority',$(this).index());
				        });
						
						// Prepare field data
		                dataArray = $.map($(this).children('tr'), function(el){
					        return {'priority':$(el).data('priority'), 'meta':$(el).data('meta'), 'required':$(el).data('required'), 'show_on_signup':$(el).data('show_on_signup')}; 
					    });

					    // Get nonce
					    var wpum_editor_nonce = $('#_wpnonce').val();

		                $.ajax({
							type: 'POST',
							dataType: 'json',
							url: wpum_admin_js.ajax,
							data: {
								'action' : 'wpum_update_fields_order', // Calls the ajax action
								'items' : dataArray,
								'wpum_editor_nonce': wpum_editor_nonce
							},
							beforeSend: function() {
								WPUM_Admin.display_loader();
								WPUM_Admin.remove_message();
							},
							success: function(results) {
								// Update odd even table classes
								$('.users_page_wpum-custom-fields-editor').find("tr").removeClass('alternate');
								$('.users_page_wpum-custom-fields-editor').find("tr:even").addClass('alternate');
								// Hide loading indicator
								WPUM_Admin.hide_loader();
								// Show message
								WPUM_Admin.display_success_message( '.wpum-page-title', results.data.message );
							},
							error: function(xhr, status, error) {
							    alert(xhr.responseText);
							}
						});

		            }
				}).disableSelection();
			}

		},

		// Adjust table width when dragging
		sortable_table_fix : function( e, tr ) {
			var $originals = tr.children();
		    var $helper = tr.clone();
		    $helper.children().each(function(index){
		      $(this).width($originals.eq(index).width())
		    });
		    return $helper;
		},

		// Ajax Function to restore default fields
		restore_default_fields : function() {

			$('#wpum-restore-default-fields').on('click', function(e) {

				e.preventDefault();

				if( confirm( wpum_admin_js.confirm ) ) {

					var wpum_restore_fields_nonce = $('#wpum_backend_fields_restore').val();

					$.ajax({
						type: 'GET',
						dataType: 'json',
						url: wpum_admin_js.ajax,
						data: {
							'action' : 'wpum_restore_default_fields', // Calls the ajax action
							'wpum_backend_fields_restore' : wpum_restore_fields_nonce
						},
						beforeSend: function() {
							$( '.wpum-spinner' ).remove();
							$( '.wpum-ajax-done-message' ).remove();
							$( '#wpum-restore-default-fields' ).after('<span id="wpum-spinner" class="spinner wpum-spinner is-active"></span>');
						},
						success: function(results) {
							$( '#wpum-restore-default-fields' ).after( '<p class="wpum-ajax-done-message"> <span class="dashicons dashicons-yes"></span> ' + results.data.message + '</p>' );
							$( '.wpum-spinner' ).remove();
						},
						error: function(xhr, status, error) {
						    alert(xhr.responseText);
						}
					});

			    } else {
			        
			        return false;
			    
			    }

			});

		},

		// Custom Fields Editor
		custom_fields_editor : function() {

			$('.users_page_wpum-custom-fields-editor td.column-edit a').on('click', function(e) {

				e.preventDefault();

				// Grab row of the selected field to edit
				var field_row = $(this).parent().parent();
				var field_meta = $(this).data('meta');
				var field_nonce = $(this).next().val();

				// Remove any previous editors
				$( '.wpum-fields-editor' ).remove();

				$.ajax({
					type: "POST",
					dataType: 'json',
					url: wpum_admin_js.ajax,
					data: {
						'action' : 'wpum_load_field_editor', // Calls the ajax action
						'field_meta' : field_meta,
						'field_nonce' : field_nonce
					},
					beforeSend: function() {

						// Set height of loader indicator the same as the
						// editor table.
						WPUM_Admin.display_loader();
						WPUM_Admin.remove_message();

					},
					success: function(results) {

						// hide loader indicator
						WPUM_Admin.hide_loader();

						// Disable drag and drop of the table
						$( '.users_page_wpum-custom-fields-editor tbody' ).sortable( "disable" );

						// Now display the editor
						$( results ).insertAfter( field_row );

						// Scroll to the editor
						$('html, body').animate({
					        scrollTop: $('.wpum-fields-editor').offset().top
					    }, 800);

						// Remove the editor if cancel button is pressed.
						$('#delete-action a').on('click', function(e) {
							e.preventDefault();
							
							$( field_row ).removeClass('editing');
							$( '.wpum-fields-editor' ).remove();

							// Enable drag and drop of the table
							$( '.users_page_wpum-custom-fields-editor tbody' ).sortable( "enable" );

							// scroll back
							$('html, body').animate({
						        scrollTop: $( field_row ).offset().top
						    }, 800);

							return false;
						});

						// Perform field update
						$('.wpum-fields-editor form').on('submit', function(e) {
							
							e.preventDefault();

							// Grab field nonce 
							var update_nonce = $( this ).find( '#_wpnonce' ).val();

							// Grab values
							var values = $(this).serializeJSON({checkboxUncheckedValue: "false"});

							// Send field update
							WPUM_Admin.update_custom_field( values, update_nonce, field_meta );							

						});
						
					},
					error: function(xhr, status, error) {

						// hide loader indicator
						$( '.wpum-table-loader' ).hide();
						alert(xhr.responseText);

					}
				});

			});

		},

		// Updates the single field
		update_custom_field : function( values, nonce, field_id ) {

			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: wpum_admin_js.ajax,
				data: {
					'action' : 'wpum_update_single_field', // Calls the ajax action
					'update_nonce' : nonce,
					'field' : field_id,
					'options' : values
				},
				beforeSend: function() {
					WPUM_Admin.display_loader();
					WPUM_Admin.remove_message();
				},
				success: function( results ) {
					WPUM_Admin.hide_loader();

					$( '.wpum-fields-editor' ).remove();
					// Enable drag and drop of the table
					$( '.users_page_wpum-custom-fields-editor tbody' ).sortable( "enable" );

					WPUM_Admin.display_success_message( '.wpum-page-title', results.data.message );

					location.reload();

				},
				error: function(xhr, status, error) {
				    alert(xhr.responseText);
				}
			});

		},

		// Display spinner
		display_loader : function() {

			// Set height of loader indicator the same as the
			// editor table.
			var table_height = $( '.wp-list-table' ).height();
			$('.wpum-table-loader').css('display','table');
			$('.wpum-table-loader').css('height', table_height );
			$('.wpum-table-loader #wpum-spinner').addClass('is-active');

		},
		// Hide the spinner
		hide_loader : function() {
			$('.wpum-table-loader').hide();
			$('.wpum-table-loader #wpum-spinner').removeClass('is-active');
		},
		// Display a success message
		display_success_message : function( after, message, status, scroll ) {
			status = status || "updated";
			scroll = scroll || true;

			$( after ).after( '<div class="wpum-message '+ status +' notice is-dismissible"><p>' + message + '</p></div>' );

			if( scroll ) {
				// scroll back
				$("html, body").animate({ scrollTop: 0 }, "slow");
  				return false;
			}

		},

		remove_message : function() {
			$( '.wpum-message' ).remove();
		}

	};

	WPUM_Admin.init();

});