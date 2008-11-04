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

$statusMessage = null;
$errorMessage = null;
$pageHeader = "";
$formAction = getRequestParam("form_action");
$appId = getRequestParam('app_id');
$apiKey = getRequestParam('api_key');

$secret = getRequestParam("secret_key");

//process form input
if ($formAction == "edit") {

	if ($appId == null) {
		$errorMessage = "No app ID specified.";
	}

	if ($errorMessage == null) {
		$props = array("application_name","secret_key","email","callback_url","canvas_url",
							"use_iframe","desktop","is_mobile","ip_list","dev_mode","tos_url",
							"icon_url","installable","post_install_url","uninstall_url",
							"description","default_fbml","default_column","dev_mode",
							"edit_url","sidenav_url","privacy_url","attachment_action",
							"attachment_callback_url","icon_url", "api_key");
		$resp = $client->api_client->admin_getAppProperties($props, $appId);

		error_log("Developer APp Properties:");
		error_log(var_export($resp, true));
		$secret = $resp['secret_key'];
		$appName = $resp["application_name"];
		$email = $resp["email"];
		$callbackUrl = $resp["callback_url"];
		$canvasUrl = $resp["canvas_url"];
		$ipList = $resp["ip_list"];
		$tosUrl = $resp["tos_url"];
		$postInstallUrl = $resp["post_install_url"];
		$postRemoveUrl = $resp["uninstall_url"];
		$description = $resp["description"];
		$defaultFbml = $resp["default_fbml"];
		$editUrl = $resp["edit_url"];
		$sidenavUrl = $resp["sidenav_url"];
		$privacyUrl = $resp["privacy_url"];
		$attachmentAction = $resp["attachment_action"];
		$attachmentCallbackUrl = $resp["attachment_callback_url"];
		$iconUrl = $resp["icon_url"];
      $apiKey = $resp['api_key'];
      
		$developerModeChecked = "";
		$developerMode = $resp["dev_mode"];
		if ($developerMode) $developerModeChecked = "checked";

		$defaultColumn = $resp["default_column"];
		if ($defaultColumn) {
			$wideChecked = "checked";
			$narrowChecked = "";
		} else {
			$wideChecked = "";
			$narrowChecked = "checked";
		}

		$iframe = $resp["use_iframe"];
		if ($iframe == 0) {
			$iframeChecked = "checked";
			$fbmlChecked = "";
			$osChecked = "";
		} else if ($iframe == 1) {
			$iframeChecked = "";
			$fbmlChecked = "checked";
			$osChecked = "";
		} else if ($iframe == 2) {
			$iframeChecked = "";
			$fbmlChecked = "";
			$osChecked = "checked";
		}

		$desktop = $resp["desktop"];
		if ($desktop) {
			$desktopChecked = "checked";
			$webChecked = "";
		} else {
			$desktopChecked = "";
			$webChecked = "checked";
		}

		$mobileChecked = "";
		$mobile = $resp["is_mobile"];
		if ($mobile)  $mobileChecked = "checked";

		$deployed = $resp["installable"];
		if ($deployed) {
			$yesDeployedChecked = "checked";
			$noDeployedChecked = "";
		} else {
			$yesDeployedChecked = "";
			$noDeployedChecked = "checked";
		}

		$submitText = "Save Changes";
	}

	$formAction = "update";
	$pageHeader = "Edit Application: $appName";
	
	$success = getRequestParam('success');
	if ($success != null) {
		if ($success == 'true') {
			$statusMessage = '<br /><font color="green"><b>Changes successfully saved.</b></font><br /><br />';
		} else if ($success == 'false') {
			$errorMessage = 'Failed to save changes.';
		}
	}

} else if ($formAction == "update") {

	if ($appId == null) {
		$errorMessage = "No app ID specified.";
	}

	if ($errorMessage == null) {
		$props = array();
		$props['api_key'] = getRequestParam('api_key');
		$props['secret_key'] = getRequestParam('secret_key');
		$props["application_name"] = getRequestParam("app_name");
		$props["email"] = getRequestParam("email");

		$callbackUrl = getRequestParam( 'callback_url' );
// This does not work for OS or any app that uses a document as a cllback URL (wreichardt)
//		if( isset( $callbackUrl ) && !empty( $callbackUrl ) ) {
//    		$lastchar = substr( $callbackUrl, ( strlen( $callbackUrl ) - 1 ), 1 );
//            $hasslash = ( $lastchar == '/' ) ? true : false;
//            if( !$hasslash ) {
//                $callbackUrl .= '/';
//            }
//		}
		$props["callback_url"] = $callbackUrl;
		$props["canvas_url"] = getRequestParam("canvas_url");
		$props["ip_list"] = getRequestParam("ip_list");
		$props["tos_url"] = getRequestParam("tos_url");
		 
		$useIframe = getRequestParam("canvas_type");
		if ($useIframe == "iframe") {
			$ctype = 0;
		} else if ($useIframe == "fbml") {
			$ctype = 1;
		} else if ($useIframe == "os") {
			$ctype = 2;
		}
		$props["use_iframe"] = $ctype;
		 
		$props["desktop"] = (getRequestParam("app_type") == "desktop") ? 1 : 0;
		$props["is_mobile"] =  (getRequestParam("mobile") == null) ? 0 : 1;
		$props["installable"] = intval(getRequestParam("deployed"));
		$props["post_install_url"] = getRequestParam("post_install_url");
		$props["uninstall_url"] = getRequestParam("post_remove_url");
		$props["description"] = getRequestParam("description");
		$props["default_fbml"] = getRequestParam("default_fbml");
		$props["default_column"] = getRequestParam("default_column");
		$props["dev_mode"] = (getRequestParam("dev_mode") == null) ? 0 : 1;
		$props["edit_url"] = getRequestParam("edit_url");
		$props["sidenav_url"] = getRequestParam("sidenav_url");
		$props["privacy_url"] = getRequestParam("privacy_url");
		$props["attachment_action"] = getRequestParam("attachment_action");
		$props["attachment_callback_url"] = getRequestParam("attachment_callback_url");
		$props["icon_url"] = getRequestParam("icon_url");
		 
		$resp = null;
		try{
			$resp = $client->api_client->admin_setAppProperties($props, $appId);
		}catch(Exception $e)
		{
			$errorMessage = $e->getMessage();
		}
		if ( $resp === null ) {
			if(!isset($errorMessage) && strlen($errorMessage) == 0)
			{
				$errorMessage = "Unspecified error occurred.";
			}
		} else {

			RingsideWebUtils::redirect("edit_app.php?app_id=$appId&form_action=edit&success=true");
		}
	}

} else if ($formAction == "new") {

	$pageHeader = "Create a New Application";
	$formAction = "new";
	$submitText = "Create Application";
	$appName = getRequestParam("app_name");
	if ($appName == null) {
		$errorMessage = "Please specify an application name.";
	}

	if ($errorMessage == null) {
		try {
			$appId = DeveloperAppUtils::createApp($uid, $appName);
		} catch (Exception $e) {
			$errorMessage = "Error creating app: " . $e->getMessage();
		}
	}
	if ($errorMessage == null) {
		RingsideWebUtils::redirect("new_app_success.php?app_id=$appId");
	}

} else if ($formAction == null) {

	$pageHeader = "Create a New Application";
	$statusMessage = "Please select a unique name for your application. " .
						  "An API key and secret will be created for you.";
	$formAction = "new";
	$submitText = "Create Application";
	$appName = '';
}


include("ringside/apps/developer/templates/edit_app.tpl");

?>
