<?php
$table_name = $wpdb->prefix . 'config_twitter_api';

  $query = "SELECT  api_config_id, api_config_acces_token, 
                    api_config_acces_token_secret, 
                    api_config_consumer_key, 
                    api_config_consumer_secret 
            FROM  $table_name";


            
  $select = $wpdb->get_results( $query );