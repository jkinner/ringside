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
require_once ('ringside/api/AbstractRest.php');
require_once ('ringside/api/dao/records/RingsideRating.php');
/**
 * @author mlugert@ringsidenetworks.com
 */
class Api_Dao_Ratings
{

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $iids
	 * @param unknown_type $uids
	 * @return unknown
	 */
	public static function getRatings($app_id, $iids, $uids)
	{
		//$items = explode(',', $iids);
		$comma_separated_item_list = implode("','", $iids);
		$comma_separated_uid_list = implode(',', $uids);
		
		$q = Doctrine_Query::create();
		$q->select('item_id, uid, vote')->from('RingsideRating')->where("app_id = $app_id AND uid IN ($comma_separated_uid_list) AND item_id IN ('$comma_separated_item_list')");
		
		return $q->execute();
	}

	/**
	 * Gets the average vote for the item across all uids
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $item_id
	 * @return unknown
	 */
	public static function getAverageRating($app_id, $item_id)
	{
		$q = Doctrine_Query::create();
		$q->select('AVG(vote) AS average_vote')->from('RingsideRating')->where('app_id = ? AND item_id = ?');
		$avg = $q->execute(array($app_id, $item_id));
		if(!$avg[0]->contains('average_vote'))
		{
			return 0;
		}
		return $avg[0]['average_vote'];
	}

	/**
	 * Gets the average vote for the given item and uids
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $item_id
	 * @param unknown_type $uids
	 * @return unknown
	 */
	public static function getAverageRatingByUids($app_id, $item_id, $uids)
	{
		$comma_separated_uid_list = implode(',', $uids);
		
		$q = Doctrine_Query::create();
		$q->select('AVG(vote) AS average_vote')->from('RingsideRating')->where('app_id = ? AND item_id = ? AND uid in (?)');
		$avg = $q->execute(array($app_id, $item_id, $comma_separated_uid_list));
		if(!$avg[0]->contains('average_vote'))
		{
			return 0;
		}
		return $avg[0]['average_vote'];
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $uid
	 * @param unknown_type $item_id
	 * @param unknown_type $vote
	 * @return unknown
	 */
	public static function createRating($app_id, $uid, $item_id, $vote)
	{
		$rating = new RingsideRating();
		$rating->app_id = $app_id;
		$rating->uid = $uid;
		$rating->item_id = $item_id;
		$rating->vote = $vote;
		$ret = $rating->trySave();
		
		if($ret)
		{
			return $rating->getIncremented();
		}
		
		return false;
	}

	/**
	 * Updates a users vote for this particular item
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $uid
	 * @param unknown_type $item_id
	 * @param unknown_type $vote
	 */
	public static function updateRating($app_id, $uid, $item_id, $vote)
	{
		$q = Doctrine_Query::create();
		$q->update('RingsideRating')->set('vote', '?', $vote)->where('app_id = ? AND uid = ? AND item_id = ?');
		return $q->execute(array($app_id, $uid, $item_id));
	}

	/**
	 * Returns true if the item has been rated by this user, returns false otherwise.
	 *
	 * @param unknown_type $app_id
	 * @param unknown_type $uid
	 * @param unknown_type $item_id
	 * @return unknown
	 */
	public static function isRated($app_id, $uid, $item_id)
	{
		$q = Doctrine_Query::create();
		$q->select('count(id) as rating_count')->from('RingsideRating')->where("app_id = ? AND uid = ? AND item_id = ?");
		$count = $q->execute(array($app_id, $uid, $item_id));
		
		if($count[0]['rating_count'] > 0)
		{
			return true;
		}
		
		return false;
	}
}
?>
