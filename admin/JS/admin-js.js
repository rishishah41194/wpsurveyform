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
		
		if( enable_disable_value === "Enable" ) {
			jQuery(".survey_form_table ").removeClass("disable_class");
		} else {
			jQuery(".survey_form_table ").addClass("disable_class");
		}

	});

	var enable_disable_value = jQuery("input[name='enable/disable']:checked").val();

	jQuery(document).on("click", ".add_option" , function() {
		jQuery('.question_option:last').clone().addClass('newClass').appendTo('.question_form_repeater');
		jQuery('.option_class:last').val("");
	});

	jQuery(document).on("click", ".remove_option" , function() {
		jQuery(this).closest("td").remove();
	});

	jQuery('.checkbox_switch').change(function() {
		
		var sf_active_status = "";
		var sf_shortcode_id = jQuery(this).attr("id");
		
		if ( jQuery(this).is(':checked') ) {
			sf_active_status = "Enable";
		} else {
			sf_active_status = "Disable";
		}

		var ajaxurl = "http://wpregisterform.local/wp-admin/admin-ajax.php";
		
		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',
			data:{ 
			  action: 'sf_active_status_ajax_action',
			  sf_active_status: sf_active_status,
			  sf_shortcode_id: sf_shortcode_id,
			},
			success: function( data ){
				window.location.reload();
			}

		  });

	});
	
	var enable_disable_value = jQuery("input[name='enable/disable']:checked").val();

	if( enable_disable_value === "Enable" ) {
		jQuery(".survey_form_table ").removeClass("disable_class");
	} else {
		jQuery(".survey_form_table ").addClass("disable_class");
	}

	jQuery(".copy").click(function() {
		var id = jQuery(this).attr("id");
		var copyText = document.getElementById( "myInput"+id );
		copyText.select();
		document.execCommand("copy");
	});

});