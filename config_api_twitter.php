<?php


if(isset($_POST['access_token'])){

	$table_name = $wpdb->prefix . 'config_twitter_api';
  	$query = "SELECT api_config_id, api_config_acces_token, api_config_acces_token_secret, api_config_consumer_key, api_config_consumer_secret FROM  $table_name";
  	$select = $wpdb->query( $query );

  	$access_token = $_POST['access_token'];
	$access_token_secret = $_POST['access_token_secret'];
	$consumer_key = $_POST['consumer_key'];
	$consumer_secret = $_POST['consumer_secret'];

	global $wpdb;
	$table_name = $wpdb->prefix . 'config_twitter_api';

  	if (  ( $select === 0 ) ) {

		
		$my_query = " INSERT INTO $table_name ( api_config_acces_token, api_config_acces_token_secret, api_config_consumer_key, api_config_consumer_secret) VALUES ('$access_token','$access_token_secret','$consumer_key','$consumer_secret' ) ";
		$result = $wpdb->query( $my_query );

		if ( ! ( $result === FALSE ) ) {
	   	
		} else {
		   echo  __( "Oups ! Un problème a été rencontré." );
		}
	}
	else{
		$my_query = " 	UPDATE 	$table_name 
						SET 	api_config_acces_token 			= '$access_token', 
								api_config_acces_token_secret 	= '$access_token_secret', 
								api_config_consumer_key 		= '$consumer_key', 
								api_config_consumer_secret 		= '$consumer_secret'
						WHERE 	api_config_id = '0'";
		$result = $wpdb->query( $my_query );

		if ( ! ( $result === FALSE ) ) {
	   	
		} else {
		   echo  __( "Oups ! Un problème a été rencontré." );
		}
	}
}

