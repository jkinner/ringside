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
/**
 * Demonstrates how to perform identity mapping.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
error_log("Facebook params:");
error_log(var_export($_REQUEST, true));
$ringside = null;
if ( $_REQUEST['fb_sig_nid'] == 'facebook' ) {
	error_log("Initializing Facebook client");
	$ringside = new RingsideApiClients('c392078dde1c96d4c019d60420fd07ab', '93694d948f9b1cf0d8567ccf7e67a860', 'http://www.facebook.com', 'http://api.facebook.com/restserver.php', 'http://68.39.18.144:8888/social');
} else {
	error_log("Initializing Ringside client");
	$ringside = new RingsideApiClients('c392078dde1c96d4c019d60420fd07ab', '93694d948f9b1cf0d8567ccf7e67a860', 'http://68.39.18.144:8888/web/', 'http://68.39.18.144:8888/api/restserver.php', 'http://68.39.18.144:8888/social');
}

// This will cause redirect if this user is not yet a principal
$ringside->require_network_login(); 
?>You are principal <?php echo $ringside->get_network_user() ?> on <?php echo $ringside->get_network_id() ?><br/>
Your name on this network is <fb:name uid="loggedinuser" useyou="false"/>