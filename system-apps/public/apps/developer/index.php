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

$ringside = new RingsideApiClients( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey );
$ringside->setLocalClient( true );
$uid = $ringside->require_login( );
if ( !isset( $uid) ) { 
   return;
}

require_once("header.inc");

$appsCount = 0;
$appProps = DeveloperAppUtils::getAppsForUser($uid);
error_log(var_export($appProps, true));
$appListHtml = "<i>No developer apps added.</i>";
if (count($appProps) > 0) {	
	$appListHtml = "";
	$appListHtml .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" id=\"apps-list\">";
	
	foreach ($appProps as $app) {
		$name = $app["application_name"];
		$appId = $app['application_id'];
		$apiKey = $app["api_key"];
		$icon_url = $app["icon_url"];
		$appClassAttr = '';
		$appClassAttr .= ($appsCount==0) ? ' first' : '';
		$appClassAttr .= ($appsCount==count($appProps)-1) ? ' last' : '';
		$appListHtml .= "<tr>";
		$appListHtml .= "<td class=\"app-name ".$appClassAttr."\" width=\"240\">";
		$iconStyle    = " style=\"background:url(".$app['icon_url'].") no-repeat; padding-left:22px;\"";
		$appListHtml .= "<a href=\"edit_app.php?app_id=$appId&form_action=edit\" $iconStyle>$name</a>&#160;&#160;&#160;";
		$appListHtml .= "</td>";
		$appListHtml .= "<td class=\"app-action".$appClassAttr."\">";
		$appListHtml .= "<a href=\"delete_app.php?app_id=$appId\" class=\"developer-action\">Delete this App</a>";
		$appListHtml .= "</td>";
		$appListHtml .= "</tr>";
		$appsCount++;
	}
	$appListHtml .= "</table>";
}
include("ringside/apps/developer/templates/index.tpl");


?>

