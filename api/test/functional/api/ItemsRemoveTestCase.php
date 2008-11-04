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
require_once ('ringside/rest/ItemsRemove.php');
require_once ('ringside/api/dao/Items.php');

/**
 * @author Mark Lugert mlugert@ringsidenetworks.com
 */
class ItemsRemoveTestCase extends BaseAPITestCase
{

	/*
	 * Test the creation of the Object
	 */
	public function testConstructor()
	{
		$appId = 1;
		$uid = 18033;
		$iid = "TestItemGetInfo1";
		
		$apiParams = array();
		try
		{
			$faf = $this->initRest(new ItemsRemove(), $apiParams, $uid, $appId);
		}catch(Exception $e)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $e->getCode());
		}
		
		$apiParams = array();
		$apiParams['iid'] = $iid;
		try
		{
			$faf = $this->initRest(new ItemsRemove(), $apiParams, $uid, $appId);
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
		$appId = 2;
		$app_id = 2;
		$uid = 18034;
		$datatype = 1;
		$url = "http://ringsidenetworks.com";
		$refurl = "http://ringsidenetworks.com";
		$iid = "TestItemRemove5";
		$iids = $iid;
		
		try
		{
			$apiParams = array();
			$apiParams['iid'] = $iid;
			
			$faf = $this->initRest(new ItemsRemove(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "ItemsRemove should not be null!");
			
			Api_Dao_Items::createItem($app_id, $iid, $url, $refurl, $datatype);
			
			$result = $faf->execute();
			$this->assertEquals($result['result'], '1');
			
			$retVal = Api_Dao_Items::getInfo($app_id, $iids, $datatype);
			
			$this->assertEquals(count($retVal), 0, "GetInfo Count: " . count($retVal) . "!=0");
		}catch(OpenFBAPIException $exception)
		{
			$this->fail($exception->getMessage() . "\n" . $exception->getTraceAsString());
		}
	}
}

?>
