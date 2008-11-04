<?php

require_once("header.inc");

$apiKey = getRequestParam('api_key');

if ($apiKey == null) {
	RingsideWebUtils::redirect('index.php');
}

$props = array('application_name','secret_key', 'api_key');
$resp = $client->api_client->admin_getAppProperties($props, null, null, $apiKey);

$secret = $resp['secret_key'];
$appName = $resp['application_name'];

include('ringside/apps/developer/templates/new_app_success.tpl');

?>