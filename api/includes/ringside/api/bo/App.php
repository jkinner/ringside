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
require_once 'ringside/api/bo/Util.php';
require_once 'ringside/api/dao/AppPrefs.php';
require_once 'ringside/api/dao/App.php';
require_once 'ringside/api/dao/UsersApp.php';
require_once 'ringside/api/dao/UserAppSession.php';
require_once 'ringside/api/dao/records/RingsideUsersApp.php';

/**
 * @author mlugert@ringsidenetworks.com
 * Contains logic for
 * App
 * AppPrefs
 * UsersApp
 */
class Api_Bo_App
{
    /**
     * @param $pastTimestamp all apps added after this time are counted (defaults to all time)
     *                       you can use Api_Bo_Util::getPastTimestamp() to build this
     * @return int total number of apps in the system 
     */
    public static function getTotalCountOfApps($pastTimestamp = null)
    {
        $q = Doctrine_Query::create();
        $q->select('count(a.id) count')
          ->from('RingsideApp a');
        if ($pastTimestamp != null)
        {
            $q->where('created > ?', $pastTimestamp);
        }
        $_results = $q->execute();
        return $_results[0]['count'];
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $uid
     * @param unknown_type $aid
     * @return unknown
     */
    public static function getUserAppSession($uid, $aid)
    {
        return Api_Dao_UserAppSession::getUserAppSession($uid, $aid)->toArray();
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $aid
     * @param unknown_type $uid
     * @param unknown_type $infinite
     * @param unknown_type $key
     * @return unknown
     */
    public static function setUserAppSession($aid, $uid, $infinite = 0, $key)
    {
        if(Api_Dao_UserAppSession::isUserAppSession($uid, $aid))
        {
            return Api_Dao_UserAppSession::updateUserAppSession($aid, $uid, $infinite, $key);
        }else
        {
            return Api_Dao_UserAppSession::createUserAppSession($aid, $uid, $infinite, $key);
        }
    
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $aid
     * @param unknown_type $uid
     */
    public static function deleteUserAppSession($aid, $uid)
    {
        return Api_Dao_UserAppSession::deleteUserAppSession($aid, $uid);
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $userId
     * @param unknown_type $aid
     * @return unknown
     */
    public static function getUserApp($userId, $aid = null)
    {
        if(self::isEmpty($aid))
        {
            return Api_Dao_UsersApp::getUserAppByUserId($userId, $aid)->toArray();
        }else
        {
            return Api_Dao_UsersApp::getUserAppByAppIdAndUserId($userId, $aid)->toArray();
        }
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $api_key
     * @param unknown_type $userId
     * @param unknown_type $ext_perm
     * @return unknown
     */
    public static function checkUserHasPermission($api_key, $userId, $ext_perm, $networkId)
    {
        return Api_Dao_UsersApp::checkUserHasPermission($api_key, $userId, $ext_perm, $networkId);
    }
    
    /**
     * Enter description here...
     *
     * @param unknown_type $appId
     * @param unknown_type $userId
     * @return unknown
     */
    public static function isUsersApp($appId, $userId)
    {
        return Api_Dao_UsersApp::isUsersApp($appId, $userId);
    }
    
    /**
     * Enter description here...
     *
     * @param unknown_type $userId
     * @param unknown_type $appId
     * @return unknown
     */
    public static function removeUsersApp($userId, $appId)
    {
        return Api_Dao_UsersApp::deleteUserAppByAppIdAndUid($appId, $userId);
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
     * @return unknown
     */
    public static function setUsersApp($app_id, $user_id, $allows_status_update = 0, $allows_create_listing = 0, $allows_photo_upload = 0, $auth_information = 0, $auth_profile = 0, $auth_leftnav = 0, $auth_newsfeeds = 0, $profile_col = 'wide', $profile_order = 0)
    {
        $response = Api_Dao_App::getApplicationInfoById($app_id);
        $a = $response[0];
        $fbml = $a['default_fbml'];
        
        if(Api_Dao_UsersApp::isUsersApp($app_id, $user_id))
        {
            return Api_Dao_UsersApp::updateUsersApp($app_id, $user_id, $allows_status_update, $allows_create_listing, $allows_photo_upload, $auth_information, $auth_profile, $auth_leftnav, $auth_newsfeeds, $profile_col, $profile_order, $fbml);
        }else
        {
            return Api_Dao_UsersApp::createUsersApp($app_id, $user_id, $allows_status_update, $allows_create_listing, $allows_photo_upload, $auth_information, $auth_profile, $auth_leftnav, $auth_newsfeeds, $profile_col, $profile_order, $fbml);
        }
    }
        
    /**
     * Enter description here...
     *
     * @param unknown_type $apiKey
     * @param unknown_type $props
     * @return unknown
     */
    public static function updateAppProperties($appId, $props, $networkId)
    {
        Api_Dao_App::updateAppProperties($appId, $props, $networkId);
    }

    /**
     * Returns the list of applications that belong to this user
     *
     * @param int $userId
     * @return array
     */
    public static function getApplicationListByUserId($userId)
    {
        $apps = Api_Dao_App::getApplicationListByUid($userId);
        return self::convertCollectionAsListToArray($apps, true);
    }

    /**
     * Returns all applications registered in this instance of Ringside
     *
     * @return unknown
     */
    public static function getAllApplications()
    {
        $apps = Api_Dao_App::getApplicationList();
        return self::convertCollectionToArray($apps, false);
    }

    /**
     * Returns all applications registered in this instance of Ringside
     *
     * @return unknown
     */
    public static function getAllApplicationsAndKeys()
    {
        $apps = Api_Dao_App::getFullApplicationList();
       return self::convertCollectionToArray($apps, true);
    }

    private static function _checkKeyPair($api_key, $secret)
    {
        $keysFailed = array();
        if ( empty($api_key) )
        {
            $keysFailed[] = 'API key';
        }
        if ( empty($secret) )
        {
            $keysFailed[] = 'secret';
        }
        
        if ( sizeof($keysFailed) > 0 )
        {
            throw new Exception(1, 'Could not accept key pair: '.join(' and ', $keysFailed).' '.(sizeof($keysFailed) > 1?'are':'is').' invalid');
        }
    }

    private static function _addNetworkKeysToApplicationInfo(&$appInfo, $keys)
    {
        // This function expects $appInfo to already contain the "rest" of the results. There should also be keys in the keychain.
        if ( !empty($appInfo) && !empty($keys) )
        {
            $appInfo['api_key'] = $keys->api_key;
            $appInfo['secret_key'] = $keys->secret;
        }
        return $appInfo;
    }
            
    /**
     * Enter description here...
     *
     * @param unknown_type $userId
     * @param unknown_type $appId
     * @return unknown
     */
    public static function checkUserOwnsApp($userId, $appId)
    {
        return Api_Dao_App::checkUserOwnsApp($userId, $appId);
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $apiKey
     * @return unknown
     */
    public static function getAppIdByApiKey($apiKey, $networkId)
    {
        return Api_Dao_App::getAppIdByApiKey($apiKey, $networkId);
    }

    public static function getAppIdByCanvasName($canvasName)
    {
        return Api_Dao_App::getAppIdByCanvasName($canvasName);
    }
    
    /**
     * Enter description here...
     *
     * @param unknown_type $apiKey
     * @param unknown_type $secret
     * @return unknown
     */
    public static function getAppIdByApiKeyAndSecret($apiKey, $secret, $networkId)
    {
    
        return Api_Dao_App::getAppIdByApiKeyAndSecret($apiKey, $secret, $networkId);
    }
    
    /**
     * Enter description here...
     *
     * @param unknown_type $name
     * @return unknown
     */
    public static function getAppIdByName($name)
    {
        return Api_Dao_App::getAppIdByName($name);
    }
    /**
     * Gets application preferences for the given app id and user id
     *
     * @param unknown_type $appId
     * @param unknown_type $userId
     * @return unknown
     */
    public static function getApplicationPreferences($appId, $userId)
    {
        if(! isset($appId) || ! isset($userId))
        {
            throw new Exception("Parameter Missing!", 0);
        }
        $appPrefs = Api_Dao_AppPrefs::getAppPrefsByAppIdAndUserId($appId, $userId);
        return json_decode($appPrefs[0]->value, true);
    }

    /**
     * Checks to see if the user has App Prefs tied to this App.
     *
     * @param int $appId
     * @param int $userId
     * @return unknown
     */
    public static function hasAppPrefs($appId, $userId)
    {
        if(! isset($appId) || ! isset($userId))
        {
            throw new Exception("Parameter Missing!", 0);
        }
        $appPrefs = Api_Dao_AppPrefs::getAppPrefsByAppIdAndUserId($appId, $userId);
        
        if(count($appPrefs) > 0)
        {
            return true;
        }
        
        return false;
    }

    /**
     * Saves the provided App Pref
     *
     * @param unknown_type $appId
     * @param unknown_type $userId
     * @param unknown_type $prefs
     * @return unknown
     */
    public static function saveAppPrefs($appId, $userId, $prefs)
    {
        $hasPrefs = self::hasAppPrefs($appId, $userId);
        // New pref?
        $ret = 0;
        if($hasPrefs)
        {
            $ret = Api_Dao_AppPrefs::updateAppPrefs($appId, $userId, $prefs);
        }else
        {
            $ret = Api_Dao_AppPrefs::createAppPrefs($appId, $userId, $prefs);
        }
        
        if(false === $ret || 0 == $ret)
        {
            return false;
        }
        
        return true;
    }
    
    /**
     * Enter description here...
     *
     * @param unknown_type $userId
     * @param unknown_type $appId
     * @return unknown
     */
    public static function getFBML($userId, $appId)
    {
        $userApp = Api_Dao_UsersApp::getUserAppByAppIdAndUserId($userId, $appId);
        if(count($userApp) > 0)
        {
            return $userApp[0]->fbml;
        }
        
        return null;
    }
    
    /**
     * Enter description here...
     *
     * @param unknown_type $userId
     * @param unknown_type $appId
     * @param unknown_type $fbml
     */
    public static function setFBML($userId, $appId, $fbml)
    {
        $userApps = Api_Dao_UsersApp::getUserAppByAppIdAndUserId($userId, $appId);
        if(count($userApps) > 0)
        {
            $userApp = $userApps[0];
            $userApp->fbml = $fbml;
            $ret = $userApp->trySave();
            if($ret)
            {
                return true;
            }
            return false;
        }else
        {
            return Api_Dao_UsersApp::createUsersApp($appId, $userId, 0, 0, 0, 0, 0, 0, 0, 'wide', 0, $fbml);
        }
        
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $var
     * @return unknown
     *
     * FIXME: make part of parent class
     */
    private static function isEmpty($var)
    {
        if(! isset($var) || is_null($var))
        {
            return true;
        }
        
        if(is_string($var) && strlen(rtrim($var)) == 0)
        {
            return true;
        }
        
        if(is_array($var) && count($var) == 0)
        {
            return true;
        }
        
        return false;
    }

    // TODO: inline calls to this private method
    private static function convertCollectionToArray($collection, $deep = false)
    {
        return Api_Bo_Util::convertCollectionToArray($collection, $deep);
    }

    // TODO: inline calls to this private method
    private static function convertCollectionAsListToArray($collection, $deep = false)
    {
        return Api_Bo_Util::convertCollectionAsListToArray($collection, $deep);
    }
}

?>
