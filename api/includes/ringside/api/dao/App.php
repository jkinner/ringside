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
require_once ('ringside/api/config/RingsideApiConfig.php');
require_once ('ringside/api/dao/records/RingsideApp.php');
require_once ('ringside/api/dao/records/RingsideUsersApp.php');
require_once ('ringside/api/dao/records/RingsideDeveloperApp.php');

/**
 * Represents a row in the OpenFB app table.
 */
class Api_Dao_App
{

	/**
	 * Returns a list of apps that this user has added to their profile
	 *
	 * @param int $uid
	 */
	public static function getApplicationListByUid($uid)
	{
		$q = Doctrine_Query::create();
		$q->select('u.*, a.*')->from('RingsideUsersApp u LEFT JOIN u.RingsideApp a ON u.app_id=a.id')->where("u.enabled=1 AND u.user_id=$uid");
		return $q->execute();
	}

	/**
	 * Returns all apps registered in the system
	 */
	public static function getApplicationList()
	{
		$q = Doctrine_Query::create();
		$q->from('RingsideApp a');
		return $q->execute();
	}

	public static function getFullApplicationList()
	{
		$q = Doctrine_Query::create();
		$q->from('RingsideApp a')->leftJoin('a.keys');
		return $q->execute();
	}
	
	/**
	 * Returns the Data for this Application ID
	 *
	 * @param unknown_type $id
	 * @return unknown
	 */
	public static function getApplicationInfoById($id, $networkId = null)
	{
		return self::getApplicationInfoByProperty($id, 'id', $networkId);
	}

	private static function _restrictQueryToNetworkId(Doctrine_Query $q, $networkId)
	{
	   if ( null != $networkId )
		{
		    $q->leftJoin('a.keys as ak')->where('ak.network_id = ? AND ak.app_id = a.id', array($networkId));
		}
	    
	}
	/**
	 * Returns all fields for all apps with the given ids.
	 *
	 * @param unknown_type $aids
	 * @return unknown
	 */
	public static function getApplicationInfoByIds($aids, $network_id = null)
	{
		if(count($aids > 0))
		{
			$q = Doctrine_Query::create();
			$q->from('RingsideApp a');
			self::_restrictQueryToNetworkId($q, $network_id);
			$q->whereIn('a.id', $aids);
			
			return $q->execute();
		}
		return array();
	}

	/**
	 * Get application information from it's name.
	 *
	 * @param unknown_type $applicationName
	 * @return unknown
	 */
	public static function getApplicationInfoByName($applicationName, $networkId = null)
	{
		return self::getApplicationInfoByProperty($applicationName, 'name', $networkId);
	}

	/**
	 * Get application information from it's canvas name.
	 *
	 * @param unknown_type $applicationName
	 * @param unknown_type $dbCon
	 * @return unknown
	 */
	public static function getApplicationInfoByCanvasName($applicationCanvasName, $networkId = null)
	{
		return self::getApplicationInfoByProperty($applicationCanvasName, 'canvas_url', $networkId);
	}

	/**
	 * Get application information from it's api key.
	 */
	public static function getApplicationInfoByApiKey($apiKey, $networkId)
	{
		$q = Doctrine_Query::create();
		$q->from('RingsideApp a')->innerJoin('a.keys ak')->where("ak.network_id = ? AND ak.api_key = ?");
		return $q->execute(array($networkId, $apiKey));
	}

	/**
	 * Get application information from the specified property column
	 * and value.
	 *
	 * @param unknown_type $propertyVal The value of the DB column
	 * @param unknown_type $colName The name of the DB column.
	 * @return unknown
	 */
	private static function getApplicationInfoByProperty($propertyVal, $colName, $networkId = null)
	{
		$q = Doctrine_Query::create();
		$q->from('RingsideApp a');
		self::_restrictQueryToNetworkId($q, $networkId);
		$q->where("a.$colName = ?", $propertyVal);
		
		return $q->execute();
	}

	/**
	 * Returns true if the user is a developer of the app with aid.
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $aid
	 * @return unknown
	 */
	public static function checkUserOwnsApp($uid, $aid)
	{
		$q = Doctrine_Query::create();
		$q->select('d.user_id')->from('RingsideDeveloperApp d')->where("d.user_id=? AND d.app_id = ?", array($uid, $aid));
		$apps = $q->execute();
		
		if($apps->count() > 0)
		{
			if($uid == intval($apps[0]->user_id))
			{
				return true;
			}
		}
		
		return false;
	}
	
	public static function setAppOwner($uid, $aid)
	{
		$dapp = new RingsideDeveloperApp();
		$dapp->user_id = $uid;
		$dapp->app_id = $aid;
		return $dapp->save();
	}
	
	public static function removeAppOwner($uid, $aid)
	{
		$q = Doctrine_Query::create();
		$q->select('d.user_id')->from('RingsideDeveloperApp d')->where("d.user_id=? AND d.app_id = ?", array($uid, $aid));
		$apps = $q->execute();
		
		if($apps->count() > 0) {
			return $apps[0]->delete();
		}
		return false;
	}

	/*
	 * Returns the app ID associated with the given API key, or -1.
	 */
	public static function getAppIdByApiKey($apiKey, $networkId)
	{
	    	    // TODO: Figure out whether this needs to "graduate" to BO layer
		$q = Doctrine_Query::create();
		$q->select('a.id as id')->from('RingsideApp a')->innerJoin('a.keys ak')->where('ak.api_key = ? AND ak.network_id = ?', array($apiKey, $networkId));
		
		$apps = $q->execute();
		if($apps->count() > 0)
		{
			return $apps[0]->id;
		}
		
		return false;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $apiKey
	 * @return unknown
	 */
	public static function getAppIdByApiKeyAndSecret($apiKey, $secret, $networkId)
	{
		$q = Doctrine_Query::create();
		$q->select('app_id')->from('RingsideAppKey')->where('network_id = ? and api_key = ? and secret = ?');
		
		$apps = $q->execute(array($networkId, $apiKey, $secret));
		if($apps->count() > 0)
		{
			return $apps[0]->id;
		}
		
		return false;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $name
	 */
	public static function getAppIdByName($name)
	{
		$q = Doctrine_Query::create();
		$q->select('id')->from('RingsideApp a')->where("name='$name'");
		
		$apps = $q->execute();
		if($apps->count() > 0)
		{
			return $apps[0]->id;
		}
		
		return false;
	}

	public static function getAppIdByCanvasName($canvasName, $networkId = null)
	{
		$q = Doctrine_Query::create();
		$q->select('id')->from('RingsideApp a')->where("canvasUrl=?", array($canvasName));
		self::_restrictQueryToNetworkId($q, $networkId);
		$apps = $q->execute();
		if($apps->count() > 0)
		{
			return $apps[0]->id;
		}
		
		return false;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $apiKey
	 * @param unknown_type $dbCon
	 */
	public static function deleteAppByApiKey($apiKey, $networkId)
	{
		$appId = self::getAppIdByApiKey($apiKey, $networkId);
		if($appId === false)
		{
			throw new Exception("Could not obtain ID given API key '$apiKey'");
		}
		
		self::deleteApp($appId);
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $apiKey
	 * @param unknown_type $dbCon
	 */
	public static function deleteAppByCanvasName($canvasName)
	{
		$appId = self::getAppIdByCanvasName($canvasName);
		if($appId === false)
		{
			throw new Exception("Could not obtain ID given canvas name '$canvasName'");
		}
		
		self::deleteApp($appId);
	}
	
	/*
	 * Update properties for the application specified by $apiKey.
	 * $props is an associative array, with keys in format of <tablename>.<fieldname>,
	 * where <tablename> can be "app", and <fieldname>
	 * is the name of the field to update.
	 */
	public static function updateAppProperties($appId, $props, $networkId)
	{
		$tableMap = array();
		//parse up property table/field names
		foreach($props as $name=>$val)
		{
			$tmp = explode(".", $name);
			if(count($tmp) < 2)
			{
				throw new Exception("No table name specified in property '$name'.");
			}
			$tbl = $tmp[0];
			$fld = $tmp[1];
			
			if(! isset($tableMap[$tbl]))
				$tableMap[$tbl] = array();
			$tableMap[$tbl][] = $fld;
		}
		
		//construct, execute update SQL
		// NOTE: The connection for RingsideApp must be the same as the related tables that contain the rest of the properties
		$conn = Doctrine_Manager::getInstance()->getConnectionForComponent('RingsideApp');
		$conn->beginTransaction();		
		try
		{
    		foreach($tableMap as $tbl=>$flds)
    		{
    			$vals = array();
    			foreach($flds as $fld)
    			{
    				$str = $props["$tbl.$fld"];
    				$vals[$fld] = "$str";
    			}
    			
    			if(! empty($vals))
    			{
    				$q = Doctrine_Query::create();
    				$update = $q->update($tbl);    				
    				foreach($vals as $k=>$v)
    				{    				
    					$update->set($k, '?', $v);
    				}
    				if ( $tbl == 'RingsideApp' )
    				{
    				    $q->where("id=$appId");
    				}
    				else
    				{
    				    // TODO: Assuming app_id is the foreign key column on the other tables; maybe a little presumptuous?
    				    $q->where("app_id=? AND network_id=?", array($appId, $networkId));
    				}    				
    				$q->execute();
    			}
    		}
    		$conn->commit();
		}
		catch ( Exception $e )
		{
		    error_log($e->getMessage());
		    $conn->rollback();
		}
	}

	/**
	 * Sets the api key and secret key for this app.
	 *
	 * @param unknown_type $new_api_key
	 * @param unknown_type $new_secret_key
	 * @param unknown_type $current_api_key
	 */
	public static function setKeys($new_api_key, $new_secret_key, $app_id, $networkId)
	{
		$q = Doctrine_Query::create();
		$q->update('RingsideAppKey');
		if ( $new_api_key != null )
		{
		    $q->set('api_key', '?', $new_api_key);
		}
		if ( $new_secret_key != null )
		{
		    $q->set('secret', '?', $new_secret_key);
		}
		return $q->where("network_id = ? AND app_id= ?", array($networkId, $app_id))->execute();
	}

	/**
	 * Creates an app
	 *
	 * @param unknown_type $api_key
	 * @param unknown_type $callback_url
	 * @param unknown_type $canvas_url
	 * @param unknown_type $name
	 * @param unknown_type $secret_key
	 * @param unknown_type $sidenav_url
	 * @param unknown_type $icon_url
	 * @param unknown_type $canvas_type
	 * @return unknown
	 */
	public static function createApp($api_key, $callback_url, $canvas_url, $name, $default = 0, $secret_key, $sidenav_url, $icon_url = null, $canvas_type = 0, $desktop = 0, $developer_mode = 0, $author = null, $author_url = null, $author_description = null, $support_email = null, $application_type = null, $mobile = 0, $deployed = 0, $description = null, $default_fbml = null, $tos_url = null, $postadd_url = null, $postremove_url = null, $privacy_url = null, $ip_list = null, $about_url = null, $logo_url = null, $edit_url = null, $default_column = 1, $attachment_action = null, $attachment_callback_url = null, $nativeId = NULL)
	{
		$app = new RingsideApp();
		$app->callback_url = $callback_url;
		$app->canvas_url = $canvas_url;
		$app->name = $name;
		$app->sidenav_url = $sidenav_url;
		$app->isdefault = $default;
		$app->icon_url = $icon_url;
		$app->canvas_type = $canvas_type;
		$app->desktop = $desktop;
		$app->developer_mode = $developer_mode;
		$app->author = $author;
		$app->author_url = $author_url;
		$app->author_description = $author_description;
		$app->support_email = $support_email;
		$app->application_type = $application_type;
		$app->mobile = $mobile;
		$app->deployed = $deployed;
		$app->description = $description;
		$app->default_fbml = $default_fbml;
		$app->tos_url = $tos_url;
		$app->postadd_url = $postadd_url;
		$app->postremove_url = $postremove_url;
		$app->privacy_url = $privacy_url;
		$app->ip_list = $ip_list;
		$app->about_url = $about_url;
		$app->logo_url = $logo_url;
		$app->edit_url = $edit_url;
		$app->default_column = $default_column;
		$app->attachment_action = $attachment_action;
		$app->attachment_callback_url = $attachment_callback_url;
		if ($nativeId != NULL) $app->id = $nativeId;
		
		$ret = $app->trySave();
		
		if($ret)
		{
			return $app->id;
		}
		
		return false;
	}

	/**
	 * Deletes an App
	 *
	 * @param unknown_type $aid
	 * @return unknown
	 */
	public static function deleteApp($id)
	{
		$q = new Doctrine_Query();
		
		return $q->delete('RingsideApp')->from('RingsideApp a')->where("id = $id")->execute();
	}
}
?>