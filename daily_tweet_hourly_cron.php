<?php



require_once('assets/inc/TwitterAPIExchange.php');
  
/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
$settings = array(
  'oauth_access_token' => "788062235054772224-9eFCaoMLiHQKxceAbHNhglR2CiErzTf",
  'oauth_access_token_secret' => "b66YUQosOM5kmyRXlZQGovPT3bgfTCw80Yt0zNWY5oRH1",
  'consumer_key' => "t8wOYfM7rx6ELBLWLVn8SPLx4",
  'consumer_secret' => "MIMtHRAxAxzDJpATcLkUMMQej1r7QJqMgnEEPpBlUsMwwnp4UB"
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

  $post_date = date("d-m-Y G:i:s", strtotime($post_date)); 
  //echo "Today: ".$post_date;

  $yesterday = date("d-m-Y G:i:s", mktime(date("G"), date("i"), date("s") , date("m"), date("d")-1, date("Y")));
  //echo "<br/>Yesterday: ".$yesterday."<br/><br/>";

  $test = strtotime($post_date);
  //echo "====>".$test."<======<br/><br/>";

  if( strtotime($post_date) > strtotime("-1 day") )
  {
    //echo 'post date supp';
    //*******************************************************
    // info per tweet
    //*******************************************************
    $tweet_infos[$i][0] =  $result[$i]->id;
    $tweet_infos[$i][1] =  $result[$i]->text;
    $tweet_infos[$i][2] =  $post_date;
    $tweet_infos[$i][3] =  $result[$i]->retweet_count;
    $tweet_infos[$i][4] =  $result[$i]->favorite_count;
    $tweet_infos[$i][5] =  $result[$i]->user->screen_name;
    //echo "<pre>";print_r($tweet_infos);echo "</pre>";
    /*
    
    */
    
  }
  else
  {
    break;
  }

}
  
//echo "<pre>";print_r($tweet_infos);echo "</pre>";

$count_tweet_infos = count($tweet_infos);

for ($i=0; $i <= $count_tweet_infos -1 ; $i++) {

  $wp_twitter_tweet_id = $tweet_infos[$i][0];
  $wp_twitter_tweet_content = $tweet_infos[$i][1];
  $wp_twitter_tweet_date = $tweet_infos[$i][2];
  $wp_twitter_retweet_count = $tweet_infos[$i][3];
  $wp_twitter_favorite_count = $tweet_infos[$i][4];
  $wp_twitter_screen_name = $tweet_infos[$i][5];

  $table_name = $wpdb->prefix . 'twitter_tweets';

    $insert_dayly_tweet = " INSERT INTO $table_name (   `wp_twitter_tweet_id`, 
                                          `wp_twitter_tweet_content`, 
                                          `wp_twitter_tweet_date`, 
                                          `wp_twitter_retweet_count`, 
                                          `wp_twitter_favorite_count`, 
                                          `wp_twitter_screen_name`) 
                            VALUES (  '$wp_twitter_tweet_id', 
                                      '$wp_twitter_tweet_content', 
                                      '$wp_twitter_tweet_date', 
                                      '$wp_twitter_retweet_count', 
                                      '$wp_twitter_favorite_count', 
                                      '$wp_twitter_screen_name') ";
    //echo $insert_dayly_tweet;
    $result = $wpdb->query( $insert_dayly_tweet );

    if ( ( $result === FALSE ) ) {
      echo  __( "Oups ! Un problème a été rencontré." );
    } 
    else
    {
      echo "----> OKOKOK <-------";
    }
}