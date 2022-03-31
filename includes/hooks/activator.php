<?php

add_action('check_post_links', 'check_urls');

function register_urls_table() {
    global $wpdb;
    $wpdb->urls = "{$wpdb->prefix}urls";
}

function activateCron() {
    wp_schedule_event(strtotime('tomorrow midnight'), 'daily', 'check_post_links');
}

function deactivateCron() {
    wp_clear_scheduled_hook('check_post_links');
}


function dev_challenge_activate() {

    // Code for creating a table goes here
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    global $wpdb;
	global $charset_collate;

	// Call this manually as we may have missed the init hook
	register_urls_table();

	$sql_create_table = "CREATE TABLE {$wpdb->urls} (
	      url_id int(11) NOT NULL auto_increment,
	      url text NOT NULL,
	      status varchar(40) NOT NULL,
	      origin int(11)  NOT NULL,
	      PRIMARY KEY  (url_id)
	 ) $charset_collate; ";
 
	dbDelta($sql_create_table);


	activateCron();
}