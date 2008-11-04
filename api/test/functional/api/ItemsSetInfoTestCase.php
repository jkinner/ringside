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
require_once ('ringside/rest/ItemsSetInfo.php');
require_once ('ringside/api/dao/Items.php');

/**
 * @author Mark Lugert mlugert@ringsidenetworks.com
 */
class ItemsSetInfoTestCase extends BaseAPITestCase
{

	/*
	 * Test the creation of the Object
	 */
	public function testConstructor()
	{
		$appId = 6;
		$uid = 18033;
		$datatype = 5;
		$iid = "TestItemSetInfo14";
		$url = "http://www.ringsidenetworks.com";
		$refurl = "http://www.ringsidenetworks.com";
		
		$apiParams = array();
		$apiParams['datatype'] = $datatype;
		try
		{
			$faf = $this->initRest(new ItemsSetInfo(), $apiParams, $uid, $appId);
		}catch(Exception $e)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $e->getCode());
		}
		
		$apiParams = array();
		$apiParams['datatype'] = $datatype;
		$apiParams['iid'] = $iid;
		try
		{
			$faf = $this->initRest(new ItemsSetInfo(), $apiParams, $uid, $appId);
		}catch(Exception $e)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $e->getCode());
		}
		
		$apiParams = array();
		$apiParams['datatype'] = $datatype;
		$apiParams['iid'] = $iid;
		$apiParams['url'] = $url;
		try
		{
			$faf = $this->initRest(new ItemsSetInfo(), $apiParams, $uid, $appId);
		}catch(Exception $e)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $e->getCode());
		}
		
		$apiParams = array();
		$apiParams['datatype'] = $datatype;
		$apiParams['iid'] = $iid;
		$apiParams['url'] = $url;
		$apiParams['refurl'] = $refurl;
		try
		{
			$faf = $this->initRest(new ItemsSetInfo(), $apiParams, $uid, $appId);
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
		$appId = 67;
		$app_id = 67;
		$uid = 18036;
		$datatype = 55;
		$iid = "TestItemSetInfo14";
		$url = "http://www.ringsidenetworks.com";
		$refurl = "http://www.ringsidenetworks.com";
		
		$iids = $iid;
		
		try
		{
			// without datatype
			$apiParams = array();
			$apiParams['iid'] = $iid;
			$apiParams['url'] = $url;
			$apiParams['refurl'] = $refurl;
			$faf = $this->initRest(new ItemsSetInfo(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "ItemsSetInfo should not be null!");
			
			// with datatype
			$apiParams = array();
			$apiParams['datatype'] = $datatype;
			$apiParams['iid'] = $iid;
			$apiParams['url'] = $url;
			$apiParams['refurl'] = $refurl;
			
			$faf = $this->initRest(new ItemsSetInfo(), $apiParams, $uid, $appId);
			$this->assertNotNull($faf, "ItemsSetInfo should not be null!");
			
			$result = $faf->execute();
			$this->assertEquals($result['result'], '1');
			
			$retVal = Api_Dao_Items::getInfo($app_id, $iids, $datatype);
			$a = $retVal[0]->toArray();
			
			$this->assertNotNull($a);
			$this->assertEquals($a['item_id'], $iid, "iid: " . $a['item_id'] . "!=" . $iid);
			$this->assertEquals($a['item_url'], $url, "url: " . $a['item_url'] . "!=" . $url);
			$this->assertEquals($a['item_refurl'], $refurl, "refurl: " . $a['item_refurl'] . "!=" . $refurl);
			$this->assertEquals($a['item_data_type'], $datatype, "datatype: " . $a['item_data_type'] . "!=" . $datatype);
		}catch(OpenFBAPIException $exception)
		{
			$this->fail($exception->getMessage() . "\n" . $exception->getTraceAsString());
		}
	}
}

?>
