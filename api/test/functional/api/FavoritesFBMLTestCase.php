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
require_once ("ringside/rest/FavoritesSetFBML.php");
require_once ("ringside/rest/FavoritesGetFBML.php");
require_once ('CoreApiUtil.php');

/**
 * @author Mark Lugert mlugert@ringsidenetworks.com
 */
class FavoritesFBMLTestCase extends BaseAPITestCase
{

	/*
	 * Test the creation of the Object
	 */
	public function testGetFBMLConstructor()
	{
		$appId = 1;
		$uid = 18014;
		$iid = "Test Item";
		$alid = 1;
		$lid = 2;
		$uids = "18014";
		
		// missing a list id
		$apiParams = array();
		try
		{
			$faf = $this->initRest(new FavoritesGetFBML(), $apiParams, $uid, $appId);
		}catch(Exception $e)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $e->getCode());
		}
		
		// missing iid
		$apiParams = array();
		$apiParams['alid'] = $alid;
		try
		{
			$faf = $this->initRest(new FavoritesGetFBML(), $apiParams, $uid, $appId);
		}catch(Exception $e)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $e->getCode());
		}
		
		// to many list ids
		$apiParams = array();
		$apiParams['iid'] = $iid;
		$apiParams['alid'] = $alid;
		$apiParams['lid'] = $lid;
		$faf = $this->initRest(new FavoritesGetFBML(), $apiParams, $uid, $appId);
		$this->assertNotNull($faf, "FavoritesGetFBML should not be null!");
		
		// no more exceptions
		$apiParams = array();
		$apiParams['iid'] = $iid;
		$apiParams['lid'] = $lid;
		$apiParams['uids'] = $uids;
		try
		{
			$faf = $this->initRest(new FavoritesGetFBML(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "FavoritesGetFBML should not be null!");
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have gotten an exception: " . $exception->getCode());
		}
		
		$apiParams = array();
		$apiParams['iid'] = $iid;
		try
		{
			$faf = $this->initRest(new FavoritesGetFBML(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "FavoritesGetFBML should not be null!");
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have gotten an exception: " . $exception->getCode());
		}
		
		// no more exceptions
		$apiParams = array();
		$apiParams['iid'] = $iid;
		$apiParams['alid'] = $alid;
		$apiParams['uids'] = $uids;
		try
		{
			$faf = $this->initRest(new FavoritesGetFBML(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "FavoritesGetFBML should not be null!");
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have gotten an exception: " . $exception->getCode());
		}
	}

	/*
	 * Test the creation of the Object
	 */
	public function testSetFBMLConstructor()
	{
		$appId = 1;
		$uid = 18016;
		$iid = "Test Item";
		$alid = 5;
		$lid = 6;
		$fbml = "Just some string!";
		
		// missing a list id
		$apiParams = array();
		$apiParams['iid'] = $iid;
		try
		{
			$faf = $this->initRest(new FavoritesSetFBML(), $apiParams, $uid, $appId);
			$this->fail("Should have gotten an exception.");
			$faf->execute();
		}catch(OpenFBAPIException $exception)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode());
		}
		
		$apiParams = array();
		$apiParams['iid'] = $iid;
		$apiParams['fbml'] = $fbml;
		try
		{
			$faf = $this->initRest(new FavoritesSetFBML(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "FavoritesSetFBML should not be null!");
			$faf->execute();
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have gotten an exception: " . $exception->getCode());
		}
		
		// missing iid
		$apiParams = array();
		$apiParams['alid'] = $alid;
		try
		{
			$faf = $this->initRest(new FavoritesSetFBML(), $apiParams, $uid, $appId);
			$faf->execute();
			$this->fail("Should have gotten an exception.");
		}catch(OpenFBAPIException $exception)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode());
		}
		
		// to many list ids
		$apiParams = array();
		$apiParams['iid'] = $iid;
		$apiParams['fbml'] = $fbml;
		$apiParams['alid'] = $alid;
		$apiParams['lid'] = $lid;
		try
		{
			$faf = $this->initRest(new FavoritesSetFBML(), $apiParams, $uid, $appId);
			$faf->execute();
			$this->fail("Should have gotten an exception.");
		}catch(OpenFBAPIException $exception)
		{
			$this->assertEquals(FB_ERROR_CODE_INVALID_PARAMETER, $exception->getCode());
		}
		
		// no more exceptions
		$apiParams = array();
		$apiParams['iid'] = $iid;
		$apiParams['fbml'] = $fbml;
		$apiParams['lid'] = $lid;
		try
		{
			$faf = $this->initRest(new FavoritesSetFBML(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "FavoritesSetFBML should not be null!");
			$faf->execute();
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have gotten an exception: " . $exception->getCode());
		}
		
		// no more exceptions
		$apiParams = array();
		$apiParams['iid'] = $iid;
		$apiParams['fbml'] = $fbml;
		$apiParams['alid'] = $alid;
		try
		{
			$faf = $this->initRest(new FavoritesSetFBML(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "FavoritesSetFBML should not be null!");
			$faf->execute();
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have gotten an exception: " . $exception->getCode());
		}
	}

	/*
     * Test the set and get of fbml on already created Favorites
     */
	public function testExecute()
	{
		$appId = 1;
		$app_id = 1;
		$uid = 18032;
		$iid = "Test Item FBML";
		$alid = 2;
		$fbml = "Some fbml to test with!";
		$lid = null;
		$uids = null;
		
		$apiParams = array();
		$apiParams['iid'] = $iid;
		$apiParams['fbml'] = $fbml;
		$apiParams['alid'] = $alid;
		
		try
		{
			// Set the FBML
			$faf = $this->initRest(new FavoritesSetFBML(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "FavoritesSet should not be null!");
			
			Api_Dao_Items::createItem($app_id, $iid, "", "", 0);
			CoreApiUtil::createFavorite($app_id, $uid, $iid, $alid, $lid);
			
			$result = $faf->execute();
			$this->assertEquals($result['result'], '1');
			
			$faf = $this->initRest(new FavoritesGetFBML(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "FavoritesGetFBML should not be null!");
			
			$retVal = $faf->execute();
			$a = $retVal['favorite'][1];
			$this->assertEquals($fbml, $a['fbml']);
			
			// Now test passin in the UID	
			$uids = "18032";
			$apiParams['uids'] = $uids;
			
			$faf = $this->initRest(new FavoritesGetFBML(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "FavoritesGetFBML should not be null!");
			
			$retVal = $faf->execute();
			$a = $retVal['favorite'][1];
			$this->assertEquals($a['fbml'], $fbml);
		}catch(OpenFBAPIException $exception)
		{
			$this->fail($exception->getMessage() . "\n" . $exception->getTraceAsString());
		}
	}
}

?>
