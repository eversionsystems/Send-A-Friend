(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	 
	$(function() {
		/*$( '.ajax_add_to_cart' ).click(function (e) {
			e.preventDefault();
			//alert('clicked');
			if ($('#hidden_cart').length == 0) { //add cart contents only once
				//$('.added_to_cart').after('<a href="#TB_inline?width=600&height=550&inlineId=hidden_cart" class="thickbox">View my inline content!</a>');
				$(this).append('<a href="#TB_inline?width=300&height=550&inlineId=hidden_cart" id="show_hidden_cart" title="<h2>Cart</h2>" class="thickbox" style="display:none"></a>');
				$(this).append('<div id="hidden_cart" style="display:none"></div>');
			}
			//$('#show_hidden_cart').click();
		});*/
		
		// When AJAX add to cart event is called 
		/*$( document.body ).on( "added_to_cart", function( event, fragments, cart_hash, thisbutton ) {
			//$( '#button-cart').html(fragments['a.cart-contents']);
			//initButtons();
			alert('YESY');
		});*/
		
		$("#recipient_phone_number").keypress(function (e) {
			//if the letter is not digit then display error and don't type anything
			if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
			//display error message
			//$("#errmsg").html("Digits Only").show().fadeOut("slow");
				   return false;
			}
		});
		
		$('body').on('added_to_cart',function( event, fragments, cart_hash ) {
			//console.log(fragments);
			//console.log(cart_hash);
			
			//$( '.btn-add-cart-meta' ).data( "cart-hash", cart_hash );
			$('#myModal').modal('show');
		});
		
		$( '.btn-show-pop-up' ).on( "click", function( e ) {
			e.preventDefault();
			var productId = $( this ).data( 'product-id' );
			$('.btn-add-cart-meta').attr( 'data-product-id', productId );
			//testModalInput();
			
			// hide AJAX spinner on product
			$('.product-loading-icon').hide();
			$('.mk-svg-icon').hide();
			$('#myModal').modal('show');
		});
		
		$(".shop_table").on("click", ".btn-edit-cart-line-item", function(e){
			e.preventDefault();
			var cartItemKey = $( this ).data( 'cart_item_key' );
			editCartItem( cartItemKey );
		});
		
		$("#order_review").on("click", ".btn-edit-cart-line-item", function(e){
			e.preventDefault();
			var cartItemKey = $( this ).data( 'cart_item_key' );
			editCartItem( cartItemKey );
		});
		
		function editCartItem( cartItemKey) {
			$.ajax({ url: addToCartArgs.ajaxurl,
				type : 'post',
				dataType: 'json',
				data : { action: 'js_action_edit_cart_pop_up', 
						cart_item_key: cartItemKey,
						nonce: addToCartArgs.nonce
						},
				beforeSend : function() {
					$.blockUI({ message: null });
				},
				success: function(data, textStatus, jqXHR){
					$.unblockUI();
					$('#recipient_name').val(data.name);
					$('#recipient_phone_number').val(data.phoneNumber);
					$('#recipient_email').val(data.email);
					$('#recipient_message').val(data.message);
					//$('#recipient_note').val(data.note);
					$('.btn-add-cart-meta').hide();
					$('.btn-edit-cart-meta').show();
					$('.btn-edit-cart-meta').attr("data-cart-item-key", cartItemKey);
					
					$('#myModal').modal('show');
				},
				error: function(jqXHR, textStatus, errorThrown) {
					$.unblockUI();
				}
			});
		}
		
		$( '.btn-edit-cart-meta' ).click(function (e) {
			e.preventDefault();
			
			var cartItemKey = $( this ).data( 'cart-item-key' );
			var redirectUrl = $( this ).data( 'redirect-url' );
			
			var recipientName = $( '#recipient_name').val();
			var recipientPhoneNumber = $( '#recipient_phone_number' ).val();
			var recipientEmail = $( '#recipient_email' ).val();
			var recipientMessage = $( '#recipient_message' ).val();
			//var recipientNote = $( '#recipient_note' ).val();
			
			$.ajax({ url: addToCartArgs.ajaxurl,
				type : 'post',
				dataType: 'json',
				data : { action: 'js_action_update_cart_line_item', 
						cart_item_key: cartItemKey,
						nonce: addToCartArgs.nonce,
						recipient_name: recipientName,
						recipient_phone_number: recipientPhoneNumber,
						recipient_email: recipientEmail,
						recipient_message: recipientMessage,
						//recipient_note: recipientNote,
						},
				beforeSend : function() {
					//$.blockUI({ message: null });
					$( '.modal-content' ).block( { message: null } );
				},
				success: function(data, textStatus, jqXHR){
					//$.unblockUI();
					$( '.modal-content').unblock();
					$('#myModal').modal('hide');
					
					if( redirectUrl.length > 0 ) {
						window.location.href = redirectUrl;
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					//$.unblockUI();
					$( '.modal-content').unblock();
				}
			});
			
		});
		
		//$( '.btn-add-cart-meta' ).on("click", ".modal-content", function(e){
		$( '.btn-add-cart-meta' ).click(function (e) {
			e.preventDefault();
			
			//if ( $( this ).prop("disabled") == false ) {
			if ( $( this ).hasClass("disabled") == false ) {
				//var cartHash = $( '.btn-add-cart-meta' ).data( "cart-hash" );
				var productId = $( '.btn-add-cart-meta' ).data( 'product-id' );
				var recipientName = $( '#recipient_name').val();
				var recipientPhoneNumber = $( '#recipient_phone_number' ).val();
				var recipientEmail = $( '#recipient_email' ).val();
				var recipientMessage = $( '#recipient_message' ).val();
				//var recipientNote = $( '#recipient_note' ).val();
				var redirectUrl = $( this ).data( 'redirect-url' );
				
				$.ajax({ url: addToCartArgs.ajaxurl,
					type : 'post',
					dataType: 'json',
					data : { action: 'js_action_launch_cart_pop_up', 
							nonce: addToCartArgs.nonce,
							product_id: productId,
							recipient_name: recipientName,
							recipient_phone_number: recipientPhoneNumber,
							recipient_email: recipientEmail,
							recipient_message: recipientMessage,
							//recipient_note: recipientNote,
							},
					beforeSend : function() {
						//$.blockUI({ message: null });
						$( '.modal-content' ).block( { message: null } );
						//$( '.btn-send-email-message' ).block( { message: null } );
					},
					success: function(data, textStatus, jqXHR){
						//$( '.btn-send-email-message' ).unblock();
						$( '.modal-content').unblock();
						//$( '.btn-cancel-email-message' ).click();
						//$(document).ajaxStop($.unblockUI);
						//$("#modal_error").find('.btn-danger').data("linkUrl", data.linkUrl);
						//console.log(data.success);
						//$(document).ajaxStop($.unblockUI);
						$('#myModal').modal('hide');
						$('.added-cart').hide();
						
						if( redirectUrl.length > 0 ) {
							window.location.href = redirectUrl;
						}
						
						//clearModalFields();
					},
					error: function(jqXHR, textStatus, errorThrown) {
						$( '.modal-content').unblock();
						//$( '.btn-send-email-message' ).unblock();
						//$(document).ajaxStop($.unblockUI);
					}
				});
			} 
			else {
				$('.added-cart').hide();
			}
		});
		
		/*
		$('#recipient_phone_number').focusout(function() {
			if( $('#recipient_phone_number').val() != '' ) {
				$("#recipient_email").prop('required', false);
				//$("#recipient_email").prop('disabled', true);
			}
			else {
				if( $('#recipient_email').val() != '' ) { 
					$("#recipient_phone_number").prop('required', false);
					//$("#recipient_phone_number").prop('disabled', true);
				}
				else {
					$("#recipient_email").prop('required', false);
					//$("#recipient_email").prop('disabled', false);
				}
			}
			
			if( $('#recipient_phone_number').val() == '' && $('#recipient_email').val() == '' ) {
				$("#recipient_email").prop('required', true);
				$("#recipient_phone_number").prop('required', true);
			}
			
			//$('#send_friend').validator('validate');
		});
		
		$('#recipient_email').focusout(function() {
			if( $('#recipient_email').val() != '' ) {
				$("#recipient_phone_number").prop('required', false);
				//$("#recipient_phone_number").prop('disabled', true);
			}
			else {
				if( $('#recipient_phone_number').val() != '' ) { 
					$("#recipient_email").prop('required', false);
					//$("#recipient_email").prop('disabled', true);
				}
				else {
					$("#recipient_phone_number").prop('required', false);
					//$("#recipient_phone_number").prop('disabled', false);
				}
			}
			
			if( $('#recipient_phone_number').val() == '' && $('#recipient_email').val() == '' ) {
				$("#recipient_email").prop('required', true);
				$("#recipient_phone_number").prop('required', true);
			}
			
			//$('#send_friend').validator('validate');
		});

		$( "#recipient_message" ).keypress(function() {
			if( $('#recipient_email').val() != '' ) {
				$("#recipient_phone_number").prop('required', false);
			}
			if( $('#recipient_phone_number').val() != '' ) {
				$("#recipient_email").prop('required', false);
			}
		});
		*/
		
		$("input[name=devlivery_options]:radio").change(function () {
			var devliveryMethod = $('input[name=devlivery_options]:checked').val();
			if( devliveryMethod == 'phone' ) {
				$("#recipient_email").prop('required', false);
				$("#recipient_phone_number").prop('required', true);
				$(".email-field").hide('slow');
				$(".phone-number-field").show('slow');
				$("#recipient_email").val('');
			}
			else if( devliveryMethod == 'email' ) {
				$("#recipient_email").prop('required', true);
				$("#recipient_phone_number").prop('required', false);
				$(".phone-number-field").hide('slow');
				$(".email-field").show('slow');
				$("#recipient_phone_number").val('');
			}
			else {
				$("#recipient_email").prop('required', true);
				$("#recipient_phone_number").prop('required', true);
				$(".phone-number-field").show('slow');
				$(".email-field").show('slow');
			}
		});
		
		/*
		$('#recipient_message').focusout(function() {
			if( $('#recipient_phone_number').val() == '' ) {
				if ( $('#recipient_email').val() != '' ) {
					$('#recipient_phone_number').prop('required', false);
				}
			}
			if( $('#recipient_email').val() == '' ) {
				if ( $('#recipient_phone_number').val() != '' ) {
					$('#recipient_email').prop('required', false);
				}
			}
		});*/
		
		function testModalInput() {
			$( '#recipient_name').val('Test User');
			$( '#recipient_phone_number' ).val('1234567');
			$( '#recipient_email' ).val('test@test.com');
			//$( '#recipient_message' ).val('');
			//$( '#recipient_note' ).val('');
		}
		
		function clearModalFields() {
			$( '#recipient_name').val('');
			$( '#recipient_phone_number' ).val('');
			$( '#recipient_email' ).val('');
			$( '#recipient_message' ).val('');
			//$( '#recipient_note' ).val('');
		}
		
	});

})( jQuery );
