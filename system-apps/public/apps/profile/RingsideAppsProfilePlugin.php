<?php

/**
 * A plugin file loads an application into the system.  
 * Hence just copy this into the system and goto the Ringside configuration
 * page.  There you can configure and enable the application. 
 */

require_once( 'ringside/apps/RingsideAppsCommon.php' );
require_once( 'ringside/apps/RingsideAppsConfig.php' );

class RingsideAppsProfilePlugin {
   
   public $api_key = 'profile-api-key';
   public $secret_key = 'profile-secret-key';
   public $call_back = 'profile/index.php';
   public $name = 'profile';
   public $canvas_url = 'profile';
   public $sidenav_url = '';
   public $isdefault = 1;
      
   public $canvas_type = RingsideAppsCommon::CANVASTYPE_DSL;
   public $application_type = RingsideAppsCommon::APPTYPE_WEB;
   public $mobile = 0;
   public $deployed = 0;
   public $description = "Displays user information";
   public $default_fbml = null;
   public $tos_url = '';
   public $icon_url = '';
   public $postadd_url = '';
   public $postremove_url = '';
   public $privacy_url = '';
   public $ip_list = '';
   public $about_url = '';

   public function __construct() { 
      $this->support_email = RingsideAppsConfig::$supportEmail;
   }   
}

?>
