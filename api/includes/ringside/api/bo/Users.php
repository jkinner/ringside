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
require_once 'ringside/api/dao/User.php';
require_once 'ringside/api/dao/UsersApp.php';
require_once 'ringside/api/dao/UserProfile.php';
require_once 'ringside/api/facebook/OpenFBConstants.php';

require_once 'ringside/api/dao/records/RingsideUsersProfileBasic.php';

/**
 * @author mlugert@ringsidenetworks.com
 */
class Api_Bo_Users
{
    /**
     * @param $pastTimestamp all users added after this time are counted (default is all time)
     *                       you can use Api_Bo_Util::getPastTimestamp() to build this
     * @return int total number of users that have profiles in the system 
     */
    public static function getTotalCountOfUserProfiles($pastTimestamp = null)
    {
        $q = Doctrine_Query::create();
        $q->select('count(upb.user_id) count')
          ->from('RingsideUsersProfileBasic upb');
        if ($pastTimestamp != null)
        {
            $q->where('created > ?', $pastTimestamp);
        }
        $_results = $q->execute();
        return $_results[0]['count'];
    }

    /**
     * @param $pastTimestamp all application registrations registered after this time are counted (default is all time)
     *                       you can use Api_Bo_Util::getPastTimestamp() to build this
     * @return int total number of application registrations for all users. This
     *             is NOT the total number of deployed apps - the returned value will be much
     *             higher because its the number of registrations that exist for all users for all apps 
     */
    public static function getTotalCountOfUserAppRegistrations($pastTimestamp = null)
    {
        $q = Doctrine_Query::create();
        $q->select('count(ua.id) count')
          ->from('RingsideUsersApp ua');
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
     * @param unknown_type $appId
     * @param unknown_type $userIds
     * @return unknown
     */
    public static function getUsersInfo($appId, $userIds)
    {
        return Api_Dao_User::getInfoByUids($appId, $userIds)->toArray();
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $userId
     * @param unknown_type $appId
     * @param unknown_type $api_key
     * @param unknown_type $status
     * @param unknown_type $cleared
     * @return unknown
     */
    public static function setStatus($userId, $appId, $api_key, $networkId, $status, $cleared)
    {
        $hasPermissionResult = Api_Dao_UsersApp::checkUserHasPermission($api_key, $userId, 'status_update', $networkId);
        if($hasPermissionResult != true)
        {
            throw new Exception(FB_ERROR_MSG_REQUIRES_PERMISSION, FB_ERROR_CODE_REQUIRES_PERMISSION);
        }
        
        $ret = false;
        if(Api_Dao_User::isStatus($userId, $appId))
        {
            $ret = Api_Dao_User::updateStatus($userId, $appId, $status);
        }else
        {
            $ret = Api_Dao_User::createStatus($userId, $appId, $status);
        }
        
        Api_Dao_User::createStatusHistory($userId, $appId, $status, $cleared);
        
        return $ret;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $q
     */
    public static function searchUsers($q)
    {
        if(isset($q) && ! empty($q) && strlen($q) > 0)
        {
            $a = explode(' ', $q);
            $first_like = '';
            $first = '';
            $last = '';
            if(isset($a[0]))
            {
                $first = $a[0];
                $first_like = "like '%$first%'";
            }
            
            if(isset($a[1]))
            {
                $last = $a[1];
            }else
            {
                $last = $first;
            }
            Api_Dao_User::searchUsers($last, $first_like)->toArray();
        }
        
        throw new Exception('No query provided!');
    }
    
    /**
     * Enter description here...
     *
     * @param unknown_type $userId
     * @return unknown
     */
    public static function isUserAdmin($userId)
    {
        return Api_Dao_User::isUserAdmin($userId);
    }
}
?>