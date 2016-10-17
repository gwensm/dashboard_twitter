<?php
/*
Plugin Name: dashboard twitter
Plugin URI: https://github.com/gwensm/dashboard_twitter.git
Description:  A WordPress plugin to create a twitter dashboard
Version: 0.1
Author: Gwenaël Frelau
License: GPL
*/

$your_db_name = $wpdb->prefix . 'test';
 
// function to create the DB / Options / Defaults					
function your_plugin_options_install() 
{
   	global $wpdb;
  	global $your_db_name;
 
	// create the ECPT metabox database table
	if($wpdb->get_var("show tables like '$your_db_name'") != $your_db_name) 
	{
		$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}test (id INT AUTO_INCREMENT PRIMARY KEY, email VARCHAR(255) NOT NULL);";
 
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}

// run the install scripts upon plugin activation
register_activation_hook(__FILE__,'your_plugin_options_install');

