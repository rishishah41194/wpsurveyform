<?php

/**
 * sf_survey_form_admin class for all the admin actions and functions.
 *
 */
class sf_survey_form_admin {

	/**
	 * Initializes WordPress hooks.
	 *
	 */
	function __construct() {
		add_action( 'admin_menu', array( $this, 'sf_sf_survey_form_admin_side' ) );
		add_action( 'admin_print_styles', array( $this, 'sf_load_custom_sf_admin_style' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'sf_load_custom_sf_admin_script' ) );
		add_action( 'admin_post_submit-form', array( $this, 'sf_survey_form_add_record' ) );
		add_action( 'admin_post_nopriv_submit-form', array( $this, 'sf_survey_form_add_record' ) );
		add_action( 'wp_ajax_delete_form_data_action', array( $this, 'sf_delete_form_data_action' ) );
		add_action( 'wp_ajax_nopriv_delete_form_data_action', array( $this, 'sf_delete_form_data_action' ) );
		add_action( 'wp_ajax_sf_active_status_ajax_action', array( $this, 'sf_active_status_ajax_action' ) );
		add_action( 'wp_ajax_sf_closest_option_name_remove', array( $this, 'sf_closest_option_name_remove' ) );
		add_action( 'wp_ajax_sf_reset_option_count', array( $this, 'sf_reset_option_count' ) );
		add_action( 'wp_ajax_nopriv_sf_reset_option_count', array( $this, 'sf_reset_option_count' ) );
	}

	/**
	 * Added Pages in Menu for Settings.
	 *
	 */
	public function sf_sf_survey_form_admin_side() {
		add_menu_page( 'Add New Survey Form', 'Add New Survey Form', 'administrator', 'add_new_survey_form', array( $this, 'sf_add_new_survey_form' ), 'dashicons-forms' );
		add_submenu_page( 'add_new_survey_form', 'Display Survey Form', 'Display Survey Form', 'manage_options', 'display_survey_form', array( $this, 'sf_display_survey_form' ) );
	}

	/**
	 * sf_add_new_survey_form function for call Add New Survey Form template.
	 *
	 */
	public function sf_add_new_survey_form() {
		require_once( sf_survey_form_path . '/admin/HTML/AddNewSurveyForm.php' );
	}

	/**
	 * sf_display_survey_form function for call Display Survey Form template.
	 *
	 */
	public function sf_display_survey_form() {
		require_once( sf_survey_form_path . '/admin/HTML/DisplaySurveyForm.php' );
	}

	/**
	 * sf_load_custom_sf_admin_style function for load admin style sheet.
	 *
	 */
	public function sf_load_custom_sf_admin_style() {
		wp_enqueue_style( 'admin-css', plugins_url( '/CSS/admin-style.css', __FILE__ ) );
	}

	/**
	 * sf_load_custom_sf_admin_script function for load admin JS sheet.
	 *
	 */
	public function sf_load_custom_sf_admin_script() {
		wp_enqueue_script( 'my_custom_script', plugins_url( '/JS/admin-js.js', __FILE__ ) );
		wp_enqueue_script( 'ajaxHandle' );
		wp_localize_script( 'ajaxHandle', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin_ajax.php' ) ) );
	}

	/**
	 * sf_survey_form_add_record function for insert form record.
	 *
	 */
	public function sf_survey_form_add_record() {
		global $wpdb;

		// Get all the values from the Add form.
		$survey_name                = filter_input( INPUT_POST, 'survey_name', FILTER_SANITIZE_STRING );
		$survey_question            = filter_input( INPUT_POST, 'survey_question', FILTER_SANITIZE_STRING );
		$survey_form_enable_disable = filter_input( INPUT_POST, 'enable/disable', FILTER_SANITIZE_STRING );
		$survey_form_id             = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_STRING );

		// For sanitize all the options.
		$question_option                  = isset( $_POST['question_option'] ) ? $_POST['question_option'] : "";
		$question_option_remove_blank     = array_filter( $question_option );
		$question_option_string           = implode( ",", $question_option_remove_blank );
		$question_option_string_sanitized = strip_tags( $question_option_string, 'wp-survey-form' );

		// Add/Update records to the database.
		$sf_table_name = $wpdb->prefix . "survey_form_data";
		if ( ! empty( $survey_form_id ) ) {
			$wpdb->query( $wpdb->prepare( "update $sf_table_name SET survey_form_name = %s, survey_form_question = %s, survey_form_option = %s, survey_form_enable_disable = %s WHERE id = %d ", array(
					$survey_name,
					$survey_question,
					$question_option_string_sanitized,
					$survey_form_enable_disable,
					$survey_form_id,
				) ) );
			wp_safe_redirect( "/wp-admin/admin.php?page=add_new_survey_form&id=$survey_form_id" );
		} else {
			$wpdb->query( $wpdb->prepare( "INSERT INTO $sf_table_name ( survey_form_name, survey_form_question, survey_form_option, survey_form_enable_disable ) VALUES ( %s, %s, %s, %s ) ", array(
					$survey_name,
					$survey_question,
					$question_option_string_sanitized,
					$survey_form_enable_disable,
				) ) );
			wp_safe_redirect( "/wp-admin/admin.php?page=display_survey_form" );
		}
	}

	/**
	 * sf_delete_form_data_action function for delete form record.
	 *
	 */
	public function sf_delete_form_data_action() {
		global $wpdb;

		$survey_form_id = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_STRING );
		$sf_table_name  = $wpdb->prefix . "survey_form_data";

		//  Delete whole data of form.
		if ( ! empty( $survey_form_id ) ) {
			$wpdb->query( $wpdb->prepare( "DELETE FROM $sf_table_name WHERE id = %d", array( $survey_form_id ) ) );
		}
		wp_die();
	}

	/**
	 * sf_active_status_ajax_action function for uodate Active status of form.
	 *
	 */
	function sf_active_status_ajax_action() {

		global $wpdb;

		$sf_shortcode_id  = filter_input( INPUT_POST, 'sf_shortcode_id', FILTER_SANITIZE_STRING );
		$sf_active_status = filter_input( INPUT_POST, 'sf_active_status', FILTER_SANITIZE_STRING );
		$sf_table_name    = $wpdb->prefix . "survey_form_data";

		// Change Active status of particular form.
		if ( ! empty( $sf_shortcode_id ) && ! empty( $sf_active_status ) ) {
			$wpdb->query( $wpdb->prepare( "update $sf_table_name SET survey_form_enable_disable = %s WHERE id = %d ", array( $sf_active_status, $sf_shortcode_id ) ) );
		}

		wp_die();

	}

	/**
	 * sf_closest_option_name_remove function for remove option of any form.
	 *
	 */
	function sf_closest_option_name_remove() {

		global $wpdb;

		$closest_option_name = filter_input( INPUT_POST, 'closest_option_name', FILTER_SANITIZE_STRING );
		$sf_table_name       = $wpdb->prefix . "survey_form_data_count";

		// Remove any option from survey form.
		$wpdb->query( $wpdb->prepare( "DELETE FROM $sf_table_name WHERE form_option_name = %s", array( $closest_option_name ) ) );
		wp_die();

	}

	/**
	 * sf_reset_option_count function for reset option count.
	 *
	 */
	function sf_reset_option_count() {

		global $wpdb;

		$option_id     = filter_input( INPUT_POST, 'option_id', FILTER_SANITIZE_STRING );
		$sf_table_name = $wpdb->prefix . "survey_form_data_count";

		// Reset option count of particular option click.
		if ( ! empty( $option_id ) && ! empty( $option_id ) ) {
			$wpdb->query( $wpdb->prepare( "update $sf_table_name SET form_option_count = %d WHERE form_option_name = %s ", array( "0", $option_id ) ) );
		}

		wp_die();
	}

}
