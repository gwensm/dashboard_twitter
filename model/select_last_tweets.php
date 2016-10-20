<?php
global $wpdb;

$table_name = $wpdb->prefix . 'twitter_metrics';

$query = "SELECT `wp_twitter_tweet_id`,`wp_twitter_tweet_content`,DATE_FORMAT(`wp_twitter_tweet_date`, '%d-%m-%Y') as tweet_date, DATE_FORMAT(`wp_twitter_tweet_date`, '%T') as tweet_hour,`wp_twitter_retweet_count`,`wp_twitter_favorite_count`,`wp_twitter_screen_name` FROM `wp_twitter_tweets` order by `wp_twitter_tweet_date` desc limit 5";


            
$select_last_tweet = $wpdb->get_results( $query );


