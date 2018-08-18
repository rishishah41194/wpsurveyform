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

		wp_localize_script( 'custom_js', 'ajax_object', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		) );

	}

	public function generate_survey_form( $attr ) {
		ob_start();
		global $wpdb;
		$form_id   = isset( $attr['form_id'] ) ? $attr['form_id'] : "";
		$form_name = isset( $attr['form_name'] ) ? $attr['form_name'] : "";

		$result_front = $wpdb->get_results( "SELECT * FROM wp_survey_form_data WHERE `id` = '$form_id' AND `survey_form_name` = '$form_name'", ARRAY_A );

		if ( ! empty( $result_front ) ) {
			$option_array = explode( ",", $result_front[0]['survey_form_option'] );
			$count        = 1;
			$cookie_name = "survey_form_result_cookie";
			if( !isset( $_COOKIE[$cookie_name] ) ) {
				?>
				<div class="main_user_block_section">
					<div class="wrapper">
						<label class=""><?php echo $result_front[0]['survey_form_question']; ?></label>
						<?php
						foreach ( $option_array as $val ) {
							?>
							<label class=""><input type="radio" name="survey_option" value="<?php echo $val; ?>"><?php echo $val; ?></label>
							<div class="prg_bar">
								<div class="<?php echo "progressbar_$count" ?>"></div>
							</div>
							<?php
							$count ++;
						}
						?>
					</div>
					<input type="hidden" name="hidden_id" class="hidden_form_id" value="<?php echo $form_id; ?>">
				</div>
				<?php
			} else {
				$cookie_name = isset( $_COOKIE[$cookie_name] ) ? stripslashes( $_COOKIE[$cookie_name] ) : "";
				$cookie_name_decoded = json_decode( $cookie_name );
				?>
				<div class="main_user_block_section display_cookie_value">
					<div class="wrapper">
						<label class=""><?php echo $result_front[0]['survey_form_question']; ?></label>
						<?php
						foreach ( $option_array as $val ) {
							?>
							<label class=""><input type="radio" class="survey_option" value="<?php echo $val; ?>" <?php if( $val === $cookie_name_decoded[2] ) { echo "checked='checked'"; }  ?>><?php echo $val; ?></label>
							<div class="prg_bar">
								<div class="<?php echo "progressbar_$count" ?>"></div>
							</div>
							<?php
							$count ++;
						}
						?>
					</div>
					<input type="hidden" class="hidden_cookie_value1"  value="<?php echo $cookie_name_decoded[0]; ?>" >
					<input type="hidden" class="hidden_cookie_value2" value="<?php echo $cookie_name_decoded[1]; ?>">
				</div>
				<?php
			}
		}

		$content = ob_get_contents();
		ob_clean();

		return $content;

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

		$option_one_value = get_option( "survey-$hidden_form_id-$option_array[0]" );
		$option_two_value = get_option( "survey-$hidden_form_id-$option_array[1]" );

		$total_count = $option_one_value + $option_two_value;

		$percentage_1 = ( $option_one_value * 100 ) / $total_count;
		$percentage_2 = ( $option_two_value * 100 ) / $total_count;

		$result['option_1'] = round( $percentage_1 );
		$result['option_2'] = round( $percentage_2 );

		header( "Content-type: application/json" );
		echo json_encode( $result );

		$cookie_name = "survey_form_result_cookie";
		$cookie_value = array( $result['option_1'], $result['option_2'], $option_value );

		$cookie_value_final = json_encode( $cookie_value );

		setcookie( $cookie_name, $cookie_value_final, time() + (86400 * 30), "/" );

		wp_die();
	}

}
