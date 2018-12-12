<?php
global $wpdb;
$sf_form_id              = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_STRING );
$sf_result_form          = $wpdb->get_results( "SELECT * FROM `wp_survey_form_data` WHERE `id` = '$sf_form_id'", ARRAY_A );
$sf_survey_option_string = $sf_result_form[0]['survey_form_option'];
$sf_survey_option_array  = explode( ",", $sf_survey_option_string );

?>
<div class="main_survey_form">
	<div class="r-group">
		<div class="wrap">
			<h1><?php esc_html_e( 'Add New Survey Form', 'wp-survey-form' ); ?></h1>
			<div class="form">
				<form action='<?php echo get_admin_url(); ?>admin-post.php' method="post" name="survey_form" id="add_new_survey_form" class="repeater">

					<div class="active_deactive_buttons">
						<table class="status_table">
							<tr>
								<th><?php esc_html_e( "Enable / Disable The Form:", 'wp-survey-form' ) ?></th>
								<td>
									<label><input type="radio" name="enable/disable" value="Enable" class="survey_enable_disable" <?php if ( $sf_result_form[0]['survey_form_enable_disable'] === "Enable" ) {
											echo "checked";
										} ?> <?php if ( empty( $sf_form_id ) ) {
											echo "checked";
										} ?>>Enable</label>
									<label><input type="radio" name="enable/disable" value="Disable" class="survey_enable_disable" <?php if ( $sf_result_form[0]['survey_form_enable_disable'] === "Disable" ) {
											echo "checked";
										} ?>>Disable</label>
								</td>
							</tr>
						</table>
					</div>

					<table class="survey_form_table form-table disable_class">
						<tr>
							<th><label><?php esc_html_e( 'Survey Name', 'wp-survey-form' ); ?></label></th>
							<td>
								<input type="text" name="survey_name" placeholder="Enter your survey name" class="survey_name" value="<?php echo isset( $sf_result_form[0]['survey_form_name'] ) ? $sf_result_form[0]['survey_form_name'] : "" ?>" required>
							</td>
						</tr>
						<tr>
							<th><label><?php esc_html_e( 'Survey Question', 'wp-survey-form' ); ?></label></th>
							<td>
								<input type="text" name="survey_question" placeholder="Enter your survey question" class="survey_name" value="<?php echo isset( $sf_result_form[0]['survey_form_question'] ) ? $sf_result_form[0]['survey_form_question'] : "" ?>" required>
							</td>
						</tr>
						<tr class="question_form_repeater">
							<th><label><?php esc_html_e( 'Add Question Option', 'wp-survey-form' ); ?></label></th>
							<?php
							if ( ! empty( $sf_survey_option_array ) ) {
								$count = 1;
								foreach ( $sf_survey_option_array as $sf_survey_option_array_result ) {
									?>
									<td class="question_option <?php if ( $count > 1 ) {
										echo "newClass";
									} ?>">
										<input type="text" name="question_option[]" placeholder="Enter your survey option" class="survey_name option_class" id="1" value="<?php echo $sf_survey_option_array_result; ?>" required>
										<lable class="add_option"><span class="dashicons dashicons-plus"></span></lable>
										<lable class="remove_option"><span class="dashicons dashicons-trash" id="surveyformid_<?php echo $sf_form_id; ?>_<?php echo $sf_survey_option_array_result; ?>"></span></lable>
									</td>
									<?php
									$count ++;
								}
							}
							?>
						</tr>
					</table>
					<input type='hidden' name='action' value='submit-form'>
					<input type='hidden' name='id' value='<?php echo esc_attr_e( $sf_form_id ); ?>'>
					<input type="hidden" name="question_option[]" placeholder="Enter your survey option" class="question_option_hidden" value="">
					<input type="submit" name="submit" value="submit" class="button button-primary" id="add_update_survey_form">
				</form>
			</div>
		</div>
	</div>
</div>