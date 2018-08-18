jQuery( document ).ready(function() {

	jQuery( "input[name=survey_option]" ).click(function() {

		var option_value = jQuery(this).val();
		var hidden_form_id = jQuery(".hidden_form_id").val();

		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',
			data:{
				action: 'submit_survey_form_ajax',
				option_value: option_value,
				hidden_form_id: hidden_form_id,
			},
			success: function( data ) {
				jQuery( ".progressbar_1" ).text( data['option_1'] + "%" );
				jQuery( ".progressbar_2" ).text( data['option_2'] + "%" );

				jQuery( ".progressbar_1" ).css( "background-color", "#50d818" );
				jQuery( ".progressbar_1" ).css( "width", data['option_1']+"%" );

				jQuery( ".progressbar_2" ).css( "background-color", "#50d818" );
				jQuery( ".progressbar_2" ).css( "width", data['option_2']+"%" );

			}

		});
	});

	var set_coockie_value1 = jQuery( ".hidden_cookie_value1" ).val();
	var set_coockie_value2 = jQuery( ".hidden_cookie_value2" ).val();

	if( set_coockie_value1 !== undefined && set_coockie_value2 !== undefined ) {

		jQuery(".survey_option").attr('disabled', true);


		jQuery( ".progressbar_1" ).text( set_coockie_value1 + "%" );
		jQuery( ".progressbar_2" ).text( set_coockie_value2 + "%" );

		jQuery( ".progressbar_1" ).css( "background-color", "#50d818" );
		jQuery( ".progressbar_1" ).css( "width", set_coockie_value1+"%" );

		jQuery( ".progressbar_2" ).css( "background-color", "#50d818" );
		jQuery( ".progressbar_2" ).css( "width", set_coockie_value2+"%" );

	}

	});