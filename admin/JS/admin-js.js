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

	jQuery('.search_box_submit_btn').on('click', function() {

		var search_box_specialization = $(".search_box_specialization").val();
		var search_box_zipcode = $(".search_box_zipcode").val();


		alert( "vdsvbui" );
		jQuery("#add_new_survey_form").validate({

			// Specify the validation rules
			rules: {
				survey_name : "required",
				survey_question : "required",
				question_option : "required",
			},

			// Specify the validation error messages
			messages: {
				survey_name : "survey name is required",
				survey_question : "survey question is required",
				question_option : "question option level is required",
			},

			submitHandler: function(form) {
				form.submit();
			}
		});

	});
});