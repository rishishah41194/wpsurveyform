jQuery( document ).ready(function() {

	jQuery("#disabled").find("input, select, button, textarea").attr("disabled",true);

	jQuery( "input[name=survey_option]" ).click(function() {

		var option_value = jQuery(this).val();
		var hidden_form_id = jQuery(".hidden_form_id").val();
		var hidden_form_name = jQuery(".hidden_form_name").val();
		
		jQuery.ajax({
			url: ajax_object.ajaxurl,
			type: 'POST',
			data:{
				action: 'sf_submit_survey_form_ajax',
				option_value: option_value,
				hidden_form_id: hidden_form_id,
				hidden_form_name: hidden_form_name,
			},
			success: function( data ) {
				document.location.reload();
			}

		});
	});

	});