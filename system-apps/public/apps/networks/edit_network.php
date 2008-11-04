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
$pageHeader = "";
$formAction = getRequestParam('form_action');

$key = getRequestParam('key');

if ($formAction == 'edit') {

    $props = array('key', 'name', 'auth_url', 'login_url',
    							'canvas_url', 'web_url', 'social_url',
    							'auth_class', 'postmap_url');
    try {
    	$resp = $client->api_client->admin_getNetworkProperties(array($key), $props);
    } catch (Exception $e) {
    	$errorMessage = 'Could not get network properties: ' . $e->getMessage(); 
    }
    
    if ($errorMessage == null) {
        $name = $resp[0]['name'];
        $key = $resp[0]['key'];
        $authUrl = $resp[0]['auth_url'];
        $loginUrl = $resp[0]['login_url'];
        $canvasUrl = $resp[0]['canvas_url'];
        $webUrl = $resp[0]['web_url'];
        $socialUrl = $resp[0]['social_url'];
        $authClass = $resp[0]['auth_class'];
        $postmapUrl = $resp[0]['postmap_url'];
        
        $success = getRequestParam('success');
        if ($success != null) {
        	$statusMessage = 'Network successfully updated.';
        }
        $created = getRequestParam('created');
        if ($created != null) {
        	$statusMessage = 'Network successfully created.';
        }
    }
    
    $formAction = 'update';
    $pageHeader = 'Edit Network';
    $submitText = 'Save Changes';   
    
} else if ($formAction == 'update') {
	
	$props = array('key', 'name', 'auth_url', 'login_url',
    					'canvas_url', 'web_url', 'social_url',
    					'auth_class', 'postmap_url');
	$oldKey = getRequestParam('old_key');
	$newVals = array(); 
	foreach ($props as $name) {
		$newVals[$name] = getRequestParam($name, '');
	}
	   
	try {
   	$resp = $client->api_client->admin_setNetworkProperties($oldKey, $newVals);
	} catch (Exception $e) {
		$errorMessage = 'Could not set properties: ' . $e->getMessage();
	}
   if ($errorMessage == null) {
   	RingsideWebUtils::redirect("edit_network.php?key=$key&form_action=edit&success=true");
   } else {   
   	$getFailed = false;
   	try {
        	$resp = $client->api_client->admin_getNetworkProperties(array($oldKey), $props);
      } catch (Exception $e) {
      	$getFailed = true;
        	$errorMessage = 'Could not get network properties: ' . $e->getMessage(); 
      }
        
      if (!$getFailed) {
      	$name = $resp[0]['name'];
         $key = $resp[0]['key'];
         $authUrl = $resp[0]['auth_url'];
         $loginUrl = $resp[0]['login_url'];
         $canvasUrl = $resp[0]['canvas_url'];
         $webUrl = $resp[0]['web_url'];
         $socialUrl = $resp[0]['social_url'];
         $authClass = $resp[0]['auth_class'];
         $postmapUrl = $resp[0]['postmap_url'];
      }
   
   	$formAction = 'update';
    	$pageHeader = 'Edit Network';
    	$submitText = 'Save Changes';
   }

} else if ($formAction == 'new') {

	$pageHeader = 'Create a New Network';
	$formAction = 'new';
	$submitText = 'Create Network';
	
	$errorMessage = null;
	
	$name = getRequestParam('name', '');
	if (strlen($name) == 0) $errorMessage = 'Please specify a network name.';	
	$authUrl = getRequestParam('auth_url', '');
	if (strlen($authUrl) == 0) $errorMessage = 'Please specify an authorization URL.';
	$loginUrl = getRequestParam('login_url', '');
	if (strlen($loginUrl) == 0) $errorMessage = 'Please specify an login URL.';
	$canvasUrl = getRequestParam('canvas_url', '');
	if (strlen($canvasUrl) == 0) $errorMessage = 'Please specify an canvas URL.';
	$webUrl = getRequestParam('web_url', '');
	if (strlen($webUrl) == 0) $errorMessage = 'Please specify an web URL.';
	
	if ($errorMessage == null) {
		try {
			$resp = $client->api_client->admin_createNetwork($name, $authUrl, $loginUrl, $canvasUrl, $webUrl);			
			$key = $resp['network']['key'];
		} catch (Exception $e) {
			$errorMessage = "Error creating app: " . $e->getMessage();
		}
	}
	if ($errorMessage == null) {
		RingsideWebUtils::redirect("edit_network.php?key=$key&created=true&form_action=edit");
	}


} else {

	$pageHeader = 'Create a New Network';
	$statusMessage = 'Please select a unique name for your network. ' .
						  'A key will be created for you.';
	$formAction = 'new';
	$submitText = 'Create Application';
	$name = '';
}



include("ringside/apps/networks/templates/edit_network.tpl");

?>