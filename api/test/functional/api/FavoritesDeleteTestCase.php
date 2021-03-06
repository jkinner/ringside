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
require_once ('ringside/rest/FavoritesDelete.php');
require_once ("ringside/api/dao/Favorites.php");
require_once ('CoreApiUtil.php');

/**
 * @author Mark Lugert mlugert@ringsidenetworks.com
 */
class FavoritesDeleteTestCase extends BaseAPITestCase
{

	/*
	 * Test the creation of the Object
	 */
	public function testConstructor()
	{
		$appId = 1;
		$uid = 18014;
		$iid = "Test Item";
		$alid = 1;
		$lid = 2;
		
		// missing a list id
		$apiParams = array();
		try
		{
			$faf = $this->initRest(new FavoritesDelete(), $apiParams, $uid, $appId);
		}catch(Exception $e)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $e->getCode());
		}
		
		// missing iid
		$apiParams = array();
		$apiParams['alid'] = $alid;
		try
		{
			$faf = $this->initRest(new FavoritesDelete(), $apiParams, $uid, $appId);
		}catch(Exception $e)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $e->getCode());
		}
		
		// to many list ids
		$apiParams = array();
		$apiParams['iid'] = $iid;
		$apiParams['alid'] = $alid;
		$apiParams['lid'] = $lid;
		
		$faf = $this->initRest(new FavoritesDelete(), $apiParams, $uid, $appId);
		$this->assertNotNull($faf, "FavoritesDelete should not be null!");
		
		// no more exceptions
		$apiParams = array();
		$apiParams['iid'] = $iid;
		$apiParams['lid'] = $lid;
		try
		{
			$faf = $this->initRest(new FavoritesDelete(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "FavoritesDelete should not be null!");
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have gotten an exception: " . $exception->getCode());
		}
		
		$apiParams = array();
		$apiParams['iid'] = $iid;
		try
		{
			$faf = $this->initRest(new FavoritesDelete(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "FavoritesDelete should not be null!");
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have gotten an exception: " . $exception->getCode());
		}
		
		// no more exceptions
		$apiParams = array();
		$apiParams['iid'] = $iid;
		$apiParams['alid'] = $alid;
		try
		{
			$faf = $this->initRest(new FavoritesDelete(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "FavoritesDelete should not be null!");
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
		$appId = 1;
		$app_id = 1;
		$name = "Test User List";
		$uid = 18015;
		$iid = "Test Item Delete";
		$alid = 2;
		$lid = null;
		$uids = array("18015");
		
		$apiParams = Array();
		$apiParams['iid'] = $iid;
		$apiParams['alid'] = $alid;
		
		try
		{
			// Test alid
			$faf = $this->initRest(new FavoritesDelete(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "FavoritesDelete should not be null!");
			
			$this->assertTrue(Api_Dao_Items::createItem($app_id, $iid, "", "", 0) !== false);
			$this->assertTrue(CoreApiUtil::createFavorite($app_id, $uid, $iid, $alid, $lid) !== false);
			
			$this->assertTrue(CoreApiUtil::isFavorite($app_id, $iid, $alid, $lid, $uids), "Favorite should exist!");
			$result = $faf->execute();
			$this->assertEquals($result['result'], '1');
			$this->assertFalse(CoreApiUtil::isFavorite($app_id, $iid, $alid, $lid, $uids), "Favorite should no longer exist!");
			
			try
			{
				$alid = null;
				$lid = 1001;
				CoreApiUtil::createFavorite($app_id, $uid, $iid, $alid, $lid);
				$this->fail("Create should not work if the user list does not exist!");
			}catch(Exception $e)
			{
				// ignore
			}
			
			// Now test lid
			$apiParams = array();
			$apiParams['iid'] = $iid;
			
			$lid = CoreApiUtil::createList($name, $app_id, $uid);
			$apiParams['lid'] = $lid;
			CoreApiUtil::createFavorite($app_id, $uid, $iid, $alid, $lid);
			
			$faf = $this->initRest(new FavoritesDelete(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "FavoritesDelete should not be null!");
			
			$this->assertTrue(CoreApiUtil::isFavorite($app_id, $iid, $alid, $lid, $uids), "Favorite should exist!");
			$result = $faf->execute();
			$this->assertEquals($result['result'], '1');
			$this->assertFalse(CoreApiUtil::isFavorite($app_id, $iid, $alid, $lid, $uids), "Favorite should no longer exist!");
		}catch(OpenFBAPIException $exception)
		{
			$this->fail($exception->getMessage() . "\n" . $exception->getTraceAsString());
		}
	
	}
}

?>
