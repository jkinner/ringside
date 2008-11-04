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
require_once ('ringside/rest/FavoritesCreateList.php');
require_once ('ringside/rest/FavoritesGetLists.php');
require_once ('ringside/api/dao/Favorites.php');
require_once ('CoreApiUtil.php');

/**
 * @author Mark Lugert mlugert@ringsidenetworks.com
 */
class FavoritesListTestCase extends BaseAPITestCase
{

	/*
	 * Test the creation of the Object
	 */
	public function testCreateListConstructor()
	{
		$appId = 1;
		$uid = 18011;
		$name = 'Test List';
		
		$apiParams = array();
		try
		{
			$faf = $this->initRest(new FavoritesCreateList(), $apiParams, $uid, $appId);
		}catch(Exception $e)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $e->getCode());
		}
		
		$apiParams = array();
		$apiParams['name'] = $name;
		try
		{
			$faf = $this->initRest(new FavoritesCreateList(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "FavoritesGetFavoritesForUser should not be null!");
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have gotten an exception: " . $exception->getCode());
		}
	}

	public function testGetListConstructor()
	{
		$appId = 1;
		$uid = 18011;
		$uid2 = 18012;
		
		$apiParams = array();
		try
		{
			$faf = $this->initRest(new FavoritesGetLists(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "FavoritesGetFavoritesForUser should not be null!");
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have gotten an exception: " . $exception->getCode());
		}
		
		$apiParams = array();
		$apiParams['uid'] = $uid2;
		try
		{
			$faf = $this->initRest(new FavoritesGetLists(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "FavoritesGetFavoritesForUser should not be null!");
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
		$appId = 17;
		$uid = 18013;
		$name = "Test List";
		
		$apiParams = array();
		$apiParams['name'] = $name;
		
		try
		{
			// Test the create
			$faf = $this->initRest(new FavoritesCreateList(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "FavoritesCreateList should not be null!");
			
			// Execute our create
			$result = $faf->execute();
			$this->assertNotNull($result['result'], "assertNotNull should not be null!");
			$list_id = $result['result'];
			
			// Now test that we can get the list implicitly and explicitly
			$apiParams = array();
			$apiParams['uid'] = $uid;
			$faf = $this->initRest(new FavoritesGetLists(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "FavoritesGetLists should not be null!");
			
			$result = $faf->execute();
			$a = $result['list'][0];
			$this->assertEquals($a['name'], $name, "Result should be a $name");
			$this->assertEquals($a['id'], $list_id, "Result should be a $list_id");
		}catch(OpenFBAPIException $exception)
		{
			$this->fail($exception->getMessage() . "\n" . $exception->getTraceAsString());
		}
	
	}
}

?>
