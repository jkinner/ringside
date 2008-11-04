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
require_once('ringside/api/clients/RingsideApiClients.php');
include_once('LocalSettings.php');

/**
 * Reads application-specific data out of the session, making sure user mapping is working properly.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */

// If the test runner passing in a network_key, it will be evaluated by this class.
$ringside = new RingsideApiClients('idmapapp', 'idmapappsecret', $webUrl, $serverUrl );

$ringside->require_login();

$mapped_stored_user = '<ERROR>';
error_log("Calling idmap get_data");

// From Facebook developer's wiki: http://wiki.developers.facebook.com/index.php/Gotchas
// This is how you get $_SESSION to work with Facebook
if (isset($_POST["fb_sig_session_key"]))
{
  $_fb_sig_session_key = str_replace("-","0",$_POST["fb_sig_session_key"]);
  session_id($_fb_sig_session_key);
}
session_start();

error_log("Stored UID is ".$_SESSION['test_uid']);
// The only mapping we're doing here is _from_ the principal _to_ the network; so we ALWAYS pass a second argument.

$stored_user = $_SESSION['test_uid'];
if ( strpos($stored_user, ',') !== false ) {
	$stored_user = explode(',', $stored_user);
}

if ( $_REQUEST['nid'] ) {
	$mapped_stored_user = $ringside->api_client->users_mapToSubject($_SESSION['test_uid'], $_REQUEST['nid']);
} else {
	$mapped_stored_user = $ringside->api_client->users_mapToSubject($_SESSION['test_uid']);
}

error_log("Mapped id is $mapped_stored_user");
echo "Result{".$mapped_stored_user."}";
?>