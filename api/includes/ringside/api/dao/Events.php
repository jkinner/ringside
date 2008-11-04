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
require_once 'ringside/api/bo/Events.php';
require_once 'ringside/api/config/RingsideApiConfig.php';
require_once 'ringside/api/dao/records/RingsideEvent.php';
require_once 'ringside/api/dao/records/RingsideEventsMember.php';


/**
 * @author mlugert@ringsidenetworks.com
 */
class Api_Dao_Events
{
	/**
	 * Used to get event members, used by getEvents* calls
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $rsvp
	 * @param unknown_type $eids
	 * @return unknown
	 */
	public static function getEventMembersAsArray($uid, $rsvp = null, $eids = null)
	{
		$q = Doctrine_Query::create();
		$q->select('e.eid')->from('RingsideEventsMember e');
		$select_where = "e.uid = {$uid}";
		if($rsvp != null)
		{
			$status = Api_Bo_Events::getRsvpStatusCode($rsvp);
			$select_where .= " and rsvp = $status";
		}
		
		if($eids != null)
		{
			$eids = implode(",", $eids);
			$select_where .= " and eid in ($eids)";
		}
		
		$q->where($select_where);
		$members = $q->execute();
		
		$eid_array = array();
		foreach($members as $member)
		{
			$eid_array[] = $member->eid;
		}
		return $eid_array;
	}

	public static function getEventIdsByUserIdAndEventIdAsString($uid, $eid)
	{
		$q2 = Doctrine_Query::create();
		$q2->select('m.eid')->from('RingsideEventsMember m')->where("m.uid = {$uid} and m.eid = {$eid}");
		$members = $q2->execute();
		
		$a = array();
		foreach($members as $member)
		{
			$a[] = $member->eid;
		}
		
		$s = implode(',', $a);
		if(empty($s))
			return 0;
		
		return $s;
	}

	public static function getEventIdsByEidAndAccessOpenAsString($eid)
	{
		$q3 = Doctrine_Query::create();
		$q3->select('e.eid')->from('RingsideEvent e')->where("e.eid = $eid and e.access =" . Api_Bo_Events::RS_FBDB_ACCESS_OPEN);
		$events = $q3->execute();
		
		$a = array();
		foreach($events as $event)
		{
			$a[] = $event->eid;
		}
		
		$s = implode(',', $a);
		if(empty($s))
			return 0;
		
		return $s;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $start_time
	 * @param unknown_type $end_time
	 * @param unknown_type $user_id
	 * @param unknown_type $eids
	 * @param unknown_type $uid
	 * @param unknown_type $rsvp
	 * @return unknown
	 */
	public static function getEventsByMembers($members, $start_time = null, $end_time = null, $privacy = false)
	{
		$in = implode(',', $members);
		if(strlen($in) == 0)
			$in = 0;
		
		$where = "eid IN ($in)";
		
		// What about privacy?
		if($privacy !== false)
		{
			$where .= ' AND access <> ' . Api_Bo_Events::RS_FBDB_ACCESS_PRIVATE;
		}
		
		if($start_time != null)
		{
			$where .= " AND end_time > {$start_time}";
		}
		
		if($end_time != null)
		{
			$where .= " AND start_time < {$end_time}";
		}
		
		$q = Doctrine_Query::create();
		$q->from('RingsideEvent e')->where($where);
		return $q->execute();
	
	}

	public static function getEventsByEidsAndMembers($eids, $members, $start_time = null, $end_time = null)
	{
		$in = implode(',', $members);
		$eids = implode(",", $eids);
		
		if(strlen($eids) == 0)
			$eids = 0;
		
		if(strlen($in) == 0)
			$in = 0;
		
		$where = "eid IN ($eids) and (access <> " . Api_Bo_Events::RS_FBDB_ACCESS_PRIVATE . " or eid IN ($in))";
		
		if($start_time != null)
		{
			$where .= " AND end_time > {$start_time}";
		}
		
		if($end_time != null)
		{
			$where .= " AND start_time < {$end_time}";
		}
		
		$q = Doctrine_Query::create();
		$q->from('RingsideEvent e')->where($where);
		return $q->execute();
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $eid
	 * @return unknown
	 */
	public static function getMembersByUserIdAndEventId($uid, $eid)
	{
		$ifClosed = Api_Dao_Events::getEventIdsByUserIdAndEventIdAsString($uid, $eid);
		$ifOpen = Api_Dao_Events::getEventIdsByEidAndAccessOpenAsString($eid);
		
		$q = Doctrine_Query::create();
		$q->select('e.uid, e.rsvp')->from('RingsideEventsMember e')->where("e.eid = $eid AND (e.eid in ($ifOpen) or e.eid in ($ifClosed))");
		return $q->execute();
	}
}
?>
