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
require_once('ringside/api/config/RingsideApiConfig.php');

require_once('ringside/api/dao/records/RingsideFriend.php');
require_once('ringside/api/dao/records/RingsideApp.php');
require_once('ringside/api/dao/records/RingsideUsersApp.php');
require_once('ringside/api/dao/records/RingsideUsersProfileBasic.php');


/**
 * Represents a row in the OpenFB users_profile_friends table.
 * "Basically, it's: ringside/<package>/<base class name><layer>.php; class name is <package><base><layer>"
 */
class Api_Dao_Friends
{
	const RS_FBDB_FRIENDS_DECLINED = 0;
	const RS_FBDB_FRIENDS_INVITE = 1;
	const RS_FBDB_FRIENDS_FRIENDS = 2;
	const RS_FBDB_FRIENDS_REMOVED = 3;

	const RS_FBDB_FRIENDS_ACCESS_DISABLED = 0;
	const RS_FBDB_FRIENDS_ACCESS_ALL = 1;
	const RS_FBDB_FRIENDS_ACCESS_MINIMAL = 2;

	/**
	 * Checks to see if there is any active record proving these two folks are friends.
	 *
	 * @param integer $uid1
	 * @param integer $uid2
	 * @param datbase $dbCon
	 * @return 0/1 value representing friendship.
	 */
	public static function friendCheck( $uid1, $uid2 )
	{
		if ($uid1 == $uid2) return true;
		$q = Doctrine_Query::create();

		$q->select('COUNT(from_id) as uid_count')
		->from('RingsideFriend f')
		->where("((from_id = $uid1 AND to_id = $uid2) OR ( from_id = $uid2 AND to_id = $uid1 )) AND status = ".self::RS_FBDB_FRIENDS_FRIENDS);

		$friends = $q->execute();
		if($friends[0]['uid_count'] > 0)
		{
			return true;
		}
		return false;
	}

	/**
	 * Return a users friends.
	 *
	 * @param integer $uid user id to get friends of.
	 * @param databse $dbCon db connection to use
	 * @return array of users ids which are valid friends
	 * @throws Exception on DB error.
	 */
	public static function friendsGetFriends( $uid )
	{
		$q = Doctrine_Query::create();
		$q2 = Doctrine_Query::create();

		$q->select('from_id')->from('RingsideFriend f')->where("to_id = $uid  AND status = ".self::RS_FBDB_FRIENDS_FRIENDS);
		$q2->select('to_id')->from('RingsideFriend f')->where("from_id =  $uid AND status = ".self::RS_FBDB_FRIENDS_FRIENDS);
		
		$friends = $q->execute();
		$friends2 = $q2->execute();
		
		$response = array();
		foreach($friends as $friend)
		{
			$response[] = $friend->from_id;
		}
		foreach($friends2 as $friend)
		{
			$response[] = $friend->to_id;
		}

		return $response;
	}

	/**
	 * Query for the users friends
	 *
	 * @param string $uid
	 * @param string $q
	 * @param resource $dbCon
	 * @return Array
	 */
	public static function friendsSearch( $uid, $q )
	{
		$uid_array = self::friendsGetFriends($uid);

		if(!isset($uid_array)|| empty($uid_array))
		{
			return null;
		}

		$uids = implode(",", $uid_array);
		
		$where = "user_id in ($uids)";

		if(isset($q) && !empty($q) && strlen($q) > 0)
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

			$where .= " AND first_name $first_like OR last_name = '$last'";
		}
		$q = Doctrine_Query::create();

		$q->select('user_id, first_name, last_name, pic_small_url')
			->from('RingsideUsersProfileBasic upb')
			->where($where);

		return $q->execute();
	}

	/**
	 * Creates a friend
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $fuid
	 * @param unknown_type $dbCon
	 */
	public static function createFriend($uid, $fuid)
	{
		$q = Doctrine_Query::create();

		$q->select('status')
			->from('RingsideFriend f')
			->where("from_id=$uid AND to_id=$fuid");
			
		$f = $q->execute();

		if($f->count() == 0)
		{
			$friend = new RingsideFriend();
			$friend->from_id = $uid;
			$friend->to_id = $fuid;
			$friend->access = self::RS_FBDB_FRIENDS_ACCESS_DISABLED;
			$friend->status = self::RS_FBDB_FRIENDS_INVITE;
			$friend->save();
		}else
		{
			$status = $f[0]['status'];

			if($status == self::RS_FBDB_FRIENDS_REMOVED)
			{
				$f[0]['status'] = self::RS_FBDB_FRIENDS_INVITE;
				$f->save();
			}
		}
	}

	/**
	 * Accept a friend invite
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $fuid
	 * @param unknown_type $access
	 * @param unknown_type $dbCon
	 */
	public static function acceptInvite($uid, $fuid, $status, $access)
	{
		$q = Doctrine_Query::create();

		$q->from('RingsideFriend f')
			->where("from_id=$fuid AND to_id=$uid");
			
		$f = $q->execute();
		
		// If there is no outstanding connection, create one; this enables email invites
		if ( $f == null || sizeof($f) == 0 )
		{
		    $f = new RingsideFriend();
		    $f->from_id = $fuid;
		    $f->to_id = $uid;
		    $f->status = $status;
		    $f->access = $access;
		} else {
    		$f[0]['status'] = $status;
    		$f[0]['access'] = $access;
		}
		$f->save();
	}
}
?>
