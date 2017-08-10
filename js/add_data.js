/*
jQuery(document).ready(function($) {
	$('.pgm-ajax-form').on('submit', function(e) {
		e.preventDefault();
		jQuery.ajax({
			type: "post",
			url: ajaxurl,
			data: { 
				action: 'validate', 
				name: jQuery( '#name' ).val(), 
				type: jQuery( '#type' ).val(), 
				address: jQuery( '#address' ).val(), 
				lat: jQuery( '#lat' ).val(), 
				lng: jQuery( '#lng' ).val(), 
				//form_data: $('.pgm-ajax-form').serialize(),
				security: '<?php echo $ajax_nonce; ?>' 
			},
			beforeSend: function() {
					jQuery("#loading").appendTo("#load").fadeIn('fast');
					jQuery("#formstatus").fadeOut("slow");
			},
			success: function(html){ 
				jQuery("#loading").appendTo("#load").fadeOut('slow');
				jQuery("#formstatus").html( html ); //show the html inside formstatus div
				jQuery("#formstatus").fadeIn("fast"); 
				//jQuery("#formstatus").fadeOut(5000); 	
			},
			error: function(xhr){
				alert('Error: ' + xhr.responseCode);
			}	             
		}); //close jQuery.ajax
		return false;
	});
	$(".button-reset").on("click", function(event) {
		jQuery("#formstatus").hide();
	});
});







