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
$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
$getfield = '?count=200';
$requestMethod = 'GET';

$twitter = new TwitterAPIExchange($settings);
$response = $twitter->setGetfield($getfield)
  ->buildOauth($url, $requestMethod)
  ->performRequest();

$result = json_decode($response);


//**********************************************************************
// GET ACCOUNT NAME, NBR OF FOLLOWERS, NBR OF FRIENDS, NBR OF LIST
//**********************************************************************
$account_name = $result[0]->user->screen_name;
//echo $account_name;

$followers_count = $result[0]->user->followers_count;
//echo $followers_count;

$friends_count = $result[0]->user->friends_count;
//echo $friends_count;

$listed_count = $result[0]->user->listed_count;
//echo $listed_count;


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

  $post_date = date("d-m-Y G:i:s", strtotime($post_date)); 

  $yesterday = date("d-m-Y G:i:s", mktime(date("G"), date("i"), date("s") , date("m"), date("d")-1, date("Y")));
  //echo "<br/>".$yesterday;

  if(strtotime($post_date) >= strtotime($yesterday))
  {
    //*******************************************************
    // COUNT of rt and likes
    //*******************************************************
    $retweet_count += (int)$result[$i]->retweet_count;
    $favorite_count += (int)$result[$i]->favorite_count;

  }
  else
  {
    break;
  }
}

//*******************************************************
// nbr of rt and likes
//*******************************************************

$url = 'https://api.twitter.com/1.1/statuses/mentions_timeline.json';
$getfield = '?COUNT=100';
$requestMethod = 'GET';

$twitter = new TwitterAPIExchange($settings);
$response = $twitter->setGetfield($getfield)
  ->buildOauth($url, $requestMethod)
  ->performRequest();

$result = json_decode($response);
//echo "<pre>";print_r($result);echo "</pre>";

$count_result = count($result);

$mentions_count = 0;

for ($i=0; $i <= $count_result - 1 ; $i++) 
{ 
  $post_date = explode(' ', $result[$i]->created_at);
  $post_date = $post_date[2].'-'.$post_date[1].'-'.$post_date[5].' '.$post_date[3];
  //echo "<pre>";print_r($post_date);echo "</pre>";

  $post_date = date("d-m-Y G:i:s", strtotime($post_date)); 

  $yesterday = date("d-m-Y G:i:s", mktime(date("G"), date("i"), date("s") , date("m"), date("d")-1, date("Y")));
  //echo "<br/>".$yesterday;

  if(strtotime($post_date) >= strtotime($yesterday))
  {
    $mentions_count += 1;
  }
  else
  {
    break;
  }
}


$table_name = $wpdb->prefix . 'twitter_metrics';

$my_query = " INSERT INTO $table_name (   `twitter_metrics_account_name`, 
                                      `twitter_metrics_count_followers`, 
                                      `twitter_metrics_count_friends`, 
                                      `twitter_metrics_count_lists`, 
                                      `twitter_metrics_count_retweet`, 
                                      `twitter_metrics_count_mentions`, 
                                      `twitter_metrics_count_like`) 
          VALUES (  '$account_name', 
                    '$followers_count', 
                    '$friends_count', 
                    '$listed_count', 
                    '$retweet_count', 
                    '$favorite_count', 
                    '$mentions_count') ";
$result = $wpdb->query( $my_query );

if ( ( $result === FALSE ) ) {
  echo  __( "Oups ! Un problème a été rencontré." );
} 


