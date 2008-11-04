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
require_once ('ringside/api/dao/records/RingsideGroupsMember.php');
require_once ('ringside/api/dao/records/RingsideGroup.php');

/**
 * Enter description here...
 *
 */
class Api_Dao_Group
{
	const RS_FBDB_GROUP_ACCESS_OPEN = '1';
	const RS_FBDB_GROUP_ACCESS_CLOSED = '2';
	const RS_FBDB_GROUP_ACCESS_PRIVATE = '3';
	
	const RS_FBDB_GROUP_VIDEO_DISABLED = '0';
	const RS_FBDB_GROUP_VIDEO_ADMINS = '1';
	const RS_FBDB_GROUP_VIDEO_MEMBERS = '2';
	
	const RS_FBDB_GROUP_PHOTOS_DISABLED = '0';
	const RS_FBDB_GROUP_PHOTOS_ADMINS = '1';
	const RS_FBDB_GROUP_PHOTOS_MEMBERS = '2';
	
	const RS_FBDB_GROUP_POSTED_DISABLED = '0';
	const RS_FBDB_GROUP_POSTED_ADMINS = '1';
	const RS_FBDB_GROUP_POSTED_MEMBERS = '2';

	/**
	 * Returns all the groups for this User ID
	 *
	 * @return unknown
	 */
	public static function getGroupsByUserId($userId)
	{
		$q = new Doctrine_RawSql();
		$q->select('{g.*}')->from('groups g INNER JOIN groups_member gm ON g.gid=gm.gid')->where("uid=$userId")->addComponent('g', 'RingsideGroup g')->addComponent('gm', 'g.RingsideGroupsMember gm');
		$groups = $q->execute();
		
		return $groups;
	}

	/**
	 * Returns all the groups for this user id that are in the array of groups passed in.
	 *
	 * @param unknown_type $userId
	 * @param unknown_type $uid
	 * @param unknown_type $gids
	 * @return unknown
	 */
	public static function getGroupsByUserIdAndGroupIds($userId, $gids)
	{
		$inPart = implode(",", $gids);
		$q = new Doctrine_RawSql();
		$q->select('{g.*}')->from('groups g INNER JOIN groups_member gm ON g.gid=gm.gid')->where("uid=$userId AND g.gid IN ($inPart)")->addComponent('g', 'RingsideGroup g')->addComponent('gm', 'g.RingsideGroupsMember gm');
		$groups = $q->execute();
		
		return $groups;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $gid
	 * @return unknown
	 */
	public static function getGroupMembers($gid)
	{
		$q = Doctrine_Query::create();
		$q->from('RingsideGroupsMember gm')->where("gid = $gid");
		
		return $q->execute();
	}

}
?>
