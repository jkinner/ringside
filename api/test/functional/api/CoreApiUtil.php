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
require_once 'ringside/api/bo/Favorites.php';
require_once 'ringside/api/dao/Favorites.php';

/**
 * @author mlugert@ringsidenetworks.com
 */
class CoreApiUtil
{
	/**
	 * Helper method to create a list
	 *
	 * @param unknown_type $name
	 * @param unknown_type $app_id
	 * @param unknown_type $uid
	 * @return unknown
	 */
	public static function createList($name, $app_id, $uid)
	{
		return Api_Bo_Favorites::createList($name, $app_id, $uid);
	}

	/**
	 * Helper method to check if something is a favorite of ONE of the users passed in
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $item_id
	 * @param unknown_type $alid
	 * @param unknown_type $lid
	 * @param unknown_type $uids
	 * @return unknown
	 */
	public static function isFavorite($app_id, $item_id, $alid, $lid, $uids)
	{
		$result = Api_Bo_Favorites::getUsers($app_id, $item_id, $alid, $lid, $uids);
		if(count($result) > 0)
		{
			return true;
		}
		return false;
	}

	/**
	 * Helper method to create a favorite
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $uid
	 * @param unknown_type $item_id
	 * @param unknown_type $alid
	 * @param unknown_type $lid
	 */
	public static function createFavorite($app_id, $uid, $item_id, $alid, $lid)
	{
		return Api_Bo_Favorites::setFavorite($app_id, $uid, $item_id, $alid, $lid, '');
	}
}
?>
