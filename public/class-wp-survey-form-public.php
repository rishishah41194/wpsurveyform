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
			if( isset( $option_array ) ) {
				?>
				<div class="main_user_block_section">
					<div class="wrapper">
						<label class=""><?php esc_html_e($result_front[0]['survey_form_question'], 'wp-survey-form'); ?></label>
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
		$wp_option_key  = "surveyformid_" . $hidden_form_id;

		$get_option_value      = get_option( "survey-$hidden_form_id-$option_value" );
		$get_option_value_data = (int) $get_option_value;

		if ( ! empty( $get_option_value ) ) {
			$count = $get_option_value_data + 1;
			update_option( "survey-$hidden_form_id-$option_value", $count );
		} else {
			add_option( "survey-$hidden_form_id-$option_value", 1 );
		}

		$options_of_current_form = $wpdb->get_results( "SELECT * FROM wp_survey_form_data WHERE `id` = '$hidden_form_id' ", ARRAY_A );
		$option_string           = $options_of_current_form[0]['survey_form_option'];
		$option_array            = explode( ",", $option_string );
		
		$full_result_array = array();
		if( !empty( $option_array ) ) {
			foreach( $option_array as $option_array_value ) {
				$option_value = get_option( "survey-$hidden_form_id-$option_array_value" );
				
				$full_result_array["survey"] = $option_value;

			}

			echo "<pre>";
			print_r( $full_result_array );

		}

		wp_die();
	}

}
