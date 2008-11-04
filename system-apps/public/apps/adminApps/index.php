<?php
require_once( 'ringside/web/config/RingsideWebConfig.php' );
#require_once( 'ringside/web/RingsideWebUtils.php' );
require_once( 'ringside/api/clients/RingsideApiClients.php');
#require_once( 'ringside/api/db/RingsideApiDbDatabase.php');
#require_once 'ringside/api/OpenFBAPIException.php';

$client = new RingsideApiClients( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey  );
$client->setLocalClient( true );
$client->require_login();
$uid = $client->get_loggedin_user();
$apps = $client->api_client->admin_getAppList();


?>

<ul>
  <?php foreach($apps as $app) { ?>  
    <li><?php print $app['name']; ?></li>
  <?php } ?>
</ul>
