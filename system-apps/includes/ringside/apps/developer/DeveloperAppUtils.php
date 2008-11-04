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

require_once("ringside/web/RingsideWebUtils.php");
require_once("ringside/api/clients/RingsideApiClients.php");
require_once("ringside/api/db/RingsideApiDbDatabase.php");


// TODO: SECURITY: Rewrite ALL database stuff in here
class DeveloperAppUtils
{
	public static function getAppsForUser($uid)
	{
		$sql = "SELECT app_id FROM developer_app WHERE user_id=$uid";		
		$db = RingsideApiDbDatabase::getDatabaseConnection();
		
		$idList = array();
		if ($result = mysql_query($sql, $db)) {			
			while ($row = mysql_fetch_assoc($result)) {
				$key = $row["app_id"];
				$idList[] = $key;				
			}
		} else {
			$msg = "Failed to get user apps: '" . mysql_error() . "'\nSQL='$sql'";			
			throw new Exception($msg);
		}
				
		$fb = new RingsideApiClients(RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey);		
		$appProps = array();		
		$propNames = array("application_id", "application_name", "api_key",'icon_url');
		foreach ($idList as $akey) {
		    error_log("Retrieving info for $akey");
			$app = $fb->api_client->admin_getAppProperties($propNames, $akey);
			error_log(var_export($app, true));
			$appProps[] = $app;
		}

		return $appProps;
	}
	
	/*
	 * Creates the application with the given name,
	 * returns an error message or null if no error.
	 */
	public static function createApp($uid, $name)
	{
		$fb = new RingsideApiClients(RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey);
		$resp = $fb->api_client->admin_createApp($name);
		error_log(var_export($resp, true));
		$appId = $resp["app"]["application_id"];
			
		$sql = "INSERT INTO developer_app (user_id,app_id) VALUES ($uid,$appId);";		
		$db = RingsideApiDbDatabase::getDatabaseConnection();
			
		if (!($result = mysql_query($sql, $db))) {
			throw new Exception("DB error: " . mysql_error() . "\nSQL='$sql'");
		}
		
		return $appId;
	}
	
	public static function deleteApp($appId, $uid)
	{
		$fb = new RingsideApiClients(RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey);
		$resp = $fb->api_client->admin_deleteApp($appId);
		
		if ($resp) {
    		$sql = "DELETE FROM developer_app WHERE app_id=$appId AND user_id=$uid";		
    		$db = RingsideApiDbDatabase::getDatabaseConnection();
    			
    		if (!($result = mysql_query($sql, $db))) {
    			throw new Exception("DB error: " . mysql_error() . "\nSQL='$sql'");
    		}
    		return true;
		}
		return false;
	}
	
	public static function updateApp($appId, $apiKey, $uid)
	{
	    // TODO: DEPRECATE: Can't update API key or secret
		$sql = "UPDATE developer_app SET api_key='$apiKey' WHERE user_id=$uid AND app_id=$appId";
			
		$db = RingsideApiDbDatabase::getDatabaseConnection();
			
		try{
		if (!($result = mysql_query($sql, $db))) {
			throw new Exception("DB error: " . mysql_error() . "\nSQL='$sql'");
		}
		}catch(Exception $e)
		{
			throw new Exception("Unable to update data, API Key cannot be changed to that of another application!");
		}
	}
}


?>
