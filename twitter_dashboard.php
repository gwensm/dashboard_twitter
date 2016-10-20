<?php
/*
Plugin Name: Twitter Dashboard
Plugin URI: https://github.com/gwensm/dashboard_twitter.git
Description:  A WordPress plugin to create a twitter dashboard
Version: 0.1
Author: Gwenaël Frelau
License: GPL
*/

global $wpdb;

function twitter_dashboard_create_table() 
{
   global $wpdb;
   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

   //******************************************************
   // CREATE TABLE CONFIG
   //******************************************************
   $table_name = $wpdb->prefix . 'config_twitter_api';
   $sql = "CREATE TABLE IF NOT EXISTS $table_name (
          api_config_id bigint(20) UNSIGNED NOT NULL,
          api_config_acces_token varchar(60) NOT NULL,
          api_config_acces_token_secret varchar(60) NOT NULL,
          api_config_consumer_key varchar(60) DEFAULT NULL,
          api_config_consumer_secret varchar(60) DEFAULT NULL,
          PRIMARY KEY  (api_config_id)
   );";
   
   dbDelta( $sql );

   //******************************************************
   // CREATE TABLE ALL METRICS 
   //******************************************************
   $table_twitter_metrics = $wpdb->prefix . 'twitter_metrics';
   $sql1 = "CREATE TABLE IF NOT EXISTS $table_twitter_metrics (
          twitter_metrics_id bigint(20) unsigned NOT NULL auto_increment,
          twitter_metrics_account_name varchar(50) NOT NULL,
          twitter_metrics_count_followers int(11) DEFAULT NULL,
          twitter_metrics_count_friends int(11) NOT NULL,
          twitter_metrics_count_lists int(11) NOT NULL,
          twitter_metrics_count_retweet int(11) NOT NULL,
          twitter_metrics_count_mentions int(11) NOT NULL,
          twitter_metrics_count_like int(11) NOT NULL,
          date_query datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY  (twitter_metrics_id)
   );";
  
   dbDelta( $sql1 );

   //******************************************************
   // CREATE TABLE DAILY TWEETS 
   //******************************************************
   $table_twitter_tweets = $wpdb->prefix . 'twitter_tweets';
   $sql2 = "CREATE TABLE IF NOT EXISTS $table_twitter_tweets (
          `wp_twitter_id` bigint(20) NOT NULL auto_increment,
          `wp_twitter_tweet_id` varchar(50) NOT NULL,
          `wp_twitter_tweet_content` varchar(150) NOT NULL,
          `wp_twitter_tweet_date` varchar(20) NOT NULL,
          `wp_twitter_retweet_count` int(11) NOT NULL,
          `wp_twitter_favorite_count` int(11) NOT NULL,
          `wp_twitter_screen_name` varchar(50) NOT NULL,
          `date_insert` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY  (wp_twitter_id)
   );";
   
   dbDelta( $sql2 );

   //******************************************************
   // CREATE TABLE DAILY MENTIONS
   //******************************************************
   $table_twitter_mentions = $wpdb->prefix . 'twitter_mentions';
   $sql3 = "CREATE TABLE IF NOT EXISTS $table_twitter_mentions (
          `wp_mention_id` bigint(20) NOT NULL auto_increment,
          `wp_twitter_mention_id` varchar(50) NOT NULL,
          `wp_twitter_mention_content` varchar(150) NOT NULL,
          `wp_twitter_mention_date` varchar(20) NOT NULL,
          `wp_twitter_screen_name` varchar(50) NOT NULL,
          `date_insert` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY  (wp_mention_id)
   );";

   dbDelta( $sql3 );
}

// run the install scripts upon plugin activation
register_activation_hook(__FILE__,'twitter_dashboard_create_table');

wp_register_style( 'my_css.css', plugin_dir_url( __FILE__ ) . 'assets/css/my_css.css');
wp_enqueue_style( 'my_css.css');

require_once( 'config_api_twitter.php' );

//************************************************************************
//CHANGE HEADER
//************************************************************************
function change_admin_header() 
{?>
  <div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
  
        <div class="aa_button_config">
          <a id="button_config" href="admin.php?page=twitter_dashboard_submenu"> Config Twitter Dashboard</a>
          <span>Please config Twitter Dashboard with your Twitter api id </span>
        </div>
</div>
<?php
}


$table_name = $wpdb->prefix . 'config_twitter_api';
  $query = "SELECT api_config_id, api_config_acces_token, api_config_acces_token_secret, api_config_consumer_key, api_config_consumer_secret FROM  $table_name";
  $select = $wpdb->query( $query );

  if (  ($select === 0) ) {

add_action( 'admin_notices', 'change_admin_header' );
}


//****************************************************************************
//  PLUGIN MENU
//****************************************************************************

add_action( "admin_menu", "twitter_dashbord_add_dashboard_menu" );

function twitter_dashbord_add_dashboard_menu() {
   
  //************************
  // main menu page
  //************************

   add_menu_page( 
      __( "Twitter Dashboard"), // texte de la balise <title>
      __( "Twitter Dashboard"),  // titre de l'option de menu
      "administrator", // droits requis pour voir l'option de menu
      "twitter_dashboard_menu", // slug
      "twitter_dashbord_create_main_page",
      "../wp-content/plugins/dashboard_twitter/assets/img/twitter-logo.png" // fonction de rappel pour créer la page
   );

   //*************************
   // submenu page
   //*************************

   add_submenu_page( 
   "twitter_dashboard_menu",  // slug du menu parent
   __( "Configuration of Twitter Dashboard"),  // texte de la balise <title>
   __( "Configuration"),  // titre de l'option de sous-menu
   "manage_options",  // droits requis pour voir l'option de menu
   "twitter_dashboard_submenu", // slug
   "sub_menu_config_page"  // fonction de rappel pour créer la page
);
}

//****************************************************************************
// content of main page
//****************************************************************************

function twitter_dashbord_create_main_page() {
   global $title;   // titre de la page du menu, tel que spécifié dans la fonction add_menu_page
   ?>
   <div class="wrap">
      <h2><?php echo $title; ?></h2>   
      main page
   </div>
   <?php
}




//****************************************************************************
// content of submenu page
//****************************************************************************

function sub_menu_config_page() {
  global $title;   // page title
  

  //********************************************************
  // API KEY SELECT
  //********************************************************
  global $wpdb;

  $table_name = $wpdb->prefix . 'config_twitter_api';

  $query = "SELECT  api_config_id, api_config_acces_token, 
                    api_config_acces_token_secret, 
                    api_config_consumer_key, 
                    api_config_consumer_secret 
            FROM  $table_name";


            
  $select = $wpdb->get_results( $query );

  //*****************************************************************************************
  // VIEW CONFIG API KEY
  //*****************************************************************************************

  if (  ($select[0]->api_config_id === '0') && ($select[0]->api_config_acces_token !== '') ) {
  ?> 
      <div class="wrap">
        <h2><?php echo $title; ?></h2>   
        <div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
          <h3>Change your Twitter API information</h3>
          <form id="formulaire_ctc" name ="formulaire" action="" method="post">
            
            <div>
              <div class="div_block">
                  <div class="input_name">Access Token</div>
                  <input id="access_token" class="form_style" name="access_token" placeholder="Access token" required  type="text" value="<?php echo $select[0]->api_config_acces_token ?>" /> 
              </div>
                
              <div class="div_block">
                  <div class="input_name">Access token secret</div>
                  <input id="access_token_secret" class="form_style" name="access_token_secret" placeholder="Access token secret" required  type="text" value="<?php echo $select[0]->api_config_acces_token_secret ?>"/> 
              </div>
                
              <div class="div_block">
                  <div class="input_name">Consumer key</div>
                  <input id="consumer_key" class="form_style" name="consumer_key" placeholder="Consumer key" required  type="text" value="<?php echo $select[0]->api_config_consumer_key ?>"/>
              </div>
                
              <div class="div_block">
                  <div class="input_name">Consumer secret</div>
                  <input id="consumer_secret" class="form_style" name="consumer_secret" placeholder="Consumer secret" required  type="text" value="<?php echo $select[0]->api_config_consumer_secret ?>"/> 
              </div>

              <div class="div_block">
                  <input id="btn-send" class="btn-stand" type="submit" value="Change my information" />
              </div>
            </div>   
                    
          </form>
        </div>
      </div>
  <?php 

  } else {
     ?>
   <div class="wrap">
      <h2><?php echo $title; ?></h2>   
      <div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
      <h3>Config your Twitter API information</h3>
          <form id="formulaire_ctc" name ="formulaire" action="" method="post">
            
                <div>
                  <div class="div_block">
                      <input id="access_token" class="form_style" name="access_token" placeholder="Access token" required  type="text""/>
                    </div>
                    
                    <div class="div_block">
                      <input id="access_token_secret" class="form_style" name="access_token_secret" placeholder="Access token secret" required  type="text""/>
                    </div>
                    
                    <div class="div_block">
                      <input id="consumer_key" class="form_style" name="consumer_key" placeholder="Consumer key" required  type="text""/>
                    </div>
                    
                    <div class="div_block">
                      <input id="consumer_secret" class="form_style" name="consumer_secret" placeholder="Consumer secret" required  type="text""/>
                    </div>

                    <div class="div_block">
                      <input id="btn-send" class="btn-stand" type="submit" value="Envoyer" />
                  </div>
                </div>   
                    
            </form>
      </div>
   </div>
   <?php
  }
    
}

  
  //********************************************************
  // API KEY SELECT
  //********************************************************
  global $wpdb;

  $table_name = $wpdb->prefix . 'config_twitter_api';

  $query = "SELECT  api_config_id, api_config_acces_token, 
                    api_config_acces_token_secret, 
                    api_config_consumer_key, 
                    api_config_consumer_secret 
            FROM  $table_name";


            
  $select_api_key = $wpdb->get_results( $query );


if (  ($select_api_key[0]->api_config_id === '0') && ($select_api_key[0]->api_config_acces_token !== '') ) {

  //********************************************************************
  // CRON ALL METRICS
  //********************************************************************

  $today = date('d-m-Y');
    
  $table_name = $wpdb->prefix . 'twitter_metrics';

  $query = "  SELECT DATE_FORMAT(date_query, '%d-%m-%Y') as date_query 
              FROM $table_name 
              order by twitter_metrics_id desc 
              limit 1 ";

  $select_twitter_metrics = $wpdb->get_results( $query );
  $select_twitter_metrics2 = $wpdb->query( $query );

  $date_comp = $select_twitter_metrics[0]->date_query;

    if( ($select_twitter_metrics[0] == "" && $select_twitter_metrics2 !== false) || ($date_comp != $today && $select_twitter_metrics2 !== false )){
      require_once('all-metrics_daily_cron.php');
    }


  //************************************************************************
  // CRON DAILY TWEET
  //************************************************************************
  $today_long = date('d-m-Y G:i:s');

  $table_name = $wpdb->prefix . 'twitter_tweets';

  $select_tweet_date = "  SELECT `date_insert`  
              FROM `wp_twitter_tweets` 
              order by `wp_twitter_id` 
              desc limit 1";

  $select_date_tweet = $wpdb->get_results( $select_tweet_date );
  $select_date_ok = $wpdb->query( $select_tweet_date );

  $date_insert = $select_date_tweet[0]->date_insert;

  if( ( $select_tweet_date[0] == "" && $select_date_ok !== false) || ( strtotime($date_insert) < strtotime('-2 hours') )){
    require_once('daily_tweet_hourly_cron.php');
  }


  $table_name = $wpdb->prefix . 'twitter_mentions';

  $select_mention_date = "  SELECT `date_insert`  
              FROM $table_name 
              order by `wp_mention_id` 
              desc limit 1";

  $select_date_mention = $wpdb->get_results( $select_mention_date );
  $select_date_ok = $wpdb->query( $select_mention_date );

  $date_insert = $select_date_mention[0]->date_insert;

  if( ( $select_mention_date[0] == "" && $select_date_ok !== false) || ( strtotime($date_insert) < strtotime('-2 hours') )){
    require_once('daily_mention_hourly_cron.php');
  }
}

