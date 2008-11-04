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
require_once ('BaseAPITestCase.php');
require_once ("ringside/api/OpenFBAPIException.php");
require_once ("ringside/rest/RatingsGet.php");
require_once ("ringside/api/dao/Ratings.php");

/**
 * @author Mark Lugert mlugert@ringsidenetworks.com
 */
class RatingsGetTestCase extends BaseAPITestCase
{

	public function testConstructor()
	{
		$appId = 1;
		$uid = 18005;
		$uid2 = 18006;
		$iid = "RatingsTestCaseItem1";
		$iid2 = "RatingsTestCaseItem2";
		
		$iids = "$iid, $iid2";
		// missing iid
		$apiParams = array();
		try
		{
			$faf = $this->initRest(new RatingsGet(), $apiParams, $uid, $appId);
		}catch(Exception $e)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $e->getCode());
		}
		
		$apiParams = array();
		$apiParams['uids'] = uids;
		try
		{
			$faf = $this->initRest(new RatingsGet(), $apiParams, $uid, $appId);
		}catch(Exception $e)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $e->getCode());
		}
		
		// missing iid
		$apiParams = array();
		$apiParams['uids'] = uids;
		$apiParams['iids'] = $iids;
		try
		{
			$faf = $this->initRest(new RatingsGet(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "RatingsGetRatingForUser should not be null!");
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("RatingsGetRatingForUserTestCase Exception: " . $exception->getMessage() . "\n\n" . $exception->getTraceAsString());
		}
	}

	/**
	 * Tests getting a rating for a list of users on a list of item ids
	 */
	public function testExecute()
	{
		$appId = 1;
		$app_id = 1;
		$uid = 18015;
		$uid2 = 18016;
		$iid = "RatingsTestCaseItem3";
		$iid2 = "RatingsTestCaseItem4";
		
		$uids = "$uid, $uid2";
		$iids = "$iid, $iid2";
		
		$vote = 2;
		
		//RingsideAppsDbRatings::setRating(uid, iid, vote, units, dbCon)
		$apiParams = array();
		$apiParams['iids'] = $iids;
		try
		{
			// Create our object
			$faf = $this->initRest(new RatingsGet(), $apiParams, $uid, $appId);
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
			$a = $result['rating'][0];
			
			$this->assertNotNull($a);
			$this->assertEquals($a['uid'], $uid, "UID: " . $a['uid'] . "!=" . $uid);
			$this->assertEquals($a['iid'], $iid, "iid: " . $a['iid'] . "!=" . $iid);
			$this->assertEquals($a['vote'], $vote, "Vote: " . $a['vote'] . "!=" . $vote);
		}catch(OpenFBAPIException $exception)
		{
			$this->fail($exception->getMessage() . "\n" . $exception->getTraceAsString());
		}
	}
}

?>
