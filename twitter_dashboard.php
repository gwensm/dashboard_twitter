<?php
/*
Plugin Name: Twitter Dashboard
Plugin URI: https://github.com/gwensm/dashboard_twitter.git
Description:  A WordPress plugin to create a twitter dashboard
Version: 0.1
Author: Gwenaël Frelau
License: GPL
*/

function twitter_dashboard_create_table() 
{
   global $wpdb;
   $nom_table = $wpdb->prefix . 'config_twitter_api';
   $sql = "CREATE TABLE IF NOT EXISTS $nom_table (
      matable_id bigint(20) unsigned NOT NULL auto_increment,
      matable_description varchar(50) NOT NULL,
      matable_autrechamp int NULL,
      PRIMARY KEY  (matable_id)
   );";
   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );

   $table_twitter_metrics = $wpdb->prefix . 'twitter_metrics';
   $sql2 = "CREATE TABLE IF NOT EXISTS $table_twitter_metrics (
      twitter_metrics_id bigint(20) unsigned NOT NULL auto_increment,
      twitter_metrics_fan_number varchar(50) NOT NULL,
      twitter_metrics_other int NULL,
      PRIMARY KEY  (twitter_metrics_id)
   );";
   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql2 );
}

// run the install scripts upon plugin activation
register_activation_hook(__FILE__,'twitter_dashboard_create_table');

/*
function twitter_dashboard_drop_table() 
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'twitter_metrics';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
}

register_uninstall_hook(__FILE__, 'twitter_dashboard_drop_table');
*/


require_once( 'config_api_twitter.php' );



wp_register_style( 'my_css.css', plugin_dir_url( __FILE__ ) . 'assets/css/my_css.css');
			wp_enqueue_style( 'my_css.css');


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

/*
function change_admin_header() 
{?>
  <div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
  <form id="formulaire_ctc" name ="formulaire" action="" method="post">
            
                <div>
                  <div class="div_block">
                      <input id="admail" class="form_style" name="key" placeholder="Adresse e-mail" required  type="text""/>
                    </div>
                    
                    <div class="div_block">
                      <input id="admail" class="form_style" name="admail" placeholder="Adresse e-mail" required  type="text""/>
                    </div>
                    
                    <div class="div_block">
                      <input id="admail" class="form_style" name="admail" placeholder="Adresse e-mail" required  type="text""/>
                    </div>
                    
                    <div class="div_block">
                      <input id="admail" class="form_style" name="admail" placeholder="Adresse e-mail" required  type="text""/>
                    </div>

                    <div class="div_block">
                      <input id="btn-send" class="btn-stand" type="submit" value="Envoyer" />
                  </div>
                </div>   
                    
            </form>
</div>
<?php
}
*/

add_action( 'admin_notices', 'change_admin_header' );

//****************************************************************************
//	PLUGIN MENU
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
   ?>
   <div class="wrap">
      <h2><?php echo $title; ?></h2>   
      <div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
  <form id="formulaire_ctc" name ="formulaire" action="" method="post">
            
                <div>
                  <div class="div_block">
                      <input id="admail" class="form_style" name="key" placeholder="Adresse e-mail" required  type="text""/>
                    </div>
                    
                    <div class="div_block">
                      <input id="admail" class="form_style" name="admail" placeholder="Adresse e-mail" required  type="text""/>
                    </div>
                    
                    <div class="div_block">
                      <input id="admail" class="form_style" name="admail" placeholder="Adresse e-mail" required  type="text""/>
                    </div>
                    
                    <div class="div_block">
                      <input id="admail" class="form_style" name="admail" placeholder="Adresse e-mail" required  type="text""/>
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