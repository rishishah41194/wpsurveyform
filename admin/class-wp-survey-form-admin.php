<?php

class wp_survey_form_admin {

	/**
	 * Initializes WordPress hooks
	 *
	 * Author: Rishi Shah
	 * Since: 1.0
	 */
	function __construct() {
		add_action( 'admin_menu', array( $this, 'sf_wp_survey_form_admin_side' ) );
		add_action( 'admin_print_styles', array( $this, 'sf_load_custom_wp_admin_style' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'sf_load_custom_wp_admin_script' ) );
		add_action( 'admin_post_submit-form', array( $this, 'sf_survey_form_add_record' ) );
		add_action( 'admin_post_nopriv_submit-form', array( $this, 'sf_survey_form_add_record' ) );
		add_action( 'wp_ajax_delete_form_data_action', array( $this, 'sf_delete_form_data_action' ) );
		add_action( 'wp_ajax_nopriv_delete_form_data_action', array( $this, 'sf_delete_form_data_action' ) );
		add_action( 'wp_ajax_sf_active_status_ajax_action', array( $this, 'sf_active_status_ajax_action' ) );
	}

	/**
	 * Added Pages in Menu for Settings
	 *
	 * Author: Rishi Shah
	 * Since: 1.0
	 */
	public function sf_wp_survey_form_admin_side() {
		add_menu_page( 'Add New Survey Form', 'Add New Survey Form', 'administrator', 'add_new_survey_form', array( $this, 'sf_add_new_survey_form' ), 'dashicons-forms' );
		add_submenu_page( 'add_new_survey_form', 'Display Survey Form', 'Display Survey Form', 'manage_options', 'display_survey_form', array( $this, 'sf_display_survey_form' ) );
	}

	public function sf_add_new_survey_form() {
		require_once( wp_survey_form_path . '/admin/HTML/AddNewSurveyForm.php' );
	}

	public function sf_display_survey_form() {
		require_once( wp_survey_form_path . '/admin/HTML/DisplaySurveyForm.php' );
	}

	public function sf_load_custom_wp_admin_style() {
		wp_enqueue_style( 'admin-css', plugins_url( '/CSS/admin-style.css', __FILE__ ) );
	}

	public function sf_load_custom_wp_admin_script() {
		wp_enqueue_script( 'my_custom_script', plugins_url( '/JS/admin-js.js', __FILE__ ) );
		wp_enqueue_script( 'ajaxHandle' );
		wp_localize_script( 'ajaxHandle', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin_ajax.php' ) ) );
	}

	public function sf_survey_form_add_record() {
		global $wpdb;

		$survey_name                  = filter_input( INPUT_POST, 'survey_name', FILTER_SANITIZE_STRING );
		$survey_question              = filter_input( INPUT_POST, 'survey_question', FILTER_SANITIZE_STRING );
		$question_option              = isset( $_POST['question_option'] ) ? $_POST['question_option'] : "";
		$survey_form_enable_disable   = filter_input( INPUT_POST, 'enable/disable', FILTER_SANITIZE_STRING );
		$id                           = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_STRING );
		$question_option_remove_blank = array_filter( $question_option );
		$question_option_string       = implode( ",", $question_option_remove_blank );

		if ( ! empty( $id ) ) {
			$wpdb->update( 'wp_survey_form_data', array( 'survey_form_name'           => trim( $survey_name ),
			                                             'survey_form_question'       => trim( $survey_question ),
			                                             'survey_form_option'         => trim( $question_option_string ),
			                                             'survey_form_enable_disable' => $survey_form_enable_disable,
			), array( 'id' => $id ) );
			wp_safe_redirect( "/wp-admin/admin.php?page=add_new_survey_form&id=$id" );
		} else {
			$wpdb->insert( 'wp_survey_form_data', array(
					'survey_form_name'           => trim( $survey_name ),
					'survey_form_question'       => trim( $survey_question ),
					'survey_form_option'         => trim( $question_option_string ),
					'survey_form_enable_disable' => trim( $survey_form_enable_disable ),
				) );
			$record_id = $wpdb->insert_id;
			wp_safe_redirect( "/wp-admin/admin.php?page=add_new_survey_form" );
		}
	}

	public function sf_delete_form_data_action() {
		global $wpdb;

		$id = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_STRING );

		if ( ! empty( $id ) ) {
			$wpdb->delete( 'wp_survey_form_data', array( 'id' => $id ) );
		}

		wp_die();
	}

	function sf_active_status_ajax_action() {

		global $wpdb;

		$sf_shortcode_id  = filter_input( INPUT_POST, 'sf_shortcode_id', FILTER_SANITIZE_STRING );
		$sf_active_status = filter_input( INPUT_POST, 'sf_active_status', FILTER_SANITIZE_STRING );

		if ( ! empty( $sf_shortcode_id ) && ! empty( $sf_active_status ) ) {
			$wpdb->update( 'wp_survey_form_data', array( "survey_form_enable_disable" => $sf_active_status ), array( 'id' => $sf_shortcode_id ) );
		}

		wp_die();

	}

}
