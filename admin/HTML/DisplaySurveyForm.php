<?php

global $wpdb;
$result = $wpdb->get_results( "SELECT id, survey_form_name, survey_form_enable_disable FROM wp_survey_form_data", ARRAY_A );

if( !empty( $result ) ) {
	?>
	<div class="display_survey_form_section">
		
		<?php

			/**
			 * Before Display Survey Form Table.
			 * 
			 * @since 1.0.0
			 * 
			 * @param array $result
			 * 
			 */
			do_action( 'before_display_survey_form_table', $result );
		 ?>
		<table class="display_survey_form_table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Shortcode', 'wp-survey-form' ); ?></th>
					<th><?php esc_html_e( 'Status', 'wp-survey-form' ); ?></th>
					<th><?php esc_html_e( 'Action', 'wp-survey-form' ); ?></th>
					<th><?php esc_html_e( 'Copy', 'wp-survey-form' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach ( $result as $result_value ) {
						?>
							<tr>
								<td>
									<input type="text" id="myInput<?php esc_attr_e( $result_value['id'] ); ?>" value='[generate_survey_form form_id="<?php esc_attr_e( $result_value['id'] ); ?>" form_name="<?php esc_attr_e( $result_value['survey_form_name'] ); ?>"]' readonly>
								</td>
								<td>
								<div class="col-sm-3">
								<label class="switch">
									<input type="checkbox" <?php if( $result_value['survey_form_enable_disable'] === "Enable" ) { echo "checked"; } ?> class="checkbox_switch" id="<?php echo esc_attr_e( $result_value['id'] ); ?>">
									<span class="slider round"></span>
								</label>
								</div>
								</td>
								<td class="action-blk-td">
									<div class="action_block wrap">
										<label class="edit_survey_form button"><a href="/wp-admin/admin.php?page=add_new_survey_form&id=<?php esc_attr_e( $result_value['id'] ); ?>"><span class="dashicons dashicons-edit"></span></a></label>
										<label class="delete_survey_form button" data-value="<?php esc_attr_e( $result_value['id'] ); ?>"><span class="dashicons dashicons-trash"></span></label>
									</div>
								</td>
								<td class="copy_to_clip_board">
									<button class="copy copy-to-clipboard" value="Copy" id="<?php esc_attr_e( $result_value['id'] ); ?>"><span class="dashicons dashicons-admin-page"></span></button>
								</td>
							</tr>

						<?php
					}
				?>
			</tbody>
		</table>
		<?php 

			/**
			 * After Display Survey Form Table.
			 * 
			 * @since 1.0.0
			 * 
			 * @param array $result
			 * 
			 */
			do_action( 'after_display_survey_form_table', $result );
		 ?>
	</div>
<?php

}
