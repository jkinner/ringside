<?php
/*******************************************************************************
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
 ******************************************************************************/

require_once('header.inc');

$statusMessage = null;
$errorMessage = null;
$formAction = getRequestParam('form_action');
$key = getRequestParam('key');

if ($formAction == 'delete') {
	if ($key == null) {
		$errorMessage = 'No key specified.';
	}
	if ($errorMessage == null) {
		try {			
			$client->api_client->admin_deleteNetwork($key);		
		} catch (Exception $e) {
			$errorMessage = 'Could not delete network: ' . $e->getMessage();
		}		
	}
	if ($errorMessage == null) {
		RingsideWebUtils::redirect('index.php');
	}

} else if ($formAction == null) {
	if ($key == null) {
		$errorMessage = 'No key specified.';
	}
	if ($errorMessage == null) {
		$formAction = 'delete';
	    	
    	$resp = $client->api_client->admin_getNetworkProperties(array($key), array('name'));		
    	$name = $resp[0]['name'];
    	
    	$statusMessage = "Are you sure you want to delete the network '$name'?";
	}
}

include('ringside/apps/networks/templates/delete_network.tpl');

?>
