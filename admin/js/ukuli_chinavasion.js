jQuery( document ).ready(function() {
        jQuery("form.chinavasionapi").submit(function(event) {
            event.preventDefault();
            var chinavasionapi = jQuery( "input#chinavasionapi" ).val();
            var security = jQuery("form.chinavasionapi input[name=security]").val();
		    chinavasionapiAjax(chinavasionapi,security);
            });
        jQuery("button.chinavasion_order_button").click(function(event) {
            event.preventDefault();
            jQuery.blockUI();
            var ajaxurl = ajax_object.ajax_url;
            var order = jQuery( "input#chinavasion_order_id" ).val();
            var shipping = jQuery( "#chinavasion_shipping option:selected" ).text();
            var payment = jQuery( "#chinavasion_payment option:selected" ).text();
            var security = jQuery("#chinavasion_order_security").val();
            console.log(security);
            var data = {
				action: 'ukuli_chinavasion_order_action',
				order: order,
				shipping: shipping,
				payment: payment,
                security: security,
    		};
    	  jQuery.post(ajaxurl, data, function(response) {
    	      jQuery.unblockUI();
    	      if(response == "OK") {
    	          location.reload(true); 
    	      } else {
	              jQuery("#chinavasion-result").append(response);
    	      }
    	  });
        });
        jQuery("form.chinavasionimport").submit(function(event) {
            event.preventDefault();
            var chinavasionid = jQuery( "input#chinavasionid" ).val();
            var security = jQuery("form.chinavasionimport input[name=security]").val();
		    chinavasionAjax(chinavasionid,security);
        });
});

function chinavasionAjax(chinavasionid, security) {
    jQuery.blockUI();
    var ajaxurl = ajax_object.ajax_url;
    var data = {
		action: 'ukuli_chinavasion_action',
		chinavasionid: chinavasionid,
        security: security,
    };
    jQuery.post(ajaxurl, data, function(response) {
	   jQuery.unblockUI();
	   jQuery("#chinavasion").append(response);
    });
}

function chinavasionapiAjax(chinavasionapi, security) {
    var ajaxurl = ajax_object.ajax_url;
    var data = {
		action: 'ukuli_chinavasion_api_action',
		chinavasionapi: chinavasionapi,
        security: security,
    };
    jQuery.post(ajaxurl, data, function(response) {
		location.reload(true); 
    });
}