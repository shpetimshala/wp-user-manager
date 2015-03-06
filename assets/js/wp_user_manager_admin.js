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
			this.fields_window_manager();
		},

		// General Functions
		general : function() {
			jQuery(".select2").select2();
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
							$( '#wpum-restore-emails' ).after('<span id="wpum-spinner" class="spinner wpum-spinner"></span>');
						},
						success: function(results) {
							$( '#wpum-restore-emails' ).after( '<p class="wpum-ajax-done-message"> <span class="dashicons dashicons-yes"></span> ' + results.message + '</p>' );
							$( '#wpum-spinner' ).hide();
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
					        return {'order':$(el).data('order'), 'meta':$(el).data('meta'), 'required':$(el).data('required')}; 
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
								$('.wpum-page-title').after('<div id="setting-error-" class="updated settings-error"><p><strong>' + results.message + '</strong></p></div>');
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
							$( '#wpum-restore-default-fields' ).after('<span id="wpum-spinner" class="spinner wpum-spinner"></span>');
						},
						success: function(results) {
							$( '#wpum-restore-default-fields' ).after( '<p class="wpum-ajax-done-message"> <span class="dashicons dashicons-yes"></span> ' + results.message + '</p>' );
							$( '#wpum-spinner' ).hide();
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
					$('body').addClass('themeblvd-stop-scroll');
					return false;
				});

				// Trigger field update
				var update_button = $( modal_window ).find('.button-primary');
				var update_nonce = $( modal_window ).find('#_wpnonce').val();

				$( update_button ).on( 'click', function(){

					$.ajax({
						type: 'GET',
						dataType: 'json',
						url: wpum_admin_js.ajax,
						data: {
							'action' : 'wpum_update_single_default_field', // Calls the ajax action
							'update_nonce' : update_nonce
						},
						beforeSend: function() {
							//$( '#wpum-restore-default-fields' ).after('<span id="wpum-spinner" class="spinner wpum-spinner"></span>');
						},
						success: function(results) {
							//$( '#wpum-restore-default-fields' ).after( '<p class="wpum-ajax-done-message"> <span class="dashicons dashicons-yes"></span> ' + results.message + '</p>' );
							//$( '#wpum-spinner' ).hide();
						}
					});


					$( modal_window ).hide();
					return false;
				});

			});

		}

	};

	WPUM_Admin.init();

});