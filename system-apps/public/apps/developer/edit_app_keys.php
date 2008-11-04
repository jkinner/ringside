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

require_once("header.inc");

$props = array( "application_id", "application_name", "api_key" );
$apiKey = getRequestParam( 'api_key' );
$appId = getRequestParam('app_id');
$resp = $client->api_client->admin_getAppProperties( $props, $appId );
$appName = $resp["application_name"];
$pageHeader = "Edit Application: $appName";

$statusMessage = null;
$errorMessage = null;

$pageHeader = "Edit Application: $appName";

$formAction = getRequestParam('form_action');

if ($formAction == 'update') {

	//grab key information from request values
	$netKeys = array();
	foreach ($_POST as $rname => $rval) {
		$aname = explode('_', $rname);
		if (count($aname) == 3) {
			$nid = $aname[2];			
			if (($aname[0] == 'api') && ($aname[1] == 'key')) {
    			if (!array_key_exists($nid, $netKeys)) $netKeys[$nid] = array();
    			$netKeys[$nid]['api_key'] = $rval;
			} else if (($aname[0] == 'secret') && ($aname[1] == 'key')) {
				if (!array_key_exists($nid, $netKeys)) $netKeys[$nid] = array();
    			$netKeys[$nid]['secret'] = $rval;
			}
		}
	}
	
	//convert $netKeys to something user.setAppKeys can understand
	$keyProps = array();
	foreach ($netKeys as $nid => $props) {
		$keyProps[] = array('network_id' => $nid, 'api_key' => $props['api_key'],
								  'secret' => $props['secret']);
	}
	
	//set keys
	try {
		$client->api_client->admin_setAppKeys($keyProps, $appId);
	} catch (Exception $e) {
		$errorMessage = 'Could not set keys: ' . $e->getMessage();		
	}
	
	if ($errorMessage == null) {
		RingsideWebUtils::redirect("edit_app_keys.php?app_id=$appId&success=true");
	} else {
		try {
			$keySets = $client->api_client->admin_getAppKeys($appId);
		} catch (Exception $e) {
			$errorMessage .= '<br />Could not retrieve app keys with given application ID.';
		}
	}

} else {
	$keySets = $client->api_client->admin_getAppKeys($appId);
	
	$success = getRequestParam('success');
	if (($success != null) && ($success == 'true')) {
		$statusMessage = 'Keys saved successfully.';
	}
	
	$formAction = 'update';
}

include("ringside/apps/developer/templates/edit_app_keys.tpl");

?>