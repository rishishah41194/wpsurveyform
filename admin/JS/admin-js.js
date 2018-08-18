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
});