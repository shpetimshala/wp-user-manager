/**
 * WP User Manager
 * http://wp-user-manager.com
 *
 * Copyright (c) 2015 Alessandro Tesoro
 * Licensed under the GPLv2+ license.
 */

/*!
  SerializeJSON jQuery plugin.
  https://github.com/marioizquierdo/jquery.serializeJSON
  version 2.5.0 (Mar, 2015)

  Copyright (c) 2012, 2015 Mario Izquierdo
  Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
  and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
*/
!function(e){"use strict";e.fn.serializeJSON=function(n){var r,a,t,i,s,u,l;return u=e.serializeJSON,l=u.optsWithDefaults(n),u.validateOptions(l),a=this.serializeArray(),u.readCheckboxUncheckedValues(a,this,l),r={},e.each(a,function(e,n){t=u.splitInputNameIntoKeysArray(n.name),i=t.pop(),"skip"!==i&&(s=u.parseValue(n.value,i,l),l.parseWithFunction&&"_"===i&&(s=l.parseWithFunction(s,n.name)),u.deepSet(r,t,s,l))}),r},e.serializeJSON={defaultOptions:{parseNumbers:!1,parseBooleans:!1,parseNulls:!1,parseAll:!1,parseWithFunction:null,checkboxUncheckedValue:void 0,useIntKeysAsArrayIndex:!1},optsWithDefaults:function(n){var r,a;return null==n&&(n={}),r=e.serializeJSON,a=r.optWithDefaults("parseAll",n),{parseNumbers:a||r.optWithDefaults("parseNumbers",n),parseBooleans:a||r.optWithDefaults("parseBooleans",n),parseNulls:a||r.optWithDefaults("parseNulls",n),parseWithFunction:r.optWithDefaults("parseWithFunction",n),checkboxUncheckedValue:r.optWithDefaults("checkboxUncheckedValue",n),useIntKeysAsArrayIndex:r.optWithDefaults("useIntKeysAsArrayIndex",n)}},optWithDefaults:function(n,r){return r[n]!==!1&&""!==r[n]&&(r[n]||e.serializeJSON.defaultOptions[n])},validateOptions:function(e){var n,r;r=["parseNumbers","parseBooleans","parseNulls","parseAll","parseWithFunction","checkboxUncheckedValue","useIntKeysAsArrayIndex"];for(n in e)if(-1===r.indexOf(n))throw new Error("serializeJSON ERROR: invalid option '"+n+"'. Please use one of "+r.join(","))},parseValue:function(n,r,a){var t;return t=e.serializeJSON,"string"==r?n:"number"==r||a.parseNumbers&&t.isNumeric(n)?Number(n):"boolean"==r||a.parseBooleans&&("true"===n||"false"===n)?-1===["false","null","undefined","","0"].indexOf(n):"null"==r||a.parseNulls&&"null"==n?-1!==["false","null","undefined","","0"].indexOf(n)?null:n:"array"==r||"object"==r?JSON.parse(n):"auto"==r?t.parseValue(n,null,{parseNumbers:!0,parseBooleans:!0,parseNulls:!0}):n},isObject:function(e){return e===Object(e)},isUndefined:function(e){return void 0===e},isValidArrayIndex:function(e){return/^[0-9]+$/.test(String(e))},isNumeric:function(e){return e-parseFloat(e)>=0},splitInputNameIntoKeysArray:function(n){var r,a,t,i,s;return s=e.serializeJSON,i=s.extractTypeFromInputName(n),a=i[0],t=i[1],r=a.split("["),r=e.map(r,function(e){return e.replace(/]/g,"")}),""===r[0]&&r.shift(),r.push(t),r},extractTypeFromInputName:function(n){var r,a;if(a=e.serializeJSON,r=n.match(/(.*):([^:]+)$/)){var t=["string","number","boolean","null","array","object","skip","auto"];if(-1!==t.indexOf(r[2]))return[r[1],r[2]];throw new Error("serializeJSON ERROR: Invalid type "+r[2]+" found in input name '"+n+"', please use one of "+t.join(", "))}return[n,"_"]},deepSet:function(n,r,a,t){var i,s,u,l,o,p;if(null==t&&(t={}),p=e.serializeJSON,p.isUndefined(n))throw new Error("ArgumentError: param 'o' expected to be an object or array, found undefined");if(!r||0===r.length)throw new Error("ArgumentError: param 'keys' expected to be an array with least one element");i=r[0],1===r.length?""===i?n.push(a):n[i]=a:(s=r[1],""===i&&(l=n.length-1,o=n[l],i=p.isObject(o)&&(p.isUndefined(o[s])||r.length>2)?l:l+1),""===s?(p.isUndefined(n[i])||!e.isArray(n[i]))&&(n[i]=[]):t.useIntKeysAsArrayIndex&&p.isValidArrayIndex(s)?(p.isUndefined(n[i])||!e.isArray(n[i]))&&(n[i]=[]):(p.isUndefined(n[i])||!p.isObject(n[i]))&&(n[i]={}),u=r.slice(1),p.deepSet(n[i],u,a,t))},readCheckboxUncheckedValues:function(n,r,a){var t,i,s,u,l;null==a&&(a={}),l=e.serializeJSON,t="input[type=checkbox][name]:not(:checked):not([disabled])",i=r.find(t).add(r.filter(t)),i.each(function(r,t){s=e(t),u=s.attr("data-unchecked-value"),u?n.push({name:t.name,value:u}):l.isUndefined(a.checkboxUncheckedValue)||n.push({name:t.name,value:a.checkboxUncheckedValue})})}}}(window.jQuery||window.Zepto||window.$);

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
								$('#setting-error-').remove();
								// Set height of loader indicator the same as the
								// editor table.
								var table_height = $( '.wp-list-table' ).height();
								$('.wpum-table-loader').css('display','table');
								$('.wpum-table-loader').css('height', table_height );
							},
							success: function(results) {
								// Update odd even table classes
								$('.users_page_wpum-custom-fields-editor').find("tr").removeClass('alternate');
								$('.users_page_wpum-custom-fields-editor').find("tr:even").addClass('alternate');
								// Hide loading indicator
								$('.wpum-table-loader').hide();
								// Show message
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

		// Custom Fields Editor
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

							$( this ).find(':checkbox:not(:checked)').attr('value', false);

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

				},
				success: function(results) {

				},
				error: function(xhr, status, error) {
				    alert(xhr.responseText);
				}
			});

		},

	};

	WPUM_Admin.init();

});