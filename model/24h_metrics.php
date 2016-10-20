<?php
global $wpdb;

$table_name = $wpdb->prefix . 'twitter_metrics';

$query = "SELECT 	`twitter_metrics_account_name`,
					`twitter_metrics_count_followers`,
					`twitter_metrics_count_friends`,
					`twitter_metrics_count_lists`,
					`twitter_metrics_count_retweet`,
					`twitter_metrics_count_mentions`,
					`twitter_metrics_count_like` 
			FROM 	$table_name 
			ORDER BY `date_query` 
			DESC limit 1";


            
$select = $wpdb->get_results( $query );

//echo "<pre>";print_r($select);echo "</pre>";

