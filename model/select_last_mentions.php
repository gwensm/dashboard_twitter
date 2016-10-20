<?php
global $wpdb;

$table_name = $wpdb->prefix . 'twitter_metrics';

$query = "SELECT `wp_twitter_mention_id`,`wp_twitter_mention_content`,DATE_FORMAT(`wp_twitter_mention_date`, '%d-%m-%Y') as date_mention, DATE_FORMAT(`wp_twitter_mention_date`, '%T') as hour_mention, `wp_twitter_screen_name` FROM `wp_twitter_mentions` order by `wp_twitter_mention_date` desc limit 5";


            
$select_last_mention = $wpdb->get_results( $query );

