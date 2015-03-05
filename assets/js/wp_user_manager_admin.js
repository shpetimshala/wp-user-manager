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
					handle: ".column-order",
					update: function(event, ui) {
		                
		                // Update TR data
						$(this).children('tr').each(function() {
				            $(this).children('td:first-child').html($(this).index());
				            $(this).data('priority',$(this).index());
				        });

		                dataArray = $.map($(this).children('tr'), function(el){
					        return {'priority':$(el).data('priority'), 'field_id':$(el).data('field_id')}; 
					    });

		                $.ajax({
							type: 'GET',
							dataType: 'json',
							url: wpum_admin_js.ajax,
							data: {
								'action' : 'wpum_store_default_fields_order', // Calls the ajax action
								'items' : dataArray
							},
							beforeSend: function() {
								$('.wpum-table-loader').css('display','table');
							},
							success: function(results) {
								$('.wpum-table-loader').css('display','none');
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
		}

	};

	WPUM_Admin.init();
	
});