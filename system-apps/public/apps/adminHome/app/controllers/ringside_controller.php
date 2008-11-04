<?php

require_once("ringside/web/RingsideWebUtils.php");
require_once('ringside/social/client/RingsideSocialClientLocal.php');
require_once("ringside/web/config/RingsideWebConfig.php");
require_once("ringside/m3/client/RestClient.php");
//require_once('ringside/social/config/RingsideSocialConfig.php');

require_once( 'ringside/api/clients/RingsideApiClients.php');


// require_once("ringside/web/RingsideWebUtils.php");
// require_once("ringside/web/config/RingsideWebConfig.php");
// require_once("ringside/api/clients/RingsideApiClients.php");

abstract class Ringside_Controller extends Application_Controller
{
  public $client;
  public $m3;
  public $uid = "";
  
  public function __construct($action=null)
  {
    parent::__construct($action,$this);
    $this->createSession();
  }
  
  private function createSession()
  {
    //print RingsideSocialConfig::$apiKey . "<br />" . RingsideSocialConfig::$secretKey ."<br />";
    $this->client = new RingsideApiClients( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey  );
    $this->client->setLocalClient( true );
    
    // as long as current request is not partial, require a login
    //if(!$this->$partial)
    
    $this->client->require_login();
    $this->uid = $this->client->get_loggedin_user();
    
    $this->m3 = new M3_Client_RestClient(RingsideApiClientsConfig::$serverUrl, "r1ngs1d3");
  }


}


?>