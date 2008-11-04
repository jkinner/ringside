<?php
### Configuration for api ###
echo "USING TESTSETTINGS.PHP\n";
require_once('ringside/api/clients/RingsideApiClientsConfig.php');

include dirname(__FILE__)."/../../../LocalSettings.php";
# Database configuration setttings.
$db_type = 'mysql';
$db_username = 'root';
$db_password = 'ringside';
$db_server = 'entourage:3306';
$db_name = 'ringfb_test';

$host = 'localhost';
$port = ':8888';

RingsideApiClientsConfig::$webUrl = "http://$host$port/web/";
RingsideApiClientsConfig::$socialUrl = "http://$host$port/social/";
RingsideApiClientsConfig::$serverUrl = "http://$host$port/api/restserver.php";

?>
