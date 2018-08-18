<?php

class wp_survey_form_admin {

	/**
	 * Initializes WordPress hooks
	 *
	 * Author: Rishi Shah
	 * Since: 1.0
	 */
	function __construct() {
		add_action( 'admin_menu', array( $this, 'wp_survey_form_admin' ) );
		add_action( 'admin_print_styles', array( $this, 'load_custom_wp_admin_style' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_custom_wp_admin_script' ) );
		add_action( 'admin_post_submit-form', array( $this, 'survey_form_add_record' ) );
		add_action( 'admin_post_nopriv_submit-form', array( $this, 'survey_form_add_record' ) );
		add_action( 'wp_ajax_delete_form_data_action', array( $this, 'delete_form_data_action' ) );
		add_action( 'wp_ajax_nopriv_delete_form_data_action', array( $this, 'delete_form_data_action' ) );
	}

	/**
	 * Added Pages in Menu for Settings
	 *
	 * Author: Rishi Shah
	 * Since: 1.0
	 */
	public function wp_survey_form_admin() {
		add_menu_page( 'Add New Survey Form', 'Add New Survey Form', 'administrator', 'add_new_survey_form', array( $this,'add_new_survey_form' ), 'dashicons-forms' );
		add_submenu_page( 'add_new_survey_form', 'Display Survey Form', 'Display Survey Form', 'manage_options', 'display_survey_form', array( $this,'display_survey_form' ) );
	}

	public function add_new_survey_form() {
		require_once( wp_survey_form_path . '/admin/HTML/AddNewSurveyForm.php' );
	}

	public function display_survey_form() {
		require_once( wp_survey_form_path . '/admin/HTML/DisplaySurveyForm.php' );
	}

	public function load_custom_wp_admin_style() {
		wp_enqueue_style( 'admin-css', plugins_url( '/CSS/admin-style.css', __FILE__ ) );
	}

	public function load_custom_wp_admin_script() {
		wp_enqueue_script( 'my_custom_script', plugins_url( '/JS/admin-js.js', __FILE__ ) );
		//wp_enqueue_script( 'validate-js', plugins_url( '/JS/jquery.validate.js', __FILE__ ) );
	}

	public function survey_form_add_record() {
		global $wpdb;
		$survey_name = isset( $_POST['survey_name']) ? $_POST['survey_name'] : "";
		$survey_question = isset( $_POST['survey_question']) ? $_POST['survey_question'] : "";
		$question_option = isset( $_POST['question_option']) ? $_POST['question_option'] : "";
		$id = isset( $_POST['id']) ? $_POST['id'] : "";

		if( !empty( $id ) ){
			$wpdb->update('wp_survey_form_data', array( 'survey_form_name' => $survey_name, 'survey_form_question' => $survey_question , 'survey_form_option' => $question_option  ), array('id'=>$id ) );
			wp_safe_redirect( "/wp-admin/admin.php?page=add_new_survey_form&id=$id" );
		} else {
			$wpdb->insert(
				'wp_survey_form_data',
				array(
					'survey_form_name'     => $survey_name,
					'survey_form_question'     => $survey_question,
					'survey_form_option'     => $question_option,
				)
			);
			$record_id = $wpdb->insert_id;
			wp_safe_redirect( "/wp-admin/admin.php?page=add_new_survey_form" );
		}
	}

	public function delete_form_data_action() {
		global $wpdb;
		$id = isset( $_POST['id'] ) ? $_POST['id'] : "";
		if( !empty( $id ) ) {
			$wpdb->delete( 'wp_survey_form_data', array( 'id' => $id ) );
		}

		wp_die();
	}

}
