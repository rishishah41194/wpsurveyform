<?php

global $wpdb;
$sf_survey_form_data_table_name = $wpdb->prefix . "survey_form_data";
$sf_query_forget_result         = $wpdb->add_placeholder_escape( "SELECT id, survey_form_name, survey_form_enable_disable FROM $sf_survey_form_data_table_name", "" );
$sf_result_shortcode            = $wpdb->get_results( $sf_query_forget_result, ARRAY_A );

if ( ! empty( $sf_result_shortcode ) ) {
	?>
	<div class="display_survey_form_section">

		<?php
		/**
		 * Before Display Survey Form Table.
		 *
		 * @since 1.0.0
		 * @param array $sf_result_shortcode
		 */
		do_action( 'sf_before_display_survey_form_table', $sf_result_shortcode );
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
			foreach ( $sf_result_shortcode as $sf_result_shortcode_value ) {
				?>
				<tr>
					<td>
						<input type="text" id="myInput<?php esc_attr_e( $sf_result_shortcode_value['id'] ); ?>" value='[generate_survey_form form_id="<?php esc_attr_e( $sf_result_shortcode_value['id'] ); ?>" form_name="<?php esc_attr_e( $sf_result_shortcode_value['survey_form_name'] ); ?>"]' readonly>
					</td>
					<td>
						<div class="col-sm-3">
							<label class="switch">
								<input type="checkbox" <?php if ( "Enable" === $sf_result_shortcode_value['survey_form_enable_disable'] ) {
									echo "checked";
								} ?> class="checkbox_switch" id="<?php echo esc_attr_e( $sf_result_shortcode_value['id'] ); ?>">
								<span class="slider round"></span>
							</label>
						</div>
					</td>
					<td class="action-blk-td">
						<div class="action_block wrap">
							<label class="edit_survey_form button"><a href="/wp-admin/admin.php?page=add_new_survey_form&id=<?php esc_attr_e( $sf_result_shortcode_value['id'] ); ?>"><span class="dashicons dashicons-edit"></span></a></label>
							<label class="delete_survey_form button" data-value="<?php esc_attr_e( $sf_result_shortcode_value['id'] ); ?>"><span class="dashicons dashicons-trash"></span></label>
						</div>
					</td>
					<td class="copy_to_clip_board">
						<button class="copy copy-to-clipboard" value="Copy" id="<?php esc_attr_e( $sf_result_shortcode_value['id'] ); ?>"><span class="dashicons dashicons-admin-page"></span></button>
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
		 * @param array $sf_result_shortcode_value
		 */
		do_action( 'sf_after_display_survey_form_table', $sf_result_shortcode_value );
		?>
	</div>
	<?php

}
