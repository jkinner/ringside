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
class RatingsSet extends Api_DefaultRest
{
	private $iid;
	private $vote;

	/**
	 * Validate Request.
	 */
	public function validateRequest( ) {

		$this->iid = $this->getRequiredApiParam( 'iid' );
		$this->vote = $this->getRequiredApiParam( 'vote' );		
	}

	/**
    * Sets the user's rating for an item.
    *
    * @param int $iid the item to rate
    * @param number $vote the score given by the user for the item
    * 
    * public function ratings_set($iid, $vote)
    */
	public function execute()
	{
		$ret = Api_Bo_Ratings::setRating($this->getAppId(),$this->getUserId(), $this->iid, $this->vote);
		$response [ 'result' ] = $ret?'1':'0';
		return $response;
	}

}

?>
