<?php
### Configuration for api ###
$db_type = 'mysql';
//$db_username = 'root';
//$db_password = 'ringside';
$db_server = 'entourage:3306';
//$db_name = 'ringfb_test';

# Database configuration setttings.
$db_username = 'ringside_user';
$db_password = 'ringside';
//$db_server = 'entourage:3398';
$db_name = 'ringside';
//$db_name = 'ringside_ms';

### E-mail Server Information #################################################
# Defines connection settings for sending emails
###############################################################################
$smtp_server = 'ssl://smtp.gmail.com:465';
$smtp_username = 'jkinner@ringsidenetworks.com';
$smtp_password = 'et2bamp2';
$smtp_use_auth = true;
$smtp_default_sender = 'no-reply@ringsidenetworks.com';
$smtp_default_sender_name = 'Ringside Server';

$facebook_config['debug'] = false;

$trust_facebook = false;

# Used for the API client to point to a REST server other than localhost.
# For a deployment within the server the defaults will point to local host, so no need to change them.
# Changes only required when you are using the client from somewhere else and want to poin to a server. 
#  
# $serverUrl = The URL including rest endpoint for the REST SERVER to talk to. 
# $serverUrl = "http://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}/api/restserver.php";
$serverUrl = "http://localhost/api/restserver.php";

#
# $webUrl = The Web page url to return to an application 
$webUrl = "http://localhost/web";

### Configuration for social ###

# RingsideSocial needs a connection the API server, even though they might be local
# their must be a Key/Secret required by Social to talk to the API engine.   
# If the social layer gets deployed separately, also to make sure remote apps do not
# impersonate the RingsideSocial layer. 
# We suggest adding in some random text right 6 to 8 characters long, such as 'sjd2jds';
#
$socialApiKey = '5b6b65138a5e3865520a6748899325e1';
$socialSecretKey = '430aeb7e320f7a7bfd759cffa0e80975';

$socialRoot = '/social';

$socialUrl = "http://localhost/social";

### Configuration for web ###

# Every WEB/ThirdParty connecting must maintain a NETWORK key indicating which WEB is connecting. 
# This is the default/internal web community packaged with Ringside
$networkKey = '5b6b65138a5e3865520a6748899325e1';
$networkSecret = '430aeb7e320f7a7bfd759cffa0e80975';

# Web apps need to do redirects and other URL related activities, however it does not know how the 
# calling web front end is setup.  This setting tells it how to construct absolute urls. 
$webRoot = '/web';


### Configuration for web-apps ###

# Set the support email address to be used during installation. 
$supportEmail = 'admin@localhost';

### Configuration for demo-apps ###
$demoUrl = "http://localhost/demo";

### Configuration for API client ###
//require_once('ringside/api/clients/RingsideApiClientsConfig.php');
//RingsideApiClientsConfig::$serverUrl = $serverUrl;
//RingsideApiClientsConfig::$webUrl = $webUrl;
//RingsideApiClientsConfig::$socialUrl = $socialUrl;

$uploadRootDir = '/tmp';
$uploadRootUrl = '/tmp';

$m3DataDirectory='/tmp';

if ( include_once('ringside/api/clients/RingsideApiClientsConfig.php') )
{
    RingsideApiClientsConfig::$webUrl = $webUrl;
    RingsideApiClientsConfig::$socialUrl = $socialUrl;
    RingsideApiClientsConfig::$serverUrl = $serverUrl;
}

include_once(dirname(__FILE__).'/../socialpass/socialpass_config.php');
?>
