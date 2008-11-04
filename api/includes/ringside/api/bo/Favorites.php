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
require_once ("ringside/api/dao/Favorites.php");
/**
 * @author mlugert@ringsidenetworks.com
 */
class Api_Bo_Favorites
{

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $item_id
	 * @param unknown_type $alid
	 * @param unknown_type $lid
	 * @param unknown_type $uids
	 * @return unknown
	 */
	public static function getUsers($app_id, $item_id, $alid, $lid, $uids)
	{
		self::checkLists($alid, $lid);
		if(! self::isEmpty($alid))
		{
			return Api_Dao_Favorites::getUsersByAlid($app_id, $item_id, $alid, $uids)->toArray();
		}else
		{
			return Api_Dao_Favorites::getUsersByLid($app_id, $item_id, $lid, $uids)->toArray();
		}
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $app_id
	 * @return unknown
	 */
	public static function getLists($uid, $app_id)
	{
		return Api_Dao_Favorites::getLists($uid, $app_id)->toArray();
	}

	/**
	 * 
	 *
	 * @param unknown_type $name
	 * @param unknown_type $app_id
	 * @param unknown_type $uid
	 * @return unknown
	 */
	public static function createList($name, $app_id, $uid)
	{
		if(Api_Dao_Favorites::isList($name, $app_id, $uid))
		{
			$list = Api_Dao_Favorites::getList($name, $app_id, $uid);
			if(count($list) > 0)
			{
				return $list[0]->id;
			}
			return false;
		}else
		{
			return Api_Dao_Favorites::createList($name, $app_id, $uid);
		}
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $uid
	 * @param unknown_type $item_id
	 * @param unknown_type $alid
	 * @param unknown_type $lid
	 * @return unknown
	 */
	public static function deleteFavorite($app_id, $uid, $item_id, $alid, $lid)
	{
		self::checkLists($alid, $lid);
		
		if(! self::isEmpty($alid))
		{
			return Api_Dao_Favorites::deleteFavoriteByAlid($app_id, $uid, $item_id, $alid);
		}else
		{
			return Api_Dao_Favorites::deleteFavoriteByLid($app_id, $uid, $item_id, $lid);
		}
	
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $uids
	 * @param unknown_type $alid
	 * @param unknown_type $lid
	 * @return unknown
	 */
	public static function getFavorites($app_id, $uids, $alid, $lid)
	{
		self::checkLists($alid, $lid);
		if(! self::isEmpty($alid))
		{
			return Api_Dao_Favorites::getFavoritesByAlid($app_id, $uids, $alid)->toArray();
		}else
		{
			return Api_Dao_Favorites::getFavoritesByLid($app_id, $uids, $lid)->toArray();
		}
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $uid
	 * @param unknown_type $item_id
	 * @param unknown_type $alid
	 * @param unknown_type $lid
	 */
	public static function setFavorite($app_id, $uid, $item_id, $alid, $lid, $fbml)
	{
		self::checkLists($alid, $lid);
		if(Api_Dao_Items::isItemActive($app_id, $item_id))
		{
			if(Api_Dao_Favorites::isFavorite($app_id, $uid, $item_id, $alid, $lid))
			{
				if(! self::isEmpty($alid))
				{
					return Api_Dao_Favorites::updateFavoriteByAlid($app_id, $uid, $item_id, $alid, $fbml);
				}else
				{
					return Api_Dao_Favorites::updateFavoriteByLid($app_id, $uid, $item_id, $lid, $fbml);
				}
			}else
			{
				return Api_Dao_Favorites::createFavorite($app_id, $uid, $item_id, $alid, $lid, $fbml);
			}
		}

		return false;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $item_id
	 * @param unknown_type $alid
	 * @param unknown_type $lid
	 * @param unknown_type $uids
	 * @return unknown
	 */
	public static function getFBML($app_id, $item_id, $alid, $lid, $uids)
	{
		self::checkLists($alid, $lid);
		if(! self::isEmpty($alid))
		{
			return Api_Dao_Favorites::getFavoritesByAlidAndItemId($app_id, $uids, $alid, $item_id)->toArray();
		}else
		{
			return Api_Dao_Favorites::getFavoritesByLidAndItemId($app_id, $uids, $lid, $item_id)->toArray();
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

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $alid
	 * @param unknown_type $lid
	 */
	private static function checkLists($alid, $lid)
	{
		if(! self::isEmpty($alid) && ! self::isEmpty($lid))
		{
			throw new OpenFBAPIException("The App List ID (alid=$alid) and the User List ID (lid=$lid) are mutually exclusive, provide one or the other, not both!", FB_ERROR_CODE_INVALID_PARAMETER);
		}
	}
}

?>
