<?php
/*
Plugin Name: WP Survey Form
Description: Just another registration form plugin. Simple but flexible.
Author: Rishi Shah
Author URI: https://github.com/rishishah41194
Text Domain: wp-survey-form
Version: 1.0
*/

//require_once WPCF7_PLUGIN_DIR . '/settings.php';

if ( ! defined( 'wp_register_form_path' ) ) {
	define( 'wp_survey_form_path', plugin_dir_path( __FILE__ ) );
}

register_activation_hook( __FILE__, 'my_plugin_create_db' );
function my_plugin_create_db() {

	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'survey_form_data';

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		survey_form_name varchar(255) NULL,
		survey_form_question varchar(255) NULL,
		survey_form_option varchar(255) NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

}

if ( is_admin() ) {
	require_once( wp_survey_form_path . '/admin/class-wp-survey-form-admin.php' );
	new wp_survey_form_admin();
}

require_once( wp_survey_form_path . '/public/class-wp-survey-form-public.php' );
new wp_survey_form_public();