<?php
include_once('ringside/api/clients/RingsideApiClients.php');
include_once('ringside/api/clients/RingsideRestClient.php');

// Control your configuration via this config file
include_once('config.php');

// Get the ringside client
RingsideApiClientsConfig::$webUrl = Config::$ringsideWeb;
RingsideApiClientsConfig::$serverUrl = Config::$ringsideApi;
$ringside = new RingsideApiClients( Config::$api_key, Config::$secret );

$uid = $ringside->require_login();
?>
