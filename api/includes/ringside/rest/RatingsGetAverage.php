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
require_once( "ringside/api/DefaultRest.php" );
require_once( "ringside/api/bo/Ratings.php" );

/**
 * @author Mark Lugert mlugert@ringsidenetworks.com
 */
class RatingsGetAverage extends Api_DefaultRest
{
	private $item_id;
	private $uids;

	/**
	 * Validate request.
	 */
	public function validateRequest( ) 
	{
		$this->item_id = $this->getRequiredApiParam( 'iid' );
		$uid_string = $this->getApiParam( 'uids', null );  

		if(null === $uid_string)
		{
			$this->uids = array();
		}else 
		{
			$this->uids = explode(',', $uid_string);
		}
	}
    
	/**
    * Returns the average rating information across the uids.
    *
    * @param int $iid the item used to calculate the average
    * @param array $uids the uids used to retrieve the ratings to calculate the average, otherwise default to all users
    * 
    * public function ratings_getAverage($iid, $uids = null)
    */
	public function execute()
	{
		$vote = Api_Bo_Ratings::getAverage($this->getAppId(), $this->item_id, $this->uids);
		
		$retVal = array();
		$retVal['rating'] = array();
		$retVal['rating'][0] = array('iid' => $this->item_id, 'average_vote' => $vote);
		return $retVal;
	}
}
?>
