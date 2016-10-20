<?php

require_once('assets/inc/TwitterAPIExchange.php');
require_once('model/select_api_key.php');

/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
$settings = array(
  'oauth_access_token' => $select[0]->api_config_acces_token,
  'oauth_access_token_secret' => $select[0]->api_config_acces_token_secret,
  'consumer_key' => $select[0]->api_config_consumer_key,
  'consumer_secret' => $select[0]->api_config_consumer_secret
);

//*******************************************************************************
// call twitter api
//*******************************************************************************
$url = 'https://api.twitter.com/1.1/statuses/mentions_timeline.json';
$getfield = '?count=100';
$requestMethod = 'GET';

$twitter = new TwitterAPIExchange($settings);
$response = $twitter->setGetfield($getfield)
  ->buildOauth($url, $requestMethod)
  ->performRequest();

$result = json_decode($response);
//echo "<pre>";print_r($result);echo "</pre>";


//**********************************************************************
// GET NBR OF RETWEET, NBR OF LIKES, AND INFO PER TWEET
//**********************************************************************
//echo "<pre>";print_r($result);echo "</pre>";
$count_result = count($result);

$retweet_count = 0;
$favorite_count = 0;

for ($i=0; $i <= $count_result - 1 ; $i++) 
{ 
  $post_date = explode(' ', $result[$i]->created_at);
  $post_date = $post_date[2].'-'.$post_date[1].'-'.$post_date[5].' '.$post_date[3];
  //echo "<pre>";print_r($post_date);echo "</pre>";
  //echo strtotime("- 1 day"). "<br/><br/>";

  $post_date = date("Y-m-d G:i:s", strtotime($post_date)); 
  //echo "Today: ".$post_date;

  $test = strtotime($post_date);
  //echo "====>".$test."<======<br/><br/>";

  if( strtotime($post_date) > strtotime("-1 day") )
  {
    //*******************************************************
    // info per mention
    //*******************************************************
    $mention_infos[$i][0] =  $result[$i]->id;
    $mention_infos[$i][1] =  $result[$i]->text;
    $mention_infos[$i][2] =  $post_date;
    $mention_infos[$i][3] =  $result[$i]->user->screen_name;
 
    
  }
  else
  {
    break;
  }

}
  
//echo "<pre>";print_r($mention_infos);echo "</pre>";

$count_mention_infos = count($mention_infos);

for ($i=0; $i <= $count_mention_infos -1 ; $i++) {

  $wp_twitter_mention_id = $mention_infos[$i][0];
  $wp_twitter_mention_content = $mention_infos[$i][1];
  $wp_twitter_mention_date = $mention_infos[$i][2];
  $wp_twitter_remention_count = $mention_infos[$i][3];
  $wp_twitter_favorite_count = $mention_infos[$i][4];
  $wp_twitter_screen_name = $mention_infos[$i][5];

  $table_name = $wpdb->prefix . 'twitter_mentions';

    $insert_dayly_mention = " INSERT INTO $table_name (   `wp_twitter_mention_id`, 
                                          `wp_twitter_mention_content`, 
                                          `wp_twitter_mention_date`, 
                                          `wp_twitter_screen_name`) 
                            VALUES (  '$wp_twitter_mention_id', 
                                      '$wp_twitter_mention_content', 
                                      '$wp_twitter_mention_date', 
                                      '$wp_twitter_screen_name') ";
    //echo $insert_dayly_mention;
    $result = $wpdb->query( $insert_dayly_mention );

    if ( ( $result === FALSE ) ) {
      echo  __( "Oups ! Un problème a été rencontré." );
    } 

}