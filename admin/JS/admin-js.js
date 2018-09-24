jQuery( document ).ready(function() {
	jQuery(".delete_survey_form").click(function(){
		var id = jQuery(this).attr( "data-value" );
		if ( confirm( "Delete this data?" ) ) {
			var data = {
				'action': 'delete_form_data_action',
				'id': id
			};
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response) {
				window.location.href = "/wp-admin/admin.php?page=display_survey_form";
			});
		}
	});

	jQuery("input[name='enable/disable']").change(function() {

		var enable_disable_value = jQuery(this).val();
		
		if( enable_disable_value === "Disable" ) {
			jQuery( ".survey_name" ).prop('readonly', true);
		} else {
			jQuery( ".survey_name" ).prop('readonly', false);
		}

	});

	var enable_disable_value = jQuery("input[name='enable/disable']:checked").val();

	if( enable_disable_value === "Disable" ) {
		jQuery( ".survey_name" ).prop('readonly', true);
	}

	jQuery(document).on("click", ".add_option" , function() {
		jQuery('.question_option:last').clone().addClass('newClass').appendTo('.question_form_repeater');
		jQuery('.option_class:last').val("");
	});

	jQuery(document).on("click", ".remove_option" , function() {
		jQuery(this).closest("td").remove();
	});
	
});


