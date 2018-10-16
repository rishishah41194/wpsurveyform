<?php

class wp_survey_form_public {

	/**
	 * Initializes WordPress hooks
	 */
	function __construct() {
		add_shortcode( 'generate_survey_form', array( $this, 'generate_survey_form' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_custom_wp_public_style' ) );
		add_action( 'wp_head', array( $this, 'codecanal_ajaxurl' ) );
		add_action( 'wp_ajax_submit_survey_form_ajax', array( $this, 'submit_survey_form_ajax' ) );
		add_action( 'wp_ajax_nopriv_submit_survey_form_ajax', array( $this, 'submit_survey_form_ajax' ) );
	}

	public function codecanal_ajaxurl() {
		echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '";
         </script>';
	}

	public function load_custom_wp_public_style() {
		wp_enqueue_style( 'public-css', plugins_url( '/CSS/public-style.css', __FILE__ ) );
		wp_enqueue_style( 'UI-css', 'http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' );
		wp_enqueue_script( 'public-JS', plugins_url( '/JS/public-JS.js', __FILE__ ), array(), false, true );
		wp_enqueue_script( 'UI-JS', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js', array(), false, true );
		wp_localize_script( 'custom_js', 'ajax_object', array('ajaxurl' => admin_url( 'admin-ajax.php' ), ) );
	}

	public function generate_survey_form( $attr ) {
		global $wpdb;

		ob_start();
		$form_id   = isset( $attr['form_id'] ) ? $attr['form_id'] : "";
		$form_name = isset( $attr['form_name'] ) ? $attr['form_name'] : "";

			$result_front = $wpdb->get_results( "SELECT * FROM wp_survey_form_data WHERE `id` = '$form_id' AND `survey_form_name` = '$form_name'", ARRAY_A );

			$option_array = explode( ",", $result_front[0]['survey_form_option'] );
			$count        = 1;
			$cookie_name = "survey_form_result_cookie";
			if( isset( $result_front ) && !empty( $result_front ) && $result_front[0]['survey_form_enable_disable'] === "Enable" ) {
				?>
				<div class="main_user_block_section">
					<div class="wrapper">
						<label class=""><?php esc_html_e( $result_front[0]['survey_form_question'], 'wp-survey-form' ); ?></label>
						<?php
						foreach ( $option_array as $val ) {
							?>
							<label class=""><input type="radio" name="survey_option" value="<?php echo $val; ?>"><?php echo $val; ?></label>
							<input type="hidden" class="survey-<?php echo "{$form_id}-{$val}"; ?>" value="">
							<div class="prg_bar">
								<div class="<?php echo "progressbar_{$count}" ?>"></div>
							</div>
							<?php
							$count ++;
						}
						?>
					</div>
					<input type="hidden" name="hidden_id" class="hidden_form_id" value="<?php echo $form_id; ?>">
					<input type="hidden" name="hidden_name" class="hidden_form_name" value="<?php echo $form_name; ?>">
				</div>
				<?php
			}

			$content = ob_get_contents();
			ob_clean();
	
		
		/**
		 * Update Survery Form HTML Content.
		 * 
		 * @since 1.0.0
		 * 
		 * @param string $content
		 * @param array $attr
		 * 
		 * @return string $content
		 * 
		 * */		
		return apply_filters('sf_form_output',$content,$attr );

	}

	public function submit_survey_form_ajax() {

		global $wpdb;

		$option_value   = isset( $_POST['option_value'] ) ? $_POST['option_value'] : "";
		$hidden_form_id = isset( $_POST['hidden_form_id'] ) ? $_POST['hidden_form_id'] : "";
		$hidden_form_name = isset( $_POST['hidden_form_name'] ) ? $_POST['hidden_form_name'] : "";
		$wp_option_key  = "surveyformid_{$hidden_form_id}_{$option_value}";

		
		$options_of_current_form = $wpdb->get_results( "SELECT form_option_count FROM wp_survey_form_data_count WHERE `form_option_name` = '$wp_option_key' ", ARRAY_A );

		if( isset( $options_of_current_form ) && !empty( $options_of_current_form ) ) {
			
			$increse_count = $options_of_current_form[0]['form_option_count'];
			$increse_count++;

			$wpdb->update( 'wp_survey_form_data_count', array( 
				'survey_form_id'           => $hidden_form_id,
				'form_option_id'           => $hidden_form_id,
				'form_option_name'           => $wp_option_key,
				'form_option_count'           => $increse_count,
			), array( 'form_option_name' => $wp_option_key ) );

		} else {
			$wpdb->insert( 'wp_survey_form_data_count', array(
				'survey_form_id'           => $hidden_form_id,
				'form_option_id'           => $hidden_form_id,
				'form_option_name'           => $wp_option_key,
				'form_option_count'           => 1,
			) );
			$record_id = $wpdb->insert_id;
		}


		wp_die();
	}

}
