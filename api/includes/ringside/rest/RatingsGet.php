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
require_once ("ringside/api/DefaultRest.php");
require_once ("ringside/api/bo/Ratings.php");

/**
 * @author Mark Lugert mlugert@ringsidenetworks.com
 */
class RatingsGet extends Api_DefaultRest
{
	private $item_ids;
	private $uids;

	/**
	 * Validate request.
	 */
	public function validateRequest()
	{
		$iids = $this->getRequiredApiParam('iids');
		$this->uids = $this->getApiParam('uids', $this->getUserId());
		
		$uid_string = $this->getApiParam('uids', $this->getUserId());
		
		if(strlen($uid_string) > 0)
		{
			$this->uids = explode(',', $uid_string);
		}else
		{
			$this->uids = array();
		}
		
		if(strlen($iids) > 0)
		{
			$this->item_ids = explode(',', $iids);
		}else
		{
			$this->item_ids = array();
		}
	}

	/**
	 * Gets the rating given for an item by one or more users, or for the logged in user.
	 *
	 * Data format:
	 * <code>
	 * 
	 * </code>
	 * @param array $iids the items that are being rated
	 * @param array $uids optional - the list of users to retrieve ratings for. Defaults to the logged in user if not provided
	 * @return array of ratings
	 * 
	 * public function ratings_get($iids, $uids = null)
	 */
	public function execute()
	{
		$ratings = Api_Bo_Ratings::getRatings($this->getAppId(), $this->item_ids, $this->uids);
		$retVal = array();
		if(count($ratings) > 0)
		{
			$retVal['rating'] = array();
			$i = 0;
			foreach($ratings as $rating)
			{
				$retVal['rating'][$i ++] = array('uid' => $rating['uid'], 'iid' => $rating['item_id'], 'vote' => $rating['vote']);
			}
		}
		
		return $retVal;
	}

}

?>
