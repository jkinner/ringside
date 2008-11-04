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
require_once ("ringside/api/dao/Ratings.php");

/**
 * @author mlugert@ringsidenetworks.com
 */
class Api_Bo_Ratings
{
	/**
	 * Returns the ratings for the given item ids and user ids.
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $iids
	 * @param unknown_type $uids
	 */
	public static function getRatings($app_id, $item_ids, $uids)
	{
		return Api_Dao_Ratings::getRatings($app_id, $item_ids, $uids)->toArray();
	}

	/**
	 * Returns the average rating for this item.  If uids is !empty($uids) then it returns the average rating for the item
	 * with the provided uids.
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $item_id
	 * @param unknown_type $uids
	 * @return unknown
	 */
	public static function getAverage($app_id, $item_id, $uids)
	{
		if(! empty($uids))
		{
			return Api_Dao_Ratings::getAverageRatingByUids($app_id, $item_id, $uids);
		}else
		{
			return Api_Dao_Ratings::getAverageRating($app_id, $item_id);
		}
	}

	/**
	 * Rates the item for the given user
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $uid
	 * @param unknown_type $item_id
	 * @param unknown_type $vote
	 * @param unknown_type $dbCon
	 * @return unknown
	 */
	public static function setRating($app_id, $uid, $item_id, $vote)
	{
		$ret = false;
		if(Api_Dao_Ratings::isRated($app_id, $uid, $item_id))
		{
			$ret = Api_Dao_Ratings::updateRating($app_id, $uid, $item_id, $vote);
		}else
		{
			$ret = Api_Dao_Ratings::createRating($app_id, $uid, $item_id, $vote);
		}
		
		if($ret === false)
		{
			return false;
		}
		
		return true;
	}
}

?>
