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
	var WPUM_Admin = {

		init : function() {
			this.general();
			this.restore_emails();
			this.order_default_fields();
			this.restore_default_fields();
			this.fields_window_manager();

			// Testing
			this.custom_fields_editor();
		},

		// General Functions
		general : function() {
			jQuery(".select2, .wppf-multiselect").select2();
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
							$( '.wpum-spinner' ).hide();
							$( '.wpum-ajax-done-message' ).hide();
							$( '#wpum-restore-emails' ).after('<span id="wpum-spinner" class="spinner wpum-spinner"></span>');
						},
						success: function(results) {
							$( '#wpum-restore-emails' ).after( '<p class="wpum-ajax-done-message"> <span class="dashicons dashicons-yes"></span> ' + results.data.message + '</p>' );
							$( '.wpum-spinner' ).hide();
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
			    // Get the table size
				var wpum_fields_table_h = $('.wpum_fields_table_list').height();
				var wpum_fields_table_w = $('.wpum_fields_table_list').width();
				
				// Set size to the loader div
				$('.wpum-table-loader').height( wpum_fields_table_h ).width( wpum_fields_table_w );

				$(".wpum_fields_table_list tbody").sortable({
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
				            $(this).data('order',$(this).index());
				        });

		                dataArray = $.map($(this).children('tr'), function(el){
					        return {'order':$(el).data('order'), 'meta':$(el).data('meta'), 'required':$(el).data('required'), 'show_on_signup':$(el).data('show_on_signup')}; 
					    });

					    var wpum_backend_fields_table = $('#wpum_backend_fields_table').val();

		                $.ajax({
							type: 'GET',
							dataType: 'json',
							url: wpum_admin_js.ajax,
							data: {
								'action' : 'wpum_store_default_fields_order', // Calls the ajax action
								'items' : dataArray,
								'wpum_backend_fields_table': wpum_backend_fields_table
							},
							beforeSend: function() {
								$('#setting-error-').remove();
								$('.wpum-table-loader').css('display','table');
							},
							success: function(results) {
								$('.wpum-table-loader').css('display','none');
								$('.wpum-page-title').after('<div id="setting-error-" class="updated settings-error"><p><strong>' + results.data.message + '</strong></p></div>');
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
							$( '.wpum-spinner' ).hide();
							$( '.wpum-ajax-done-message' ).hide();
							$( '#wpum-restore-default-fields' ).after('<span id="wpum-spinner" class="spinner wpum-spinner"></span>');
						},
						success: function(results) {
							$( '#wpum-restore-default-fields' ).after( '<p class="wpum-ajax-done-message"> <span class="dashicons dashicons-yes"></span> ' + results.data.message + '</p>' );
							$( '.wpum-spinner' ).hide();
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

		// Handles the display of the modal window to edit the fields.
		fields_window_manager : function() {

			$('.wpum-trigger-modal').on('click', function(e) {

				e.preventDefault();

				// Grab the window id and display it
				var field = $(this).data('field');
				var modal_window = '#window-' + field;
				$( modal_window ).show();

				// Hide the modal window when closed
				$('.media-modal-close').on( 'click', function(){
					$( modal_window ).hide();
					$( modal_window ).find('.wpum-spinner').remove();
					return false;
				});

				// Trigger field update
				var update_button = $( modal_window ).find('.button-primary');
				var update_nonce = $( modal_window ).find( '#' + field ).val();
				var field_required = $( modal_window ).find( '#' + field + '_field_required' ).val();
				var show_on_signup = $( modal_window ).find( '#' + field + '_field_display' ).val();

				$( '#' + field + '_field_required' ).on("change", function() {
				    field_required = this.value;
				});

				$( '#' + field + '_field_display' ).on("change", function() {
				    show_on_signup = this.value;
				});

				$( update_button ).on( 'click', function(){

					$.ajax({
						type: 'GET',
						dataType: 'json',
						url: wpum_admin_js.ajax,
						data: {
							'action' : 'wpum_update_single_default_field', // Calls the ajax action
							'update_nonce' : update_nonce,
							'field' : field,
							'required' : field_required,
							'show_on_signup': show_on_signup
						},
						beforeSend: function() {
							$( '.settings-error' ).remove();
							$( modal_window ).find('.wpum-spinner').remove();
							$( update_button ).before('<span id="wpum-spinner" class="spinner wpum-spinner modal-spinner"></span>');
							$( this ).attr('disabled','disabled');
						},
						success: function(results) {
							
							if( results.data.valid == true ) {

								$( modal_window ).find('.wpum-spinner').remove();
								$( modal_window ).hide();
								$('.wpum-page-title').after('<div id="setting-error-" class="updated settings-error"><p><strong>' + results.data.message + '</strong></p></div>');
								location.reload(true);

							} else {

								alert( results.data.message );

							}

						},
						error: function(xhr, status, error) {
						    alert(xhr.responseText);
						}
					});

					return false;
				});

			});

		},

		// Testing new editing window
		custom_fields_editor : function() {

			$('.users_page_wpum-custom-fields-editor td.column-edit a').on('click', function(e) {

				e.preventDefault();

				// Grab row of the selected field to edit
				var field_row = $(this).parent().parent();
				var field_meta = $(this).data('meta');
				var field_nonce = $(this).next().val();

				// Remove any previous editors
				$( '.users_page_wpum-custom-fields-editor tr' ).removeClass('editing');
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
						var table_height = $( '.wp-list-table' ).height();
						$('.wpum-table-loader').css('display','table');
						$('.wpum-table-loader').css('height', table_height );
						$( field_row ).addClass('editing');

					},
					success: function(results) {

						// hide loader indicator
						$( '.wpum-table-loader' ).hide();

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

							// scroll back
							$('html, body').animate({
						        scrollTop: $( field_row ).offset().top
						    }, 800);

							return false;
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

	};

	WPUM_Admin.init();

});