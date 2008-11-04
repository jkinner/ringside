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
require_once ("ringside/rest/RatingsSet.php");
require_once ("ringside/api/dao/Ratings.php");

/**
 * @author Mark Lugert mlugert@ringsidenetworks.com
 */
class RatingsSetTestCase extends BaseAPITestCase
{

	public function testConstructor()
	{
		$appId = 1;
		$uid = 18077;
		$iid = "RatingsTestCaseItem5";
		$vote = 3;
		
		// missing iid
		$apiParams = array();
		try
		{
			$faf = $this->initRest(new RatingsSet(), $apiParams, $uid, $appId);
		}catch(Exception $e)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $e->getCode());
		}
		
		$apiParams = array();
		$apiParams['iid'] = $iid;
		try
		{
			$faf = $this->initRest(new RatingsSet(), $apiParams, $uid, $appId);
		}catch(Exception $e)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $e->getCode());
		}
		
		$apiParams = array();
		$apiParams['vote'] = $vote;
		try
		{
			$faf = $this->initRest(new RatingsSet(), $apiParams, $uid, $appId);
		}catch(Exception $e)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $e->getCode());
		}
		
		// missing iid
		$apiParams = array();
		$apiParams['iid'] = $iid;
		$apiParams['vote'] = $vote;
		try
		{
			$faf = $this->initRest(new RatingsSet(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "RatingsGetRatingForUser should not be null!");
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("RatingsSetRatingTestCase Exception: " . $exception->getMessage() . "\n\n" . $exception->getTraceAsString());
		}
	}

	public function testExecute()
	{
		$appId = 1;
		$app_id = 1;
		$uid = 18078;
		$iid = "RatingsTestCaseItem12";
		$vote = 4;
		
		$uids = "18078";
		$iids = "$iid";
		
		//RingsideAppsDbRatings::setRating(uid, iid, vote, units, dbCon)
		$apiParams = array();
		$apiParams['iid'] = $iid;
		$apiParams['vote'] = $vote;
		try
		{
			// Create our object
			$faf = $this->initRest(new RatingsSet(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "RatingsSetRating should not be null!");
			
			$result = $faf->execute();
			//$response [ 'result' ] = $ret?'1':'0';
			$this->assertEquals($result['result'], '1', "Result should be 1, but is: " . $result['result']);
			
			// Set the rating for this user
			$retVal = Api_Dao_Ratings::getRatings($app_id, array($iids), array($uids));
			$a = $retVal[0]->toArray();
			
			$this->assertNotNull($a);
			$this->assertEquals($a['uid'], $uid, "UID: " . $a['uid'] . "!=" . $uid);
			$this->assertEquals($a['item_id'], $iid, "iid: " . $a['item_id'] . "!=" . $iid);
			$this->assertEquals($a['vote'], $vote, "Vote: " . $a['vote'] . "!=" . $vote);
		}catch(OpenFBAPIException $exception)
		{
			$this->fail($exception->getMessage() . "\n" . $exception->getTraceAsString());
		}
	}

}

?>
