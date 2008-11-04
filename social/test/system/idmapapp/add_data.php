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
 * Adds some application-specific data to the session. It will be checked
 * by the corresponding get_data.php script.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */

require_once('ringside/api/clients/RingsideApiClients.php');
include_once('LocalSettings.php');

// If the test runner passing in a network_key, it will be evaluated by this class.
$ringside = new RingsideApiClients('idmapapp', 'idmapappsecret', $webUrl, $serverUrl );

$ringside->require_login();
$mapped_loggedin_user = "<ERROR>";
// The only mapping we're doing here is _from_ the network _to_ the principal; so we never pass a second argument.
if ( $_REQUEST['uids'] ) {
	$mapped_loggedin_user = $ringside->api_client->users_mapToPrincipal(explode(',', $_REQUEST['uids']));
} else {
	// Maps the logged_in_user subject to the principal 
	$mapped_loggedin_user = $ringside->api_client->users_mapToPrincipal();
}

// From Facebook developer's wiki: http://wiki.developers.facebook.com/index.php/Gotchas
// This is how you get $_SESSION to work with Facebook
if (isset($_POST["fb_sig_session_key"]))
{
  $_fb_sig_session_key = str_replace("-","0",$_POST["fb_sig_session_key"]);
  session_id($_fb_sig_session_key);
}
session_start();
error_log("Logged-in users is ".$ringside->get_loggedin_user());
error_log("Mapped logged-in user is ".$mapped_loggedin_user);
/*
 * This will set the test_uid data to the logged-in user's mapped identity. This should
 * ALWAYS be the same value.
 */
$_SESSION['test_uid'] = is_array($mapped_loggedin_user)?implode(',', $mapped_loggedin_user):$mapped_loggedin_user;
error_log("Session key is ".session_id());
echo 'Result{'.$_SESSION['test_uid'].'}';
?>