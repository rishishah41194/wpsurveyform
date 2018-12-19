jQuery( document ).ready(function() {

	jQuery("#disabled").find("input, select, button, textarea").attr("disabled",true);

	jQuery( "input[name=survey_option]" ).click(function() {

		var option_value = jQuery(this).val();
		var hidden_form_id = jQuery(".hidden_form_id").val();
		var hidden_form_name = jQuery(".hidden_form_name").val();
		
		var data = {
			'action': 'my_action',
			'whatever': 1234
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(the_ajax_script.ajaxurl, data, function(response) {
			alert('Got this from the server: ' + response);
		});
		
	});

	});