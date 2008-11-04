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
require_once ("ringside/api/dao/Items.php");

/**
 * @author mlugert@ringsidenetworks.com
 */
class Api_Bo_Items
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
		return Api_Dao_Items::isItemActive($app_id, $item_id);
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
		return Api_Dao_Items::deleteItem($app_id, $item_id);
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
	public static function setInfo($app_id, $item_id, $url, $refurl, $datatype)
	{
		return Api_Dao_Items::createItem($app_id, $item_id, $url, $refurl, $datatype);
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $iids
	 * @param unknown_type $datatype
	 */
	public static function getInfo($app_id, $iids, $datatype)
	{
		return Api_Dao_Items::getInfo($app_id, $iids, $datatype)->toArray();
	}
}

?>
