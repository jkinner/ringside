<?php
/**
 * Here are the configuration requirements: 
 * 
 * 1) Ringside API container, configured with RingsideApiConfig::$use_facebook_trust = true
 * 2) The fbtrustapp application deployed on its own URL (it is a Facebook application) on your HTTP server (Apache)
 * 3) The fbtrustapp application deployed on Ringside using the same canvas URL as your Facebook application
 * 4) The fbtrusapp configured with the SAME secret and API key as one of your Facebook applications
 *    We recommend that the application be configured exclusively for this test.
 * 5) The Facebook application must have a callback URL that points to your local fbtrustapp endpoint. For example,
 *    if your web server is running on localhost at port 8080, the callback URL should be something like:
 *    
 *    http://localhost:8080/fbtrustapp/
 *    
 *    Yes, put localhost right there in the URL. It will work. Trust us.
 */
$api_key = 'fa95babc325f6472133325efbc2dffa0';
$secret = '2b23922848d4280c635521ea16212d2f';

RingsideRestClient::$server_url = "http://{$_SERVER['HTTP_HOST'']}/api/restserver.php";
?>