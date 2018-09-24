<?php
global $wpdb;
$id = isset( $_GET['id'] ) ? $_GET['id'] : "";
$result = $wpdb->get_results( "SELECT * FROM `wp_survey_form_data` WHERE `id` = '$id'", ARRAY_A );
$survey_option_string = $result[0]['survey_form_option'];
$survey_option_array = explode( ",", $survey_option_string );

?>
<div class="main_survey_form">
	<div class="r-group">
	<div class="wrap">
		<h1>Add New Survey Form</h1>
		<div class="form">
			<form action='<?php echo get_admin_url(); ?>admin-post.php' method="post" name="survey_form" id="add_new_survey_form" class="repeater">
				<table class="survey_form_table form-table">
					<tr>
						<th><label>Enable/Disable</label></th>
						<td>
							<label><input type="radio" name="enable/disable" value="Enable" class="survey_enable_disable" <?php if( $result[0]['survey_form_enable_disable'] === "Enable" ) { echo "checked"; } ?> <?php if( empty( $id ) ) { echo "checked"; } ?>>Enable</label>
							<label><input type="radio" name="enable/disable" value="Disable" class="survey_enable_disable" <?php if( $result[0]['survey_form_enable_disable'] === "Disable" ) { echo "checked"; } ?>>Disable</label>
						</td>
					</tr>
					<tr>
						<th><label>Survey Name</label></th>
						<td><input type="text" name="survey_name" placeholder="Enter your survey name" class="survey_name" value="<?php echo isset( $result[0]['survey_form_name'] ) ? $result[0]['survey_form_name'] : "" ?>"  required></td>
					</tr>
					<tr>
						<th><label>Survey Question</label></th>
						<td><input type="text" name="survey_question" placeholder="Enter your survey question" class="survey_name" value="<?php echo isset( $result[0]['survey_form_question'] ) ? $result[0]['survey_form_question'] : "" ?>" required></td>
					</tr>
					<tr class="question_form_repeater">
						<th><label>Add Question Option</label></th>

						<?php
						if( !empty( $survey_option_array ) ) {
							$count = 1;
							foreach ( $survey_option_array as $survey_option_array_result ) {
								?>
								<td class="question_option <?php if( $count > 1 ){ echo "newClass"; }  ?>"><input type="text" name="question_option[]" placeholder="Enter your survey option" class="survey_name option_class" value="<?php echo $survey_option_array_result; ?>" required>
									<lable class="add_option">Add</lable>
									<lable class="remove_option">Remove</lable>
								</td>
								<?php
								$count++;
							}
						}
						?>
					</tr>
				</table>
				<input type='hidden' name='action' value='submit-form'>
				<input type='hidden' name='id' value='<?php echo $id; ?>'>
				<input type="hidden" name="question_option[]" placeholder="Enter your survey option" class="question_option_hidden" value="">
				<input type="submit" name="submit" value="submit" class="button button-primary">
			</form>
		</div>
	</div>
	</div>
</div>