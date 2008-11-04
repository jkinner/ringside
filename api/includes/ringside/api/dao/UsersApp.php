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
require_once ('ringside/api/dao/App.php');
require_once ('ringside/api/dao/Network.php');
require_once ('ringside/api/dao/records/RingsideUsersApp.php');
require_once ('ringside/api/dao/records/RingsideAppKey.php');

/**
 * Represents a row in the OpenFB users_app table.
 */
class Api_Dao_UsersApp
{

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $appId
	 * @param unknown_type $uid
	 * @return unknown
	 */
	public static function isUsersApp($appId, $uid)
	{
		$q = Doctrine_Query::create();
		$q->select('count(id) as ua_count')->from('RingsideUsersApp')->where("app_id=$appId AND user_id=$uid and enabled = 1");
		$ua = $q->execute();
		
		if($ua[0]['ua_count'] > 0)
		{
			return true;
		}
		return false;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $user_id
	 * @param unknown_type $allows_status_update
	 * @param unknown_type $allows_create_listing
	 * @param unknown_type $allows_photo_upload
	 * @param unknown_type $auth_information
	 * @param unknown_type $auth_profile
	 * @param unknown_type $auth_leftnav
	 * @param unknown_type $auth_newsfeeds
	 * @param unknown_type $profile_col
	 * @param unknown_type $profile_order
	 * @param unknown_type $fbml
	 * @return unknown
	 */
	public static function updateUsersApp($app_id, $user_id, $allows_status_update = 0, $allows_create_listing = 0, $allows_photo_upload = 0, $auth_information = 0, $auth_profile = 0, $auth_leftnav = 0, $auth_newsfeeds = 0, $profile_col = 'wide', $profile_order = 0, $fbml = '')
	{
		$q = Doctrine_Query::create();
		$q->update('RingsideUsersApp')->set('enabled', '?', 1)->set('allows_status_update', '?', $allows_status_update)->set('allows_create_listing', '?', $allows_create_listing)->set('allows_photo_upload', '?', $allows_photo_upload)->set('auth_information', '?', $auth_information)->set('auth_profile', '?', $auth_profile)->set('auth_leftnav', '?', $auth_leftnav)->set('auth_newsfeeds', '?', $auth_newsfeeds)->set('fbml', '?', $fbml)->set('profile_col', '?', $profile_col)->set('profile_order', '?', $profile_order)->where("app_id=$app_id AND user_id=$user_id");
		
		return $q->execute();
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $user_id
	 * @param unknown_type $allows_status_update
	 * @param unknown_type $allows_create_listing
	 * @param unknown_type $allows_photo_upload
	 * @param unknown_type $fbml
	 * @param unknown_type $auth_information
	 * @param unknown_type $auth_profile
	 * @param unknown_type $auth_leftnav
	 * @param unknown_type $auth_newsfeeds
	 * @param unknown_type $enabled
	 * @param unknown_type $profile_col
	 * @param unknown_type $profile_order
	 * @return unknown
	 */
	public static function createUsersApp($app_id, $user_id, $allows_status_update = 0, $allows_create_listing = 0, $allows_photo_upload = 0, $auth_information = 0, $auth_profile = 0, $auth_leftnav = 0, $auth_newsfeeds = 0, $profile_col = 'wide', $profile_order = 0, $fbml = '')
	{
		$ua = new RingsideUsersApp();
		$ua->app_id = $app_id;
		$ua->user_id = $user_id;
		$ua->allows_status_update = $allows_status_update;
		$ua->allows_create_listing = $allows_create_listing;
		$ua->allows_photo_upload = $allows_photo_upload;
		$ua->auth_information = $auth_information;
		$ua->auth_profile = $auth_profile;
		$ua->auth_leftnav = $auth_leftnav;
		$ua->auth_newsfeeds = $auth_newsfeeds;
		$ua->enabled = 1;
		$ua->profile_col = $profile_col;
		$ua->profile_order = $profile_order;
		$ua->fbml = $fbml;
		$ret = $ua->trySave();
		
		if($ret)
		{
			return $ua->getIncremented();
		}
		
		return false;
	}

	/**
	 * Deletes a UsersApp entry
	 *
	 * @param unknown_type $id
	 */
	public static function deleteUserApp($id)
	{
		$q = Doctrine_Query::create();
		$q->delete()->from('RingsideUsersApp ua')->where("id = $id");
		$q->execute();
	}

	/**
	 * Deletes a User App by application id and user id
	 *
	 * @param unknown_type $appId
	 * @param unknown_type $uid
	 */
	public static function deleteUserAppByAppIdAndUid($appId, $uid)
	{
		$q = Doctrine_Query::create();
		$q->delete()->from('RingsideUsersApp ua')->where("app_id = $appId AND user_id = $uid");
		$q->execute();
	}

	/**
	 * Gets a User App object by id
	 *
	 * @param int $id
	 */
	public static function getUserAppById($id)
	{
		$q = Doctrine_Query::create();
		$q->from('RingsideUsersApp ua')->where("id = $id");
		$ua = $q->execute();
		
		if(count($ua) == 1)
		{
			return $ua[0];
		}
		
		return null;
	}

	/**
	 * Get the applications for a user
	 *
	 * @param string $uid
	 * @param string $aid
	 * @return array of user-app objectss
	 */
	public static function getUserAppByUserId($uid)
	{
		$q = Doctrine_Query::create();
		$q->from('RingsideUsersApp')->where("user_id = $uid");
		return $q->execute();
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $aid
	 * @param unknown_type $dbCon
	 * @return unknown
	 */
	public static function getUserAppByAppIdAndUserId($uid, $aid)
	{
		$q = Doctrine_Query::create();
		$q->from('RingsideUsersApp')->where("user_id = $uid AND app_id = $aid");
		return $q->execute();
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $api_key
	 * @param unknown_type $uid
	 * @param unknown_type $ext_perm
	 * @return unknown
	 */
	public static function checkUserHasPermission($api_key, $uid, $ext_perm, $network_id)
	{
		$q = Doctrine_Query::create();
		
		if($ext_perm == "status_update")
		{
			$q->select('allows_status_update');
		}else if($ext_perm == "photo_upload")
		{
			$q->select('allows_photo_upload');
		}else if($ext_perm == "create_listing")
		{
			$q->select('allows_create_listing');
		}else
		{
			throw new OpenFBAPIException("No such permission.", FB_ERROR_CODE_PARAMETER_MISSING);
		}
		
		$aid = Api_Dao_App::getAppIdByApiKey($api_key, $network_id);
		
		$q->from('RingsideUsersApp ua');				
		$q->where("ua.app_id=$aid AND ua.user_id = $uid");
		
		$perm = $q->execute();
		
		if(count($perm) > 0)
		{
			$ret = 0;
			if($ext_perm == "status_update")
			{
				$ret = $perm[0]->allows_status_update;
			}else if($ext_perm == "photo_upload")
			{
				$ret = $perm[0]->allows_photo_upload;
			}else if($ext_perm == "create_listing")
			{
				$ret = $perm[0]->allows_create_listing;
			}
			
			if(intval($ret) > 0)
			{
				return true;
			}
		}
		
		return false;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $appid
	 * @return unknown
	 */
	public static function getUsersAppKeys($uid, $appid)
	{
		$q = Doctrine_Query::create();
		$q->select('network_id, api_key, secret')->from('RingsideAppKey')->where("app_id=?", array($appid));
		return $q->execute();
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $appId
	 * @param unknown_type $nid
	 * @return unknown
	 */
	public static function isUsersAppKeys($app_id, $network_id)
	{
		$q = Doctrine_Query::create();
		$q->select('count(app_id) as app_count')->from('RingsideAppKey')->where("app_id=$app_id AND network_id='$network_id'");
		$count = $q->execute();
		
		if($count[0]['app_count'] > 0)
		{
			return true;
		}
		
		return false;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $network_id
	 * @param unknown_type $user_id
	 * @param unknown_type $api_key
	 * @param unknown_type $secret
	 * @return unknown
	 */
	public static function updateUsersAppKeys($app_id, $network_id, $api_key, $secret)
	{
		$q = Doctrine_Query::create();
		$q->update('RingsideAppKey')->set('api_key', '?', $api_key)->set('secret', '?', $secret)->where("app_id=$app_id AND network_id='$network_id'");
		return $q->execute();
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $user_id
	 * @param unknown_type $network_id
	 * @param unknown_type $api_key
	 * @param unknown_type $secret
	 * @return unknown
	 */
	public static function createUsersAppKeys($app_id, $network_id, $api_key, $secret)
	{
		$appKey = new RingsideAppKey();
		$appKey->app_id = $app_id;
		$appKey->network_id = $network_id;
		$appKey->api_key = $api_key;
		$appKey->secret = $secret;
		$ret = $appKey->trySave();
		
		if($ret)
		{
			return $appKey->getIncremented();
		}
		
		return false;
	}
}
?>