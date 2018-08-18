<?php
global $wpdb;
$result = $wpdb->get_results( "SELECT id, survey_form_name FROM wp_survey_form_data", ARRAY_A );
if( !empty( $result ) ) {
	?>
	<div class="display_survey_form_section">
		<table class="display_survey_form_table">
			<thead>
				<tr>
					<th>shortcode</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach ( $result as $result_value ) {
						?>
							<tr>
								<td>
									<input type="text" value='[generate_survey_form form_id="<?php echo $result_value['id']; ?>" form_name="<?php echo $result_value['survey_form_name']; ?>"]' readonly>
								</td>
								<td>
									<div class="action_block wrap">
										<label class="edit_survey_form button"><a href="/wp-admin/admin.php?page=add_new_survey_form&id=<?php echo $result_value['id']; ?>">Edit</a></label>
										<label class="delete_survey_form button" data-value="<?php echo $result_value['id']; ?>">Delete</label>
									</div>
								</td>
							</tr>
						<?php
					}
				?>
			</tbody>
		</table>
	</div>
<?php

}
