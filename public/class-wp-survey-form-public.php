<?php

/**
 *  Class for all public functions.
 *
 */

class sf_survey_form_public {

	/**
	 * Initializes WordPress hooks.
	 *
	 */
	function __construct() {
		add_shortcode( 'generate_survey_form', array( $this, 'generate_survey_form' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_custom_sf_public_style' ) );
		add_action( 'wp_ajax_sf_submit_survey_form_ajax', array( $this, 'sf_submit_survey_form_ajax' ) );
		add_action( 'wp_ajax_nopriv_sf_submit_survey_form_ajax', array( $this, 'sf_submit_survey_form_ajax' ) );
	}

	/**
	 *  Enqueue Scrips and CSS files.
	 *
	 */
	public function load_custom_sf_public_style() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_style( 'public-css', plugins_url( '/CSS/public-style.css', __FILE__ ) );
		wp_enqueue_script( 'public-JS', plugins_url( '/JS/public-JS.js', __FILE__ ), array(), false, true );
		wp_localize_script( 'public-JS', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), ) );
	}

	/**
	 *  Enqueue Scrips and CSS files.
	 *
	 * @function generate_survey_form()
	 * @attr     int form_id
	 * @attr     string form_name
	 * @return string
	 */
	public function generate_survey_form( $attr ) {
		global $wpdb;

		ob_start();
		$form_id      = isset( $attr['form_id'] ) ? $attr['form_id'] : "";
		$form_name    = isset( $attr['form_name'] ) ? $attr['form_name'] : "";
		$get_cookie   = isset( $_COOKIE['survey_form_cookie'] ) ? $_COOKIE['survey_form_cookie'] : "";
		$jsonData     = stripslashes( html_entity_decode( $get_cookie ) );
		$final_result = json_decode( $jsonData, true );

		if ( ! isset ( $final_result ) && empty( $final_result ) ) {
			$sf_table_name       = $wpdb->prefix . "survey_form_data";
			$result_front = $wpdb->get_results( "SELECT * FROM $sf_table_name WHERE `id` = '$form_id' AND `survey_form_name` = '$form_name'", ARRAY_A );
			$option_array = explode( ",", $result_front[0]['survey_form_option'] );
			$count        = 1;
			if ( isset( $result_front ) && ! empty( $result_front ) && $result_front[0]['survey_form_enable_disable'] === "Enable" ) {
				?>
				<div class="main_user_block_section">
					<div class="wrapper">
						<label class=""><?php esc_html_e( $result_front[0]['survey_form_question'], 'wp-survey-form' ); ?></label>
						<div class="option_group">
							<?php
							foreach ( $option_array as $val ) {
								?>
								<label class=""><input type="radio" name="survey_option" value="<?php echo $val; ?>"><?php echo $val; ?></label>
								<input type="hidden" class="survey-<?php echo "{$form_id}-{$val}"; ?>" value="">
								<div class="<?php echo "surveyformid_{$form_id}_{$val}" ?>"></div>
								<?php
								$count ++;
							}
							?>
						</div>
					</div>
					<input type="hidden" name="hidden_id" class="hidden_form_id" value="<?php echo $form_id; ?>">
					<input type="hidden" name="hidden_name" class="hidden_form_name" value="<?php echo $form_name; ?>">
				</div>
				<?php
			}
		} else {

			$sf_table_name_survey_form_data       = $wpdb->prefix . "survey_form_data";
			$sf_table_name_survey_form_data_count       = $wpdb->prefix . "survey_form_data_count";
			$result_front = $wpdb->get_results( "SELECT * FROM  $sf_table_name_survey_form_data WHERE `id` = '$form_id' AND `survey_form_name` = '$form_name'", ARRAY_A );

			if( isset( $result_front ) && !empty( $result_front ) ) {
				?>
				<div class="main_user_block_section" id="disabled">
					<label class=""><?php esc_html_e( $result_front[0]['survey_form_question'], 'wp-survey-form' ); ?></label>
					<?php
					$sf_options_string = $result_front[0]['survey_form_option'];
					$sf_options_array = explode( ",", $sf_options_string );
					$total_count_vote    = "";
					foreach ( $sf_options_array as $result_front_result ) {
						$sf_option_key = "surveyformid_".$form_id."_".$result_front_result;
						$result_from_coockie = $wpdb->get_results( "SELECT `form_option_count` FROM $sf_table_name_survey_form_data_count WHERE `form_option_name` = '$sf_option_key'", ARRAY_A );
						$total_count_vote +=  $result_from_coockie[0]['form_option_count'];
					}

					$COOKIE_option_value = explode( "_", $final_result[0] );
					$count = 0;
					foreach ( $sf_options_array as $result_front_result ) {
						$sf_option_key = "surveyformid_".$form_id."_".$result_front_result;
						$result_from_coockie = $wpdb->get_results( "SELECT `form_option_count` FROM $sf_table_name_survey_form_data_count WHERE `form_option_name` = '$sf_option_key'", ARRAY_A );
						if( isset( $result_from_coockie[0]['form_option_count'] ) && !empty( $result_from_coockie[0]['form_option_count'] ) ) {
							$single_option_count = round( $result_from_coockie[0]['form_option_count'] * 100 / $total_count_vote );
							?>
							<div class="wrapper">
								<label class=""><input type="radio" name="survey_option" value="<?php echo $result_front_result[2]; ?>" <?php if ( $COOKIE_option_value[2] === $result_front_result ) {
										echo "checked";
									} ?>><?php echo $result_front_result; ?></label>
								<input type="hidden" class="<?php echo $result_front_result; ?>" value="">
								<div class="prg_bar" id="<?php  ?>">
									<div id="progress_bar" class="<?php echo $result_front_result ?>" style="width:<?php echo $single_option_count . "%"; ?>; background:#05f50f;display: block;height: 30px;border-radius: 50px;"><?php echo isset( $single_option_count ) ? $single_option_count . "%" : "0%"; ?></div>
								</div>
								<?php
								$count ++;
								?>
							</div>
							<?php
						} else {
							?>
							<div class="wrapper">
								<label class=""><input type="radio" name="survey_option" value="<?php echo $result_front_result[2]; ?>" <?php if ( $COOKIE_option_value[2] === $result_front_result ) {
										echo "checked";
									} ?>><?php echo $result_front_result; ?></label>
								<input type="hidden" class="<?php echo $result_front_result; ?>" value="">
								<div class="prg_bar" id="<?php  ?>">
									<div id="progress_bar" class="<?php echo $result_front_result ?>" style="width: 0px; background:#05f50f;display: block;height: 30px;border-radius: 50px;">0%</div>
								</div>
								<?php
								$count ++;
								?>
							</div>
							<?php
						}
						?>
						<input type="hidden" name="hidden_id" class="hidden_form_id" value="<?php echo $form_id; ?>">
						<input type="hidden" name="hidden_name" class="hidden_form_name" value="<?php echo $form_name; ?>">
						<?php
					}

					?>
				</div>
				<?php

			}

		}

		$content = ob_get_contents();
		ob_clean();

		/**
		 * Update Servery Form HTML Content.
		 *
		 * @since 1.0.0
		 *
		 * @param string $content
		 * @param array  $attr
		 *
		 * @return string $content
		 *
		 * */
		return apply_filters( 'sf_form_output', $content, $attr );

	}

	function sf_submit_survey_form_ajax() {

		global $wpdb;
		$option_value      = isset( $_POST['option_value'] ) ? $_POST['option_value'] : "";
		$hidden_form_id    = isset( $_POST['hidden_form_id'] ) ? $_POST['hidden_form_id'] : "";
		$hidden_form_name  = isset( $_POST['hidden_form_name'] ) ? $_POST['hidden_form_name'] : "";
		$sf_option_key     = "surveyformid_{$hidden_form_id}_{$option_value}";
		$survey_form_array = array();

		array_push( $survey_form_array, $sf_option_key, $hidden_form_id );

		$table_name_survey_form_data_count = $wpdb->prefix . "survey_form_data_count";
		$options_of_current_form = $wpdb->get_results( "SELECT form_option_count FROM $table_name_survey_form_data_count WHERE `form_option_name` = '$sf_option_key' ", ARRAY_A );

		if ( isset( $options_of_current_form ) && ! empty( $options_of_current_form ) ) {

			$increse_count = $options_of_current_form[0]['form_option_count'];
			$increse_count ++;

			$wpdb->update( 'wp_survey_form_data_count', array(
				'survey_form_id'    => $hidden_form_id,
				'form_option_id'    => $hidden_form_id,
				'form_option_name'  => $sf_option_key,
				'form_option_count' => $increse_count,
			), array( 'form_option_name' => $sf_option_key ) );

		} else {
			$wpdb->insert( 'wp_survey_form_data_count', array(
				'survey_form_id'    => $hidden_form_id,
				'form_option_id'    => $hidden_form_id,
				'form_option_name'  => $sf_option_key,
				'form_option_count' => 1,
			) );
			$record_id = $wpdb->insert_id;
		}

		setcookie( 'survey_form_cookie', json_encode( $survey_form_array ), ( time() + (10 * 365 * 24 * 60 * 60 ) ), "/" );

		wp_die();
	}
}

$public_class = new sf_survey_form_public();
