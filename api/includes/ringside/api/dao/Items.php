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
require_once ("ringside/api/dao/RingsideConstants.php");
require_once ('ringside/api/dao/records/RingsideItem.php');
/**
 * @author mlugert@ringsidenetworks.com
 */
class Api_Dao_Items
{
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $item_id
	 * @return unknown
	 */
	public static function isItem($app_id, $item_id)
	{
		$q = Doctrine_Query::create();
		$q->select('count(item_id) as item_count')->from('RingsideItem i')->where("item_app_id=$app_id AND item_id='$item_id'");
		$items = $q->execute();
		
		if($items[0]['item_count'] > 0)
		{
			return true;
		}
		
		return false;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $item_id
	 * @return unknown
	 */
	public static function isItemActive($app_id, $item_id)
	{
		$q = Doctrine_Query::create();
		$q->select('count(item_id) as item_count')->from('RingsideItem i')->where("item_app_id=$app_id AND item_id='$item_id' AND item_status = '" . RingsideConstants::STATUS_ACTIVE . "'");
		$items = $q->execute();
		
		if($items[0]['item_count'] > 0)
		{
			return true;
		}
		
		return false;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $item_id
	 * @return unknown
	 */
	public static function deleteItem($app_id, $item_id)
	{
		$q = Doctrine_Query::create();
		$q->update('RingsideItem i')->set('item_status', '?', RingsideConstants::STATUS_DELETED)->where("item_app_id=$app_id AND item_id='$item_id'");
		$rows = $q->execute();
		
		if($rows > 0)
		{
			return true;
		}
		
		return false;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $item_id
	 * @param unknown_type $url
	 * @param unknown_type $refurl
	 * @param unknown_type $datatype
	 * @return unknown
	 */
	public static function createItem($app_id, $item_id, $url, $refurl, $datatype = 0)
	{
		if(self::isItem($app_id, $item_id))
		{
			return self::updateItem($app_id, $item_id, $url, $refurl, $datatype, RingsideConstants::STATUS_ACTIVE);
		}else
		{
			$item = new RingsideItem();
			$item->item_app_id = $app_id;
			$item->item_id = $item_id;
			$item->item_url = $url;
			$item->item_refurl = $refurl;
			$item->item_data_type = intval($datatype);
			return $item->trySave();
		}
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $item_id
	 * @param unknown_type $url
	 * @param unknown_type $refurl
	 * @param unknown_type $datatype
	 * @param unknown_type $status
	 * @return unknown
	 */
	public static function updateItem($app_id, $item_id, $url, $refurl, $datatype = 0, $status = RingsideConstants::STATUS_ACTIVE)
	{
		$q = Doctrine_Query::create();
		$q->update('RingsideItem')->set('item_status', '?', $status)->set('item_url', '?', $url)->set('item_refurl', '?', $refurl)->set('item_data_type', '?', $datatype)->where("item_app_id=$app_id AND item_id='$item_id'");
		return $q->execute();
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $iids
	 * @param unknown_type $datatype
	 * @return unknown
	 */
	public static function getInfo($app_id, $iids, $datatype = 0)
	{
		$items = explode(',', $iids);
		$comma_separated_item_list = implode("','", $items);
		$where = "item_status='" . RingsideConstants::STATUS_ACTIVE . "' AND item_app_id=$app_id AND item_data_type=" . intval($datatype);
		if(strlen($comma_separated_item_list) > 0)
		{
			$where .= " AND item_id IN ('$comma_separated_item_list')";
		}
		
		$q = Doctrine_Query::create();
		$q->select('item_app_id, item_id, item_url, item_refurl, item_data_type')->from('RingsideItem')->where($where);
		return $q->execute();
	}
}
?>
