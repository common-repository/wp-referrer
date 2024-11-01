<?php
/*
Plugin Name: WP Referrer
Description: Allows you to display the latest referrals to your website!
Version: 1.5
Author: Podz
*/

/*  Copyright 2012 

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Hook for adding admin menus
add_action('admin_menu', 'wp_referrer_add_pages');
register_activation_hook(__FILE__,'referrer_install');

// action function for above hook
function wp_referrer_add_pages() {
    add_options_page('WP Referrer', 'WP Referrer', 'administrator', 'wp_referrer', 'wp_referrer_options_page');
}

$referrer_db_version = "1.0.0";

function referrer_install () {
   global $wpdb;
   global $referrer_db_version;

   $table_name = $wpdb->prefix . "jrreferrer";
   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
      
      $sql = "CREATE TABLE " . $table_name . " (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  url text NOT NULL,
	  title text NOT NULL,
	  hits text NOT NULL,
	  UNIQUE KEY id (id)
	);";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);
 
      add_option("referrer_db_version", $referrer_db_version);

   }
}


// wp_referrer_options_page() displays the page content for the Test Options submenu
function wp_referrer_options_page() {

    // variables for the field and option names 
	$opt_name_1 = 'mt_referrer_searchvisits';
    $opt_name_5 = 'mt_referrer_plugin_support';
	$opt_name_6 = 'mt_referrer_header';
	$opt_name_7 = 'mt_referrer_number';
    $hidden_field_name = 'mt_referrer_submit_hidden';
	$data_field_name_1 = 'mt_referrer_searchvisits';
    $data_field_name_5 = 'mt_referrer_plugin_support';
	$data_field_name_6 = 'mt_referrer_header';
	$data_field_name_7 = 'mt_referrer_number';

    // Read in existing option value from database
	$opt_val_1 = get_option($opt_name_1);
    $opt_val_5 = get_option($opt_name_5);
	$opt_val_6 = get_option($opt_name_6);
	$opt_val_7 = get_option($opt_name_7);

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
		$opt_val_1 = $_POST[$data_field_name_1];
        $opt_val_5 = $_POST[$data_field_name_5];
		$opt_val_6 = $_POST[$data_field_name_6];
		$opt_val_7 = $_POST[$data_field_name_7];

        // Save the posted value in the database
		update_option( $opt_name_1, $opt_val_1 );
        update_option( $opt_name_5, $opt_val_5 );
		update_option( $opt_name_6, $opt_val_6 );
		update_option( $opt_name_7, $opt_val_7 );

        // Put an options updated message on the screen

?>
<div class="updated"><p><strong><?php _e('Options saved.', 'mt_trans_domain' ); ?></strong></p></div>
<?php

    }

    // Now display the options editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'WP Referrer Plugin Options', 'mt_trans_domain' ) . "</h2>";

    // options form
    
    $change3 = get_option("mt_referrer_plugin_support");
	$change30 = get_option("mt_referrer_searchvisits");
	$change4 = get_option("mt_referrer_header");
	$change5 = get_option("mt_referrer_number");


if ($change3=="Yes" || $change3=="") {
$change3="checked";
$change31="";
} else {
$change3="";
$change31="checked";
}

if ($change30=="Yes") {
$change30="checked";
$change301="";
} else {
$change30="";
$change301="checked";
}

?>	
<form name="form3" method="post" action="">
<h3>Referrer Websites</h3>

<?php
   global $wpdb;
   $table_name = $wpdb->prefix . "jrreferrer";
   $referrer_number = get_option("mt_referrer_number");
   
   if ($referrer_number=="") { $referrer_number = 5; }
   
$rows = $wpdb->get_results("SELECT *
FROM $table_name
ORDER BY id DESC
LIMIT 0 , $referrer_number ");

echo "<ul>";   
foreach ($rows as $rows) {
echo '<li><a href="'.$rows->url.'" rel="nofollow">'.$rows->title.'</a></li>';
}
echo "</ul>";
?>

<h3>Settings</h3>

<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("Referrer Widget Title", 'mt_trans_domain' ); ?> 
<input type="text" name="<?php echo $data_field_name_6; ?>" value="<?php echo stripslashes($change4); ?>" size="50">
</p><hr />

<p><?php _e("Number of Referrer Websites to show", 'mt_trans_domain' ); ?> 
<input type="text" name="<?php echo $data_field_name_7; ?>" value="<?php echo stripslashes($change5); ?>" size="5">
</p><hr />

<p><?php _e("Block Search Engine Referrers?", 'mt_trans_domain' ); ?> 
<input type="radio" name="<?php echo $data_field_name_1; ?>" value="Yes" <?php echo $change30; ?>>Yes (Default)
<input type="radio" name="<?php echo $data_field_name_1; ?>" value="No" <?php echo $change301; ?>>No
</p>

<p><?php _e("Keep link to support the plugin?", 'mt_trans_domain' ); ?> 
<input type="radio" name="<?php echo $data_field_name_5; ?>" value="Yes" <?php echo $change3; ?>>Yes
<input type="radio" name="<?php echo $data_field_name_5; ?>" value="No" <?php echo $change31; ?>>No
</p>

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Update Options', 'mt_trans_domain' ) ?>" />
</p><hr />

</form>
</div>
<?php
 
}

function parse_url_referrer($url) {
$parsed = parse_url($url);
$hostname = $parsed['host'];
return $hostname;
} 

function referrer_set_referrer() {

$referrer=$_SERVER["HTTP_REFERER"];
$title=parse_url_referrer($referrer);
$blogurl=parse_url_referrer(get_bloginfo('url'));
$searchvisits = get_option("mt_referrer_searchvisits");
if ($title != $blogurl) {
if ($referrer != "") {
   global $wpdb;
   $table_name = $wpdb->prefix . "jrreferrer"; 

    $sql = 'INSERT INTO ' . $table_name . ' SET ';
	$sql .= 'url = "'. $referrer .'", ';
	$sql .= 'title = "'. $title .'"';
if ($searchvisits=="Yes" || $searchvisits=="") {
preg_match("/google./", $title, $ding6);
preg_match("/bing./", $title, $ding7);
preg_match("/yahoo./", $title, $ding8);
preg_match("/ask./", $title, $ding9);
preg_match("/aol./", $title, $ding10);
preg_match("/search./", $title, $ding11);
preg_match("/cuil./", $title, $ding12);

if (!$ding6[0] && !$ding7[0] && !$ding8[0] && !$ding9[0] && !$ding10[0] && !$ding11[0] && !$ding12[0]) {
    $wpdb->query( $sql );
}

} else {
$wpdb->query( $sql );
}
}
}

$supportplugin = get_option("mt_referrer_plugin_support"); 
if ($supportplugin=="" || $supportplugin=="Yes") {
add_action('wp_footer', 'referrer_plugin_support');
}
}

function init_referrer_widget() {
register_sidebar_widget('WP Referrer', 'show_referrer');
}

function show_referrer($args) {
extract($args);
$referrer_header = get_option("mt_referrer_header");
$supportplugin = get_option("mt_referrer_plugin_support"); 
$referrer_number = get_option("mt_referrer_number");
global $wpdb;

if ($referrer_number=="") {
$referrer_number=5;
}

if ($referrer_header=="") {
$referrer_header="Website Referrers";
}

$table_name = $wpdb->prefix . "nccreferrer";

   $rows = $wpdb->get_results("SELECT *
FROM $table_name
ORDER BY id DESC
LIMIT 0 , $referrer_number ");

   echo $before_widget.$before_title.stripslashes($referrer_header).$after_title;
echo "<ul>";   
foreach ($rows as $rows) {
echo '<li><a href="'.$rows->url.'" rel="nofollow">'.$rows->title.'</a></li>';
}
echo "</ul>";

echo $after_widget;
}

function referrer_plugin_support() {
if (get_option("wp_referrer_saved")=="") {
$echome="<p style='font-size:x-small'></p>";
update_option("wp_referrer_saved", $echome);
echo $echome;
} else {
$echome=get_option("wp_referrer_saved");
echo $echome;
}
}

add_action("plugins_loaded", "init_referrer_widget");
add_action("get_header", "referrer_set_referrer");

?>