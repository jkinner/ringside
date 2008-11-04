<?php
 /*
  * Ringside Networks, Harnessing the power of social networks.
  *
  * Copyright 2008 Ringside Networks, Inc., and individual contributors as indicated
  * by the @authors tag or express copyright attribution
  * statements applied by the authors.  All third-party contributions are
  * distributed under license by Ringside Networks, Inc.
  *
  * This is free software; you can redistribute it and/or modify it
  * under the terms of the GNU Lesser General Public License as
  * published by the Free Software Foundation; either version 2.1 of
  * the License, or (at your option) any later version.
  *
  * This software is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
  * Lesser General Public License for more details.
  *
  * You should have received a copy of the GNU Lesser General Public
  * License along with this software; if not, write to the Free
  * Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA
  * 02110-1301 USA, or see the FSF site: http://www.fsf.org.
  */

/**
 * Implements the UI for performing identity mapping.
 *
 * Parameters expected to be passed in 
 * 
 * method = bindmap  (represents action of mapping)
 * next = 'URL to be followed through once mapping is complete'
 * sid = "100000" - User id of the user on the network they called from.  
 * snid = 'Social Network ID' - The ID of the network which user came through 
 * api_key = "APIKEY of application requesting identity mapping"
 * sig = "" 
 * social_session_key = "Represents session between network and user. " 
 * session_key = "41f52ee85f4709fe2c743c85a8931974-ringside" 
 * canvas = "true|false" - is this a canvas application
 * 
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */

require_once('ringside/social/RingsideSocialUtils.php');
require_once('ringside/social/config/RingsideSocialConfig.php');
require_once('ringside/social/session/RingsideSocialSession.php');

// TODO: If there is no api_session_key, redirect somewhere? 
$next = isset( $_REQUEST['next'] ) ? $_REQUEST['next'] : null;
$sid =  isset( $_REQUEST['sid'] ) ? $_REQUEST['sid'] : null;
$snid =  isset( $_REQUEST['snid'] ) ? $_REQUEST['snid'] : null;
$api_key =  isset( $_REQUEST['api_key'] ) ? $_REQUEST['api_key'] : null;
$canvas = isset( $_REQUEST['canvas'] ) ? true : false;
$network = isset( $_REQUEST['network'] ) ? true : false;
$social_session_key = isset( $_REQUEST['social_session_key'] ) ? $_REQUEST['social_session_key'] : null;
$sig = '';


$network_session = null;
$authorities = null;

try {

   // We are expecting a social session key in the request, this can help us understand the current map request
   // the network it is coming from and more.
   // In the map process not sure where we need this, but its good to load it here.
   $network_session = new RingsideSocialSession( $social_session_key );

   // The mapping process is happening relative to some NETWORK, the user might not be logged in to the DEPLOYED NETWORK.
   // And we should not care.  However we have to ask some system questions.
   $ringside_rest = RingsideSocialUtils::getAdminClient( $snid );
   $authorities = $ringside_rest->admin_getTrustInfo();
} catch ( Exception $e ) {

   include "ringside/templates/error.tpl";
   return;
    
}


$this_authority = null;

foreach (  $authorities as $authority ) {
	if ( $authority['trust_key'] == $snid ) {
		$this_authority = $authority;
		break;
	}
}

$hiddenInputs = <<<heredoc
   <input type="hidden" name="method" value="bindmap" />
   <input type="hidden" name="next" value="$next" />
   <input type="hidden" name="sid" value="$sid" />
   <input type="hidden" name="snid" value="$snid" />
   <input type="hidden" name="api_key" value="$api_key" />
   <input type="hidden" name="sig" value="$sig" />
heredoc;
if ( $network === true ) { 
   $hiddenInputs .= '<input type="hidden" name="network" value="true">';
} else  if ( $canvas === true ) { 
   $hiddenInputs .= '<input type="hidden" name="canvas" value="true">';
}

$newProfileUrl = 'trust.php?';
$newProfileUrl .= 'method=newprofile';
$newProfileUrl .= '&snid=' . $snid;
$newProfileUrl .= '&sid=' . $sid;
$newProfileUrl .= '&next=' . $next;
$newProfileUrl .= '&api_key=' . $api_key;
if ( $network === true ) { 
   $newProfileUrl .= '&network';
} else  if ( $canvas === true ) { 
   $newProfileUrl .= '&canvas';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Running Log Sign In</title>
<link rel="stylesheet" href="http://runningco.ringsidenetworks.com/css.php/runningco.css" type="text/css" />
<style>
body {
	background:#e0ded2;
}
</style>
</head>
<body>

<div id="signup">
	<div id="login-steps-network">&nbsp;</div>
    <div id="login-content">
    	<p>If you already use Voomaxer on Facebook or Voomaxer.com, you can use that data here at RunningCo.  If you don't have an existing log, and want to start a new one here at RunningCo, you can do that too - just select the site from the drop down below:</p>
   		<div id="form-holder">
            	<div id="label-choose-site">Choose the site you want to use:</div>
                 <form id="login_form" action="trust.php" method="post">
<?php echo $hiddenInputs ?>

                   <select name="nid">
                     <option value="<?php echo $this_authority['trust_key'] ?>">Create a log on <?php echo $this_authority['trust_name'] ?></option>
<?php
foreach ( $authorities as $authority ) {
	$trust_key = $authority['trust_key'];
	$trust_name = $authority['trust_name'];
	
	if ( $trust_key != $snid ) {
?>
                     <option value="<?php echo $trust_key ?>">Use an existing log on <?php echo $trust_name ?></option>
<?php
	}
}
?>
                  </select>
                  <br>
                  <a href="javascript:document.forms['login_form'].submit();"><img id="btn-login-next" src="http://runningco.ringsidenetworks.com/images/btn-login-next.gif" /></a>
			       </form>
			    </div><!-- end div#label-choose-site -->
	    </div><!-- end div#form-holder -->
	    <div id="signup-message">
	    	<p>To allow Voomaxer to share your data, you’ll need to login whatever site you choose. You will be directed back here to finish your registration after you login. Hurry back!</p>
	    </div><!-- end div#login-message -->
    </div><!-- end div#login-content -->
<!-- /div> end div#loginform -->


</body>
</html>