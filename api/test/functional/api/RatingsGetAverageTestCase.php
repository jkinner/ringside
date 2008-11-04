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
require_once( 'BaseAPITestCase.php' );
require_once( "ringside/api/OpenFBAPIException.php" );
require_once( "ringside/rest/RatingsGetAverage.php" );
require_once( "ringside/api/dao/Ratings.php" );

/**
 * @author Mark Lugert mlugert@ringsidenetworks.com
 */
class RatingsGetAverageTestCase extends BaseAPITestCase 
{
    public function testConstructor()
    {
    	$appId = 1;
        $uid = 18006;
        $uids = "18006";
        $iid = "RatingsTestCaseItem";

        // missing iid
        $apiParams = array();
        try {
            $faf = $this->initRest( new RatingsGetAverage(), $apiParams, $uid, $appId );
            $this->fail( "Should have gotten an exception." );
        } catch ( OpenFBAPIException $exception ) {
            $this->assertEquals( FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode() );
        }
        
        // should work
        $apiParams = array();
        $apiParams[ 'iid' ] = $iid;
    	try {
            $faf = $this->initRest( new RatingsGetAverage(), $apiParams, $uid, $appId );
            $this->assertNotNull($faf, "RatingsGetRating should not be null!");
        } catch ( OpenFBAPIException $exception ) {
            $this->fail("RatingsGetRatingForUserTestCase Exception: ".$exception->getMessage()."\n\n".$exception->getTraceAsString());
        }
        
    	// should work
        $apiParams = array();
        $apiParams[ 'iid' ] = $iid;
        $apiParams[ 'uids' ] = $uids;
    	try {
            $faf = $this->initRest( new RatingsGetAverage(), $apiParams, $uid, $appId );
            $this->assertNotNull($faf, "RatingsGetRating should not be null!");
            $faf->execute();
        } catch ( OpenFBAPIException $exception ) {
            $this->fail("RatingsGetRatingForUserTestCase Exception: ".$exception->getMessage()."\n\n".$exception->getTraceAsString());
        }
    }

    public function testExecute()
    {
    	$appId = 1;
    	$app_id = 1;
    	$uid = 18006;
        $iid = "RatingsTestCaseItem";
        $vote = 3;
        
        $uids = "18006";
        
        //RingsideAppsDbRatings::setRating(uid, iid, vote, units, dbCon)
    	$apiParams = array();
        $apiParams[ 'iid' ] = $iid;
        $apiParams[ 'uids' ] = $uids;
    	try {
    		// Create our object
            $faf = $this->initRest( new RatingsGetAverage(), $apiParams, $uid, $appId );
            $this->assertNotNull($faf, "RatingsGetRatingForUser should not be null!");
            
            // Set the rating for this user
            if(Api_Dao_Ratings::isRated($app_id, $uid, $iid))
            {
            	Api_Dao_Ratings::updateRating($app_id, $uid, $iid, $vote);
            }else 
            {
            	Api_Dao_Ratings::createRating($app_id, $uid, $iid, $vote);
            }
            
            $result = $faf->execute();
            $rating_array = $result[ 'rating' ][ 0 ];
            
            //$retVal[ 'rating' ][ 0 ] = array( 'iid'=>$item_id,'average_vote'=>$row[ 'average_vote' ] );
            $this->assertEquals($rating_array['iid'], $iid, "Item ids should be equal: ".$rating_array['iid']."!=".$iid);
            $this->assertEquals($rating_array['average_vote'], $vote, "Average vote should be $vote, but is: ".$rating_array['average_vote']);
        } catch ( OpenFBAPIException $exception ) {
            $this->fail($exception->getMessage()."\n".$exception->getTraceAsString());
        }
    }
}

?>
