<?php

$lsError = error_reporting( E_ERROR );
$ls1 = include '../../LocalSettings.php';
$ls2 = include 'LocalSettings.php';
error_reporting( $lsError );

if ( $ls1 === false && $ls2 === false  ) {
   echo "Could not locate LocalSettings.php";
}

// Configure canvas url. 
$canvas_url = "$webUrl/canvas.php";

error_log("Social context in fbfootprints is: ".var_export($_REQUEST, true));

// Get these from http://developers.facebook.com
$api_key = '4333592132647f39255bb066151a2099';
$secret  = 'b37428ff3f4320a7af98b4eb84a4aa99';
// Get these elsewhere
if ( isset($_REQUEST['fb_sig_nid']) && $_REQUEST['fb_sig_nid'] == 'example.com' ) {
    $api_key = '796aa6bc8d81d958847eb38e85761882';
    $secret = '5b6b65138a5e3865520a6748899325e1';
} else if ( isset($_REQUEST['fb_sig_nid']) && $_REQUEST['fb_sig_nid'] == 'foodnetwork.com' ) {
    $api_key = 'cc223c93558f876eab794e62f9711a36';
    $secret = '5b6b65138a5e3865520a6748899325e1';
}


/* While you're there, you'll also want to set up your callback url to the url
 * of the directory that contains Footprints' index.php, and you can set the
 * framed page URL to whatever you want.  You should also swap the references
 * in the code from http://apps.facebook.com/footprints/ to your framed page URL. */

// The IP address of your database
$db_ip = $db_server;           

$db_user = $db_username;
$db_pass = $db_password;

// the name of the database that you create for footprints.
$db_name = 'footprint';

/* create this table on the database:
CREATE TABLE `footprints` (
  `from` int(11) NOT null default '0',
  `to` int(11) NOT null default '0',
  `time` int(11) NOT null default '0',
  KEY `from` (`from`),
  KEY `to` (`to`)
)
*/
$GLOBALS['facebook_config']['debug']= false;