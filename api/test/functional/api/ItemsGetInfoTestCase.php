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
require_once( 'ringside/api/OpenFBAPIException.php' );
require_once( 'ringside/rest/ItemsGetInfo.php' );
require_once( 'ringside/api/dao/Items.php');
require_once( 'ringside/api/dao/RingsideConstants.php');

/**
 * @author Mark Lugert mlugert@ringsidenetworks.com
 */
class ItemsGetInfoTestCase extends BaseAPITestCase 
{
	/*
	 * Test the creation of the Object
	 */
    public function testConstructor()
    {
    	$appId = 1;
        $uid = 18033;
        $datatype = 1;
		$iid = "TestItemGetInfo1";
        $iids = $iid;
		
    	$apiParams = array();
        try{
        	$faf = $this->initRest( new ItemsGetInfo(), $apiParams, $uid, $appId );
        	$this->assertNotNull($faf, "FavoritesGetFavoritesForUser should not be null!");
        } catch(OpenFBAPIException $exception)
        {
        	$this->fail("Should not have gotten an exception: ".$exception->getCode());
        }
        
    	$apiParams = array();
    	$apiParams['datatype'] = $datatype;
        try{
        	$faf = $this->initRest( new ItemsGetInfo(), $apiParams, $uid, $appId );
        	$this->assertNotNull($faf, "FavoritesGetFavoritesForUser should not be null!");
        } catch(OpenFBAPIException $exception)
        {
        	$this->fail("Should not have gotten an exception: ".$exception->getCode());
        }
        
    	$apiParams = array();
    	$apiParams['iids'] = $iids;
    	$apiParams['datatype'] = $datatype;
        try{
        	$faf = $this->initRest( new ItemsGetInfo(), $apiParams, $uid, $appId );
        	$this->assertNotNull($faf, "FavoritesGetFavoritesForUser should not be null!");
        } catch(OpenFBAPIException $exception)
        {
        	$this->fail("Should not have gotten an exception: ".$exception->getCode());
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
		$iid = "TestItemGetInfo2";
        $iids = $iid;
		
		try{
			// without datatype
			$apiParams = array();
	    	$apiParams['iids'] = $iids;
			$faf = $this->initRest( new ItemsGetInfo(), $apiParams, $uid, $appId );
	        $this->assertNotNull($faf, "ItemsGetInfo should not be null!");
	        
	        // with datatype
	    	$apiParams = array();
	    	$apiParams['iids'] = $iids;
	    	$apiParams['datatype'] = $datatype;

	        $faf = $this->initRest( new ItemsGetInfo(), $apiParams, $uid, $appId );
	        $this->assertNotNull($faf, "ItemsGetInfo should not be null!");
			
	        Api_Dao_Items::createItem($app_id, $iid, $url, $refurl, $datatype);
	        
	        $retVal = $faf->execute();
	        
	        $a = $retVal[ 'item' ][ 0 ];
	        
	        $this->assertNotNull($a);
            $this->assertEquals($a['iid'], $iid, "iid: ".$a['iid']."!=".$iid);
            $this->assertEquals($a['url'], $url, "url: ".$a['url']."!=".$url);
            $this->assertEquals($a['refurl'], $refurl, "refurl: ".$a['refurl']."!=".$refurl);
            $this->assertEquals($a['datatype'], $datatype, "datatype: ".$a['datatype']."!=".$datatype);
		} catch(OpenFBAPIException $exception)
        {
        	$this->fail($exception->getMessage()."\n".$exception->getTraceAsString());
        }
    }
}

?>
