<?php
    require_once( 'LocalSettings.php' );
    require_once( "ringside/social/session/RingsideSocialSession.php" );
	require_once( 'ringside/api/Session.php' );
    ini_set( 'session.use_cookies', '0' );
    ini_set('session.save_handler', 'user');
      	
    session_set_save_handler(array('Session', 'open'),
         array('Session', 'close'),
         array('Session', 'read'),
         array('Session', 'write'),
         array('Session', 'destroy'),
         array('Session', 'gc')
      );
	
    $network_session = new RingsideSocialSession($_REQUEST['social_session']);
        
?>

<html>
<h1>Social Session Dump</h1>
<label>User ID:</label><?php echo($network_session->getUserId()); ?><br/>
<label>Principal Id:</label><?php echo($network_session->getPrincipalId()); ?><br/>
<label>Trust:</label><?php echo($network_session->getTrust()); ?><br/>
<label>Expiry:</label><?php echo($network_session->getExpiry()); ?><br/>
<label>Network:</label><?php echo($network_session->getNetwork()); ?><br/>
<label>Session Key:</label><?php echo($network_session->getSessionKey()); ?><br/>
<label>Callback:</label><?php echo($network_session->getCallbackUrl()); ?><br/>
<label>Logged In?:</label><?php echo($network_session->isLoggedIn()); ?><br/>
<label>Keys:</label><?php echo($network_session); ?><br/>


</html>
    
