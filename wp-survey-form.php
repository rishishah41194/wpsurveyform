<?php
/*
Plugin Name: WP Survey Form
Description: Just another survey form plugin. Simple but flexible.
Author: Rishi Shah
Author URI: http://rishirshah.com
Text Domain: wp-survey-form
Version: 1.0
*/

/**
 *
 * Define all the global constant.
 *
 */
if ( ! defined( 'sf_register_form_path' ) ) {
	define( 'sf_survey_form_path', plugin_dir_path( __FILE__ ) );
}

/**
 *
 * Plugin Activation hook for create DB tables.
 *
 */
register_activation_hook( __FILE__, 'sf_survey_form_create_db' );

/**
 *
 * Plugin Activation function for create DB tables.
 *
 */
function sf_survey_form_create_db() {

	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name_one  = $wpdb->prefix . 'survey_form_data';

	$sql_survey_form_data = "CREATE TABLE $table_name_one (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		survey_form_enable_disable varchar(255) NULL,
		survey_form_name varchar(255) NULL,
		survey_form_question varchar(255) NULL,
		survey_form_option varchar(255) NULL,
		UNIQUE KEY id (id),
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql_survey_form_data );

	$table_name = $wpdb->prefix . 'survey_form_data_count';;
	$sql_survey_form_data_count = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		survey_form_id mediumint(9) NOT NULL,
		form_option_id varchar(255) NULL,
		form_option_name varchar(255) NULL,
		form_option_count varchar(255) NULL,
		UNIQUE KEY id (id),
		PRIMARY KEY  (id),
		FOREIGN KEY (survey_form_id) REFERENCES $table_name_one(id)
	  ) $charset_collate;";
	  
	  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	  dbDelta( $sql_survey_form_data_count );

}

/**
 *
 * Create a new instant for admin class.
 */
if ( is_admin() ) {
	require_once( sf_survey_form_path . '/admin/class-wp-survey-form-admin.php' );
	new sf_survey_form_admin();
}

/**
 *
 * Create a new instant for public class.
 */
require_once( sf_survey_form_path . '/public/class-wp-survey-form-public.php' );
