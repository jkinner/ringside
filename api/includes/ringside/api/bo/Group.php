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
require_once 'ringside/api/dao/Group.php';

/**
 * @author mlugert@ringsidenetworks.com
 */
class Api_Bo_Group
{

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $userId
	 * @param unknown_type $uid
	 * @param unknown_type $gid
	 * @return unknown
	 */
	public static function getGroups($loggedInUserId, $uid = null, $gids = null)
	{
		$uid = (null == $uid)?$loggedInUserId:$uid;
		
		$areFriends = Api_Bo_Friends::checkFriends($loggedInUserId, $uid);
		
		if($areFriends)
		{
			if(null == $gids || empty($gids))
			{
				return Api_Dao_Group::getGroupsByUserId($uid)->toArray();
			}
			
			return Api_Dao_Group::getGroupsByUserIdAndGroupIds($uid, $gids)->toArray();
		}
		
		return array();
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $gid
	 * @return unknown
	 */
	public static function getGroupMembers($gid)
	{
		return Api_Dao_Group::getGroupMembers($gid)->toArray();
	}
}

?>
