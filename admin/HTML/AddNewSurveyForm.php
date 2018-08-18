<?php
global $wpdb;
$id = isset( $_GET['id'] ) ? $_GET['id'] : "";
$result = $wpdb->get_results( "SELECT * FROM `wp_survey_form_data` WHERE `id` = '$id'", ARRAY_A );

?>
<div class="main_survey_form">
	<div class="wrapper">
		<h1>Add New Survey Form</h1>
		<div class="form">
			<form action='<?php echo get_admin_url(); ?>admin-post.php' method="post" name="survey_form">
				<table class="survey_form_table">
					<tr>
						<td><label>Survey Name</label></td>
						<td><input type="text" name="survey_name" placeholder="Enter Your Survey Name:" class="survey_name" value="<?php echo isset( $result[0]['survey_form_name'] ) ? $result[0]['survey_form_name'] : "" ?>"></td>
					</tr>
					<tr>
						<td><label>Survey Question</label></td>
						<td><input type="text" name="survey_question" placeholder="Enter Your Survey Question:" class="survey_name" value="<?php echo isset( $result[0]['survey_form_question'] ) ? $result[0]['survey_form_question'] : "" ?>"></td>
					</tr>
					<tr>
						<td><label>Add Question Option</label></td>
						<td><input type="text" name="question_option" placeholder="Enter Your Survey Option:" class="survey_name question_option" value="<?php echo isset( $result[0]['survey_form_option'] ) ? $result[0]['survey_form_option'] : "" ?>"></td>
					</tr>
				</table>
				<input type='hidden' name='action' value='submit-form'>
				<input type='hidden' name='id' value='<?php echo $id; ?>'>
				<input type="submit" name="submit" value="submit" class="button button-primary">
			</form>
		</div>
	</div>
</div>