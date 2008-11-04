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
require_once 'ringside/api/bo/Friends.php';
require_once 'ringside/api/dao/Events.php';

/**
 * @author mlugert@ringsidenetworks.com
 */
class Api_Bo_Events
{
	const RS_FBDB_RSVP_STR_ATTENDING = 'attending';
	const RS_FBDB_RSVP_STR_DECLINED = 'declined';
	const RS_FBDB_RSVP_STR_NOT_REPLIED = 'not_replied';
	const RS_FBDB_RSVP_STR_UNSURE = 'unsure';
	
	const RS_FBDB_ACCESS_OPEN = '1';
	const RS_FBDB_ACCESS_CLOSED = '2';
	const RS_FBDB_ACCESS_PRIVATE = '3';
	
	const RS_FBDB_VIDEO_DISABLED = '0';
	const RS_FBDB_VIDEO_ADMINS = '1';
	const RS_FBDB_VIDEO_MEMBERS = '2';
	
	const RS_FBDB_PHOTOS_DISABLED = '0';
	const RS_FBDB_PHOTOS_ADMINS = '1';
	const RS_FBDB_PHOTOS_MEMBERS = '2';
	
	const RS_FBDB_POSTED_DISABLED = '0';
	const RS_FBDB_POSTED_ADMINS = '1';
	const RS_FBDB_POSTED_MEMBERS = '2';
	
	const RS_FBDB_RSVP_ATTENDING = 1;
	const RS_FBDB_RSVP_UNSURE = 2;
	const RS_FBDB_RSVP_DECLINED = 3;
	const RS_FBDB_RSVP_NOT_REPLIED = 4;

	/**
	 * Gets the events based on the parameters passed in
	 *
	 * @param int $loggedInUserId
	 * @param int $uid
	 * @param date_time $start_time
	 * @param date_time $end_time
	 * @param array $eids
	 * @param string $rsvp
	 * @return array
	 */
	public static function getEvents($loggedInUserId, $uid = null, $start_time = null, $end_time = null, $eids = null, $rsvp = null)
	{
		if($uid == null && $eids != null)
		{
			$members = Api_Dao_Events::getEventMembersAsArray($loggedInUserId, null, null);
			return Api_Dao_Events::getEventsByEidsAndMembers($eids, $members, $start_time, $end_time)->toArray();
		}else
		{
			$privacy = false;
			if($uid !== null && $uid != $loggedInUserId)
			{
				$privacy = self::RS_FBDB_ACCESS_PRIVATE;
			}
			
			$uid = ($uid == null) ? $loggedInUserId : $uid;
			// Make sure the logged in user and the uid passed are friends
			$areFriends = Api_Bo_Friends::checkFriends($loggedInUserId, $uid);
			
			if(! $areFriends)
			{
				return array();
			}
			$members = Api_Dao_Events::getEventMembersAsArray($uid, $rsvp, $eids);
			return Api_Dao_Events::getEventsByMembers($members, $start_time, $end_time, $privacy)->toArray();
		}
	
	}

	/**
	 * Returns members for the given uid and eid
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $eid
	 * @return unknown
	 */
	public static function getMembers($uid, $eid)
	{
		return Api_Dao_Events::getMembersByUserIdAndEventId($uid, $eid)->toArray();
	}
	
	/**
	 * Returns the status code
	 *
	 * @param String $status
	 * @return Integer
	 */
	public static function getRsvpStatusCode($status)
	{
		switch($status)
		{
			case self::RS_FBDB_RSVP_STR_ATTENDING:
				return self::RS_FBDB_RSVP_ATTENDING;
			case self::RS_FBDB_RSVP_STR_UNSURE:
				return self::RS_FBDB_RSVP_UNSURE;
			case self::RS_FBDB_RSVP_STR_DECLINED:
				return self::RS_FBDB_RSVP_DECLINED;
			case self::RS_FBDB_RSVP_STR_NOT_REPLIED:
				return self::RS_FBDB_RSVP_NOT_REPLIED;
			default:
				return - 1;
		}
	}

	/**
	 * Returns the Status String
	 *
	 * @param Integer $status
	 * @return String
	 */
	public static function getRsvpStatusString($status)
	{
		switch($status)
		{
			case self::RS_FBDB_RSVP_ATTENDING:
				return self::RS_FBDB_RSVP_STR_ATTENDING;
			case self::RS_FBDB_RSVP_UNSURE:
				return self::RS_FBDB_RSVP_STR_UNSURE;
			case self::RS_FBDB_RSVP_DECLINED:
				return self::RS_FBDB_RSVP_STR_DECLINED;
			case self::RS_FBDB_RSVP_NOT_REPLIED:
				return self::RS_FBDB_RSVP_STR_NOT_REPLIED;
			default:
				return null;
		}
	}
}

?>
