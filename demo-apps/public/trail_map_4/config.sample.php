<?php

// use PHP include function at the top of all of the endpoints
// so this properconfiguration is loaded

// load the necessary PHP files
include_once 'ringside/api/clients/RingsideApiClients.php';
require_once('ringside/api/clients/RingsideRestClient.php');
include_once 'SuggestionUtils.php';

$GLOBALS['facebook_config']['debug']=0;

// use object oriented programming wherever practical
class Config {
	public static $api_key = '53d6fc7956e8c55c8f351ee6ffbb41f8';
	public static $secret  = 'f837a7ca9d8df385a31cbaf172fd345b';
	
	// Configure Database
	public static $db_ip = 'localhost';    
	public static $db_name = 'ringside';
	public static $db_user = 'root';
	public static $db_pass = '';
	
	public static $webUrl = "";
	public static $serverUrl = "";
}



# This is used for the client to give back appropriate URLS.
Config::$webUrl = "http://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}/web";

# he URL including rest endpoint for the REST SERVER to talk to. 
# if this is not a full install or packaged install you might need to change $webRoot to the location
# of the API server. 
Config::$serverUrl = "http://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}/api/restserver.php";

# set the static vars of the server addresses on every request
RingsideApiClientsConfig::$serverUrl = Config::$serverUrl;
RingsideApiClientsConfig::$webUrl = Config::$webUrl;


?>
