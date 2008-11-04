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
require_once 'ringside/api/ServiceFactory.php';
require_once 'ringside/api/dao/Friends.php';
require_once 'ringside/api/dao/FriendInvitation.php';
require_once 'ringside/api/facebook/OpenFBConstants.php';

/* Required for email invitations */
require_once 'ringside/api/dao/User.php';
require_once 'ringside/api/bo/UserProfileService.php';

require_once 'Mail.php';

/**
 * @author mlugert@ringsidenetworks.com
 */
class Api_Bo_Friends
{
    const FRIEND_INVITATION_MAX_AGE = 86400 /* 60 * 60 * 24 or 24 hours */;

    /**
     * @param $pastTimestamp all friends added after this time are counted (defaults to all time)
     *                       you can use Api_Bo_Util::getPastTimestamp() to build this
     * @return int total number of friends in the system
     */
    public static function getTotalCountOfFriends($pastTimestamp = null)
    {
        $q = Doctrine_Query::create();
        $q->select('count(f.to_id) count')
        ->from('RingsideFriend f');
        if ($pastTimestamp != null)
        {
            $q->where('created > ?', $pastTimestamp);
        }
        $_results = $q->execute();
        return $_results[0]['count'];
    }

    /**
     * Searches friends of this User ID with the provided query.
     *
     * @param unknown_type $userId
     * @param unknown_type $query
     * @return unknown
     */
    public static function searchFriends($userId, $query)
    {
        $friends = Api_Dao_Friends::friendsSearch($userId, $query);
        if(null != $friends)
        {
            return $friends->toArray();
        }

        return null;
    }

    /**
     * Friends are Friends is used by other BOs and DAOs
     *
     * @param String or Array $uid1
     * @param String or Array $uid2
     * @return Array
     */
    public static function getFriendsAreFriends($uid1, $uid2)
    {
        if(! is_array($uid1))
        {
            $uid1 = explode(',', $uid1);
        }
        if(! is_array($uid2))
        {
            $uid2 = explode(',', $uid2);
        }

        if(count($uid1) != count($uid2))
        {
            throw new Exception("The uid1 and uid2 arrays are not the same length.", FB_ERROR_CODE_PARAMETER_MISSING);
        }

        $response = array();
        $response[FB_FRIENDS_FRIEND_INFO] = array();

        $numIds = count($uid1);

        for($i = 0; $i < $numIds; $i ++)
        {
            $areWeFriends = Api_Dao_Friends::friendCheck($uid1[$i], $uid2[$i]);
            $response[FB_FRIENDS_FRIEND_INFO][$i] = array(FB_FRIENDS_UID1 => $uid1[$i], FB_FRIENDS_UID2 => $uid2[$i], FB_FRIENDS_ARE_FRIENDS => ($areWeFriends ? '1' : '0'));
        }

        return $response;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $userId
     * @return unknown
     */
    public static function getFriends( $userId )
    {
        return Api_Dao_Friends::friendsGetFriends($userId);
    }

    /**
     * Checks to see if two users are friends, if they are returns true, otherwise false
     *
     * @param int $loggedInUserId
     * @param int $uid
     * @return boolean
     */
    public static function checkFriends($loggedInUserId, $uid)
    {
        // Indeed, you are friends with yourself
        if($uid == $loggedInUserId)
        {
            return true;
        }

        if(($uid != null))
        {
            $faf = Api_Bo_Friends::getFriendsAreFriends($loggedInUserId, $uid);
            if($faf['friend_info'][0]['are_friends'] == 1)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $userId
     * @param unknown_type $apiKey
     * @return unknown
     */
    public static function getAppUsers($networkId, $userId, $apiKey)
    {
        $uid_array = Api_Dao_Friends::friendsGetFriends($userId);

		if(!isset($uid_array)|| empty($uid_array))
		{
			return null;
		}

		$keyService = Api_ServiceFactory::create('KeyService');
		$ids = $keyService->getIds($apiKey);
		if (($ids == NULL) || ($ids === false)) {
			throw new Exception("No app id found corresponding to api key '$apiKey'");
		}
		$appId = $ids['entity_id'];
		
		$q = Doctrine_Query::create();

		$where = "au.app_id=$appId AND au.user_id IN (" . implode(',', $uid_array) . ')';
		$q->select('au.user_id');
		$q->from('RingsideUsersApp au');
		$q->where($where);			
			
		$ret = $q->execute();
		
        if(null === $ret)
        {
            return null;
        }

        return $ret->toArray();
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $uid
     * @param unknown_type $fuid
     */
    public static function createFriend($uid, $fuid)
    {
        Api_Dao_Friends::createFriend($uid, $fuid);
    }

    public static function inviteFriendsByEmail($uid, $domain, $rsvp, $emails)
    {
        if ( ! is_array($emails) )
        {
            $emails = array($emails);
        }
        
        // Always collect the garbage whenever interacting with the FriendInvitation table
        Api_Dao_FriendInvitation::deleteAllExpired();
        $inv = Api_Dao_FriendInvitation::createInvitation($uid, self::FRIEND_INVITATION_MAX_AGE);

        // Make sure all the email addresses are well-formed and trimmed
        $fixed_emails = array();
        foreach ( $emails as $email )
        {
            $email = trim($email);
            if ( strchr($email, '@') )
            {
                $fixed_emails[] = trim($email);
            }
        }

        // Get user info for the sending user
        $ps = Api_Bo_UserProfileService::create();
        $uid_info = $ps->getProfiles(array($uid), array($domain), array('first_name', 'last_name'));
        $uid_info = $uid_info[0];
        // TODO: Replace with a queue
        // Send a bunch of emails
        $subject = 'Friend request from '.$uid_info['first_name'].' '.$uid_info['last_name'];
        // Just making sure...
        str_replace("\r\n", ' ', $subject);
        $base_message = "{$uid_info['first_name']} {$uid_info['last_name']} has invited you to be their friend. Click the link below to connect with them.\n\n";

        // TODO: Migrate to SMTPConfig.php?
        @include('LocalSettings.php');
        
        $headers = array(
            'From'	=> str_replace("\r\n", ' ', '"'.$uid_info['first_name'].' '.$uid_info['last_name']."\" <$smtp_default_sender>"),
            'Subject' => str_replace("\r\n", ' ', 'Friend request from '.$uid_info['first_name'].' '.$uid_info['last_name'])
        );
        
        $smtp = Mail::factory('smtp', array('host' => $smtp_server, 'auth' => $smtp_use_auth, 'username' => $smtp_username, 'password' => $smtp_password));
        
//        $rsvp = 'http://' . $_SERVER['HTTP_HOST'] . RingsideWebConfig::$webRoot . '/friends.php?view=view_invites';
        foreach ( $fixed_emails as $email )
        {
            // Append the inv=<inv_key> string to the end of the RSVP URL
            $my_rsvp = $rsvp.((strpos($rsvp, '?') !== false)?'&':'?').'inv='.$inv;
            $message = $base_message . '<'.$my_rsvp.'>';
//            error_log("Sending to $email: $message");
            $headers['To'] = $email;
            $mail = $smtp->send($email, $headers, $message);
            if ( PEAR::isError($mail) )
            {
                error_log("Failed to send email to $email: ".$mail->getMessage());
                return false;
            } else {
                error_log("Message sent");
            }
        }
        return $inv;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $uid
     * @param unknown_type $fuid
     * @param unknown_type $status
     * @param unknown_type $access
     */
    public static function acceptInvite($uid, $fuid, $status, $access)
    {
        Api_Dao_Friends::acceptInvite($uid, $fuid, $status, $access);
    }
}
?>