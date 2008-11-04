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
require_once ("ringside/api/bo/Feed.php");

/**
 * @author Brian Robinson brobinson@ringsidenetworks.com
 */
class FeedGet extends Api_DefaultRest
{
	private $uid;
	private $actorid;
	private $friends;
	private $actions;
	private $stories;

	/**
	 * Validate request.
	 */
	public function validateRequest()
	{
		$this->uid = $this->getApiParam( 'uid', null );
		$this->actorid = $this->getApiParam( 'actorid', null );
		$this->friends = $this->getApiParam( 'friends', false );
		$this->actions = $this->getApiParam( 'actions', true );
		$this->stories = $this->getApiParam( 'stories', true );
		
		if( !isset( $this->uid ) && !isset( $this->actorid ) ) {
			throw new OpenFBAPIException( 'Etiher uid or actorid must be set', FB_ERROR_CODE_PARAMETER_MISSING );
		}
        
        if( isset( $this->uid ) && isset( $this->actorid ) ) {
            throw new OpenFBAPIException( 'Etiher uid or actorid must be set, but not both' );
        }
		
        if( isset( $this->actions ) && isset( $this->stories ) &&
            $this->actions == false && $this->stories == false ) {
            throw new OpenFBAPIException( 'Etiher actions or or stories may be set to false, but not both' );
        }
        if( !empty( $this->actorid ) && $this->friends == true ) {
            throw new OpenFBAPIException( 'Cannot specify actorid and friends.  Actors cannot have friends currently' );
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
        $results = $this->get();
//        error_log( 'results: ' . var_export( $results, true ) );
        if( empty( $results ) ) {
        	return array();
        }
        else {
            $entries = array( 'result' => $results );
            return $entries;
        }
	}
	
	public function get() {
        return Api_Bo_Feed::getFeedEntries( $this->uid, $this->actorid, $this->friends, $this->actions, $this->stories );
	}

}

?>
