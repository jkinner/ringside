<?php
/*******************************************************************************
 * Ringside Networks, Harnessing the power of social networks.
 * 
 * Copyright 2008 Ringside Networks, Inc., and individual contributors as indicated
 * by the @authors tag or express copyright attribution
 * statements applied by the authors.  All third-party contributions are
 * distributed under license by Ringside Networks, Inc.
 * 
 * This is free software; you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation; either version 2.1 of
 * the License, or (at your option) any later version.
 * 
 * This software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this software; if not, write to the Free
 * Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA
 * 02110-1301 USA, or see the FSF site: http://www.fsf.org.
 ******************************************************************************/

/**
 * Flavor = canvas: Enable someone to add an application.
 * Flavor = sidebar: Show Application List
 *
 * We expect
 * api_key : in the request
 * next : is the next page you would like to be redirected to.
 *
 * The users is in session
 * The user will be
 * 1. if no api_key then erred
 * 2. if bad api_key then punted
 * 3. asked a few questions, then added to db.
 */

require_once( 'ringside/web/config/RingsideWebConfig.php' );
require_once( 'ringside/web/RingsideWebUtils.php' );
require_once( 'ringside/api/clients/RingsideApiClients.php');
require_once( 'ringside/api/db/RingsideApiDbDatabase.php');
require_once 'ringside/api/OpenFBAPIException.php';

$client = new RingsideApiClients( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey  );
$client->setLocalClient( true );
$client->require_login();
$uid = $client->get_loggedin_user();
$flavor = $client->getFlavor();

if($flavor == 'sidebar')
{
   $apps = $client->api_client->users_getAppList();
   //u.app_id, u.user_id, u.enabled, a.name, a.canvas_url, a.sidenav_url, a.api_key,
   //a.callback_url, ap.canvas_type, ap.application_type, ap.description, ap.icon_url, ap.postadd_url, ap.postremove_url, ap.about_url
   if( !empty( $apps ) && count($apps) > 0) {
      ?>
<div class="content">
<h2>Applications</h2>
<div><a href="<?php echo RingsideWebConfig::$webRoot.'/canvas.php/apps/editapps.php' ?>">Edit</a></div>
<ul>
<?php
foreach($apps as $app)
{
   $sidebar = RingsideWebConfig::$webRoot.'/canvas.php/'.$app['canvas_url'];
   $imgIcon = '';
   if ( isset($app['icon_url']) && !empty($app['icon_url']) ){
      $imgIcon = '<img src="'.$app['icon_url'].'"/>';
   }

   echo '<li><a href="'.$sidebar.'">' . $imgIcon .$app['name'] .'</a></li>';
}
?>
</ul>
</div>
<?php
} else {
   ?>
<div class="app_menu">
<div><a href="<?php echo RingsideWebConfig::$webRoot.'/canvas.php/apps/browse.php' ?>">Browse Applications</a></div>
</div>
   <?php
}

} else {
   /*
    * If this is originating from a post request and the app_id
    * is set, then we are processing the request to add.
    */
   if ( isset( $_POST['app_id']) ) {
      // Process the form submit. This needs to become an API call.
      // allows_status_update, allows_create_listing,allows_photo_upload,auth_information,auth_profile,auth_leftnav,auth_newsfeeds

      $app_id = $_POST['app_id'];

      error_log("Attempting to add app: ".$app_id);
      //users_setApp($params, $app_id)
      try{
         $client->api_client->users_setApp($_POST, $app_id);

         $result = $client->api_client->admin_getAppProperties( "canvas_url" , $app_id );
         $canvas = isset( $result['canvas_url'] )? $result['canvas_url'] : '';

         // TODO move this pretty much to a utility function for keeping parameters.
         //      $next = isset( $_GET['next'] ) ? $_GET['next'] : "canvas/$name" ;

         // Which page to go to?  Default index.php
         RingsideWebUtils::redirect(RingsideWebConfig::$webRoot."/canvas.php/$canvas");
      } catch(Exception $exception) {
         error_log("Caught Exception Saving User App Settings: ".$exception->getMessage());
         error_log($exception->getTraceAsString());
         $error = $exception->getMessage();
         include 'add_empty.php';
      }
   } else if ( isset($_GET['app_id']) ) {
      	
      $app_id = $_GET['app_id'];

      //application_getPublicInfo( $application_id = null, $application_canvas_name = null, $application_api_key = null)
      try{
         // If the app doesn't exist this method will throw an exception
         $client->api_client->application_getPublicInfo($app_id, null, null);
         $status = $client->api_client->users_isAppEnabled( $uid, $app_id );
         include 'add_form.php';
      }catch(Exception $e)
      {
         if(FB_ERROR_CODE_NO_APP == $e->getCode()) {
            echo "<fb:error>
            <fb:message>Application Does Not Exist</fb:message>
            Application with Application ID $app_id does not exist on this Ringside instance!
            </fb:error>";
         } else {
            echo "<fb:error>
            <fb:message>Application Get Public Info Error</fb:message>
            Application with Application ID $app_id threw the following error:".$e->getMessage()."</fb:error>";
         }
      }

   } else if ( isset( $_GET['api_key'] ) ) {

      $database = RingsideApiDbDatabase::getDatabaseConnection();

      $api_key = mysql_real_escape_string($_REQUEST['api_key']);
      $query="SELECT app.id FROM app WHERE app.api_key='$api_key'";
      $result=mysql_query($query, $database);
      	
      if ( mysql_errno($database) || !$result ) {
         $error = mysql_error($database);
         include 'add_empty.php';
      } else if ( mysql_num_rows($result) == 0 ) {
         $error = 'No such application is registered here. ';
         include 'add_empty.php';
      } else {
         $row = mysql_fetch_assoc($result);
         $app_id = $row['id'];

         $result = $client->api_client->users_isAppAdded( $uid, $app_id );
         if ( $result == 1 ) {

            $result = $client->api_client->admin_getAppProperties( "canvas_url" , $app_id );
            $canvas = isset( $result['canvas_url'] )? $result['canvas_url'] : '';
            $error = "You have this application registered already, <a href=\"canvas.php/$canvas/\">go there</a>!";
            include 'add_empty.php';
         } else {
            $status = $result;
            include 'add_form.php';
         }
      }

   } else  {
      $error = 'Your api key field is missing.';
      include 'add_empty.php';
	}
}

?>
