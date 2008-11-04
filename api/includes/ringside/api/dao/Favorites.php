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
require_once ("ringside/api/AbstractRest.php");
require_once ("ringside/api/dao/Items.php");
require_once ("ringside/api/dao/RingsideConstants.php");
require_once ('ringside/api/dao/records/RingsideFavorite.php');
require_once ('ringside/api/dao/records/RingsideFavoritesList.php');

/**
 * @author mlugert@ringsidenetworks.com
 */
class Api_Dao_Favorites
{

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $item_id
	 * @param unknown_type $alid
	 * @param array $uids
	 * @return unknown
	 */
	public static function getUsersByAlid($app_id, $item_id, $alid, array $uids)
	{
		$comma_separated_user_list = implode(',', $uids);
		
		$q = Doctrine_Query::create();
		$q->select('user_id')->from('RingsideFavorite')->where("status='" . RingsideConstants::STATUS_ACTIVE . "' AND app_id=$app_id AND item_id='$item_id' AND app_list_id = $alid AND user_id IN ($comma_separated_user_list)");
		return $q->execute();
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $item_id
	 * @param unknown_type $lid
	 * @param array $uids
	 * @return unknown
	 */
	public static function getUsersByLid($app_id, $item_id, $lid, array $uids)
	{
		$comma_separated_user_list = implode(',', $uids);
		
		$q = Doctrine_Query::create();
		$q->select('user_id')->from('RingsideFavorite')->where("status='" . RingsideConstants::STATUS_ACTIVE . "' AND app_id=$app_id AND item_id='$item_id' AND list_id = $lid AND user_id IN ($comma_separated_user_list)");
		return $q->execute();
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $name
	 * @param unknown_type $app_id
	 * @param unknown_type $uid
	 * @return unknown
	 */
	public static function isList($name, $app_id, $uid)
	{
		$q = Doctrine_Query::create();
		$q->select('count(id) as list_count')->from('RingsideFavoritesList')->where("uid=$uid AND app_id=$app_id AND name='$name'");
		$count = $q->execute();
		
		if($count[0]['list_count'] > 0)
		{
			return true;
		}
		
		return false;
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
		$q = Doctrine_Query::create();
		$q->select('id, name')->from('RingsideFavoritesList')->where('uid = ? AND app_id = ?');
		return $q->execute(array($uid, $app_id));
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $name
	 * @param unknown_type $app_id
	 * @param unknown_type $uid
	 * @return unknown
	 */
	public static function getList($name, $app_id, $uid)
	{
		$q = Doctrine_Query::create();
		$q->select('id, name')->from('RingsideFavoritesList')->where('uid = ? AND app_id = ? AND name = ?');
		return $q->execute(array($uid, $app_id, $name));
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $name
	 * @param unknown_type $app_id
	 * @param unknown_type $uid
	 * @return unknown
	 */
	public static function createList($name, $app_id, $uid)
	{
		$fl = new RingsideFavoritesList();
		$fl->name = $name;
		$fl->app_id = $app_id;
		$fl->uid = $uid;
		$ret = $fl->trySave();
		
		if($ret)
		{
			return $fl->getIncremented();
		}
		
		return false;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $uids
	 * @param unknown_type $lid
	 * @return unknown
	 */
	public static function getFavoritesByLid($app_id, $uids, $lid)
	{
		$uid_list = implode(',', $uids);
		$q = Doctrine_Query::create();
		$q->select('user_id, item_id, status, list_id, app_list_id, fbml')
			->from('RingsideFavorite')
			->where("status='" . RingsideConstants::STATUS_ACTIVE . "' AND app_id=$app_id AND list_id = $lid AND user_id IN ($uid_list)");
		return $q->execute();
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $uids
	 * @param unknown_type $alid
	 * @return unknown
	 */
	public static function getFavoritesByAlid($app_id, $uids, $alid)
	{
		$uid_list = implode(',', $uids);
		$q = Doctrine_Query::create();
		$q->select('user_id, item_id, status, list_id, app_list_id, fbml')->from('RingsideFavorite')->where("status='" . RingsideConstants::STATUS_ACTIVE . "' AND app_id=$app_id AND app_list_id = $alid AND user_id IN ($uid_list)");
		return $q->execute();
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $uids
	 * @param unknown_type $lid
	 * @param unknown_type $item_id
	 * @return unknown
	 */
	public static function getFavoritesByLidAndItemId($app_id, $uids, $lid, $item_id)
	{
		$uid_list = implode(',', $uids);
		$q = Doctrine_Query::create();
		$q->select('user_id, item_id, status, list_id, app_list_id, fbml')->from('RingsideFavorite')->where("status='" . RingsideConstants::STATUS_ACTIVE . "' AND item_id = $item_id AND app_id=$app_id AND list_id = $lid AND user_id IN ($uid_list)");
		return $q->execute();
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $uids
	 * @param unknown_type $alid
	 * @param unknown_type $item_id
	 * @return unknown
	 */
	public static function getFavoritesByAlidAndItemId($app_id, $uids, $alid, $item_id)
	{
		$uid_list = implode(',', $uids);
		$q = Doctrine_Query::create();
		$q->select('user_id, item_id, status, list_id, app_list_id, fbml')->from('RingsideFavorite')->where("status='" . RingsideConstants::STATUS_ACTIVE . "' AND item_id = '$item_id' AND app_id=$app_id AND app_list_id = $alid AND user_id IN ($uid_list)");
		return $q->execute();
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $uid
	 * @param unknown_type $item_id
	 * @param unknown_type $alid
	 * @param unknown_type $lid
	 * @param unknown_type $fbml
	 * @return unknown
	 */
	public static function createFavorite($app_id, $uid, $item_id, $alid = 0, $lid = 0, $fbml)
	{
		$fav = new RingsideFavorite();
		$fav->app_id = $app_id;
		$fav->item_id = $item_id;
		$fav->app_list_id = intval($alid);
		$fav->user_id = $uid;
		$fav->status = RingsideConstants::STATUS_ACTIVE;
		$fav->fbml = $fbml;
		
		if(0 != intval($lid))
		{
			$fav->list_id = intval($lid);
		}
		$ret = $fav->trySave();
		
		if($ret)
		{
			return $fav->getIncremented();
		}
		
		return false;
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
	public static function isFavorite($app_id, $uid, $item_id, $alid = 0, $lid = 0)
	{
		$q = Doctrine_Query::create();
		$q->select('count(id) as fav_count')->from('RingsideFavorite')->where("app_id = ? AND user_id = ? AND item_id = ? AND app_list_id = ? AND list_id = ?");
		$favs = $q->execute(array($app_id, $uid, $item_id, intval($alid), intval($lid)));
		
		return $favs[0]['fav_count'];
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $uid
	 * @param unknown_type $item_id
	 * @param unknown_type $alid
	 * @return unknown
	 */
	public static function deleteFavoriteByAlid($app_id, $uid, $item_id, $alid)
	{
		$q = Doctrine_Query::create();
		$q->update('RingsideFavorite')->set('status', '?', RingsideConstants::STATUS_DELETED)->where("app_id=$app_id AND user_id=$uid AND item_id='$item_id' AND app_list_id = $alid");
		return $q->execute();
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $uid
	 * @param unknown_type $item_id
	 * @param unknown_type $lid
	 * @return unknown
	 */
	public static function deleteFavoriteByLid($app_id, $uid, $item_id, $lid)
	{
		$q = Doctrine_Query::create();
		$q->update('RingsideFavorite')->set('status', '?', RingsideConstants::STATUS_DELETED)->where("app_id=$app_id AND user_id=$uid AND item_id='$item_id' AND list_id = $lid");
		return $q->execute();
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $uid
	 * @param unknown_type $item_id
	 * @param unknown_type $lid
	 * @param unknown_type $fbml
	 * @return unknown
	 */
	public static function updateFavoriteByLid($app_id, $uid, $item_id, $lid, $fbml = '')
	{
		if(null == $fbml || strlen(rtrim($fbml)) == 0)
		{
			$fbml = '';
		}
		
		$q = Doctrine_Query::create();
		$q->update('RingsideFavorite')->set('status', '?', RingsideConstants::STATUS_ACTIVE)->set('app_id', '?', $app_id)->set('user_id', '?', $uid)->set('item_id', '?', $item_id)->set('list_id', '?', intval($lid))->set('fbml', '?', $fbml)->where("user_id=? AND item_id=? AND list_id=? AND app_id=?");
		return $q->execute(array($uid, $item_id, intval($lid), $app_id));
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $uid
	 * @param unknown_type $item_id
	 * @param unknown_type $alid
	 * @param unknown_type $fbml
	 * @return unknown
	 */
	public static function updateFavoriteByAlid($app_id, $uid, $item_id, $alid, $fbml = '')
	{
		if(null == $fbml || strlen(rtrim($fbml)) == 0)
		{
			$fbml = '';
		}
		
		$q = Doctrine_Query::create();
		$q->update('RingsideFavorite')->set('app_id', '?', $app_id)->set('user_id', '?', $uid)->set('item_id', '?', $item_id)->set('app_list_id', '?', intval($alid))->set('fbml', '?', $fbml)->where("user_id=? AND item_id=? AND app_list_id=? AND app_id=?");
		return $q->execute(array($uid, $item_id, intval($alid), $app_id));
	}
}
?>
