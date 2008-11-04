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
require_once ('ringside/api/OpenFBAPIException.php');
require_once ('ringside/rest/FavoritesGetUsers.php');
require_once ('CoreApiUtil.php');

/**
 * @author Mark Lugert mlugert@ringsidenetworks.com
 */
class FavoritesGetUsersTestCase extends BaseAPITestCase
{

	/*
	 * Test the creation of the Object
	 */
	public function testConstructor()
	{
		$appId = 10;
		$uid = 18066;
		$iid = "TestGetUsers1";
		$lid = 1;
		$alid = 54;
		
		$uids = "18066";
		
		$apiParams = array();		
		try
		{
			$this->initRest(new FavoritesGetUsers(), $apiParams, $uid, $appId);
		}catch(Exception $e)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $e->getCode());
		}
		
		// Try with all params set, still should not fail
		$apiParams = array();
		$apiParams['iid'] = $iid;
		$apiParams['uids'] = $uids;
		try
		{
			$faf = $this->initRest(new FavoritesGetUsers(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "FavoritesGetUsers should not be null!");
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have gotten an exception: " . $exception->getCode());
		}
		
		// Try with all params set, still should not fail
		$apiParams = array();
		$apiParams['iid'] = $iid;
		$apiParams['lid'] = $lid;
		$apiParams['alid'] = $alid;
		$apiParams['uids'] = $uids;
		$faf = $this->initRest(new FavoritesGetUsers(), $apiParams, $uid, $appId);
		$this->assertNotNull($faf, "FavoritesGetUsers should not be null!");
		
		// Try with all params set, still should not fail
		$apiParams = array();
		$apiParams['iid'] = $iid;
		try
		{
			$faf = $this->initRest(new FavoritesGetUsers(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "FavoritesGetUsers should not be null!");
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have gotten an exception: " . $exception->getCode());
		}
		
		// Try with all params set, still should not fail
		$apiParams = array();
		$apiParams['iid'] = $iid;
		$apiParams['uids'] = $uids;
		$apiParams['alid'] = $alid;
		try
		{
			$faf = $this->initRest(new FavoritesGetUsers(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "FavoritesGetUsers should not be null!");
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have gotten an exception: " . $exception->getCode());
		}
	}

	/*
     * Test the execution of the Object
     */
	public function testExecute()
	{
		$appId = 10;
		$app_id = 10;
		$uid = 18066;
		$iid = "TestGetUsers1";
		$lid = null;
		$alid = 32;
		
		$uids = "18066";
		
		try
		{
			$apiParams = array();
			$apiParams['iid'] = $iid;
			$apiParams['uids'] = $uids;
			$apiParams['alid'] = $alid;
			
			$faf = $this->initRest(new FavoritesGetUsers(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "FavoritesGetUsers should not be null!");
			
			Api_Dao_Items::createItem($app_id, $iid, "", "", 0);
			CoreApiUtil::createFavorite($app_id, $uid, $iid, $alid, $lid);
			
			$retVal = $faf->execute();
			$a = $retVal['user'][0];
			$this->assertEquals($uid, $a);
		}catch(OpenFBAPIException $exception)
		{
			$this->fail($exception->getMessage() . "\n" . $exception->getTraceAsString());
		}
	
	}
}

?>
