<?php
### Configuration for api ###

# Database configuration setttings.
$db_type = 'mysql';
$db_username = 'root';
$db_password = 'ringside';
$db_server = 'entourage:3306';
$db_name = 'ringfb_test';

//$facebook_config['debug'] = true;

$trust_facebook = false;

# Used for the API client to point to a REST server other than localhost.
# For a deployment within the server the defaults will point to local host, so no need to change them.
# Changes only required when you are using the client from somewhere else and want to poin to a server. 
#  
# $serverUrl = The URL including rest endpoint for the REST SERVER to talk to. 
$serverUrl = "http://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}/api/restserver.php";

#
# $webUrl = The Web page url to return to an application 
$webUrl = "http://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}/web";

### Configuration for social ###

# RingsideSocial needs a connection the API server, even though they might be local
# their must be a Key/Secret required by Social to talk to the API engine.   
# If the social layer gets deployed separately, also to make sure remote apps do not
# impersonate the RingsideSocial layer. 
# We suggest adding in some random text right 6 to 8 characters long, such as 'sjd2jds';
#
$socialApiKey = 'Jkinner';
$socialSecretKey = 'Jkinner';

$socialRoot = '/social';

$socialUrl = "http://{$_SERVER['SERVER_NAME']}:{$_SERVER['SERVER_PORT']}/social";

### Configuration for web ###

# Every WEB/ThirdParty connecting must maintain a NETWORK key indicating which WEB is connecting. 
# This is the default/internal web community packaged with Ringside
$networkKey = 'ringside-web';

# Web apps need to do redirects and other URL related activities, however it does not know how the 
# calling web front end is setup.  This setting tells it how to construct absolute urls. 
$webRoot = '/web';


### Configuration for web-apps ###

# Set the support email address to be used during installation. 
$supportEmail = 'admin@localhost';

### Configuration for demo-apps ###

### Configuration for API client ###
require_once('ringside/api/clients/RingsideApiClientsConfig.php');
RingsideApiClientsConfig::$serverUrl = $serverUrl;
RingsideApiClientsConfig::$webUrl = $webUrl;
RingsideApiClientsConfig::$socialUrl = $socialUrl;
?>
