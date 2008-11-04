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
require_once ("ringside/api/facebook/PhotosCreateAlbum.php");
require_once ('ringside/api/bo/Photos.php');

class PhotosCreateAlbumTestCase extends BaseAPITestCase
{

	public function testConstructor()
	{
		$uid = 123;
		
		// missing name
		$apiParams = array();
		try
		{
			$faf = $this->initRest(new PhotosCreateAlbum(), $apiParams, $uid);
			$this->fail("Should have gotten an exception.");
		}catch(OpenFBAPIException $exception)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode());
		}
		
		// no more exceptions
		

		$apiParams = array();
		$apiParams['api_key'] = "32";
		$apiParams['name'] = "crazy album name";
		
		$faf = $this->initRest(new PhotosCreateAlbum(), $apiParams, $uid);
		$this->assertEquals("crazy album name", $faf->getName());
		$this->assertEquals("", $faf->getLocation());
		$this->assertEquals("", $faf->getDescription());
		
		$apiParams = array();
		$apiParams['api_key'] = "32";
		$apiParams['name'] = "crazy album name";
		$apiParams['location'] = "loc";
		$apiParams['description'] = "desc";
		
		$faf = $this->initRest(new PhotosCreateAlbum(), $apiParams, $uid);
		$this->assertEquals("crazy album name", $faf->getName());
		$this->assertEquals("loc", $faf->getLocation());
		$this->assertEquals("desc", $faf->getDescription());
	}

	public function testExecute()
	{
		$uid = 10001;
		
		$apiParams = array();
		$apiParams['api_key'] = "32";
		$apiParams['name'] = "crazy album name";
		$apiParams['location'] = "loc";
		$apiParams['description'] = "desc";
		
		$faf = $this->initRest(new PhotosCreateAlbum(), $apiParams, $uid);
		$result = $faf->execute();
		$aid = $result[FB_PHOTOS_AID];
		$link = Api_Bo_Photos::createAlbumLink($aid, $uid);
		
		try
		{
			$this->assertEquals(10, count($result));
			$this->assertTrue($result[FB_PHOTOS_AID] > 0);
			$this->assertEquals(0, $result[FB_PHOTOS_COVER_PID]);
			$this->assertEquals(10001, $result[FB_PHOTOS_OWNER]);
			$this->assertEquals("crazy album name", $result[FB_PHOTOS_NAME]);
			$this->assertEquals($result[FB_PHOTOS_CREATED], $result[FB_PHOTOS_MODIFIED]);
			$this->assertEquals("desc", $result[FB_PHOTOS_DESCRIPTION]);
			$this->assertEquals("loc", $result[FB_PHOTOS_LOCATION]);
			$this->assertEquals($link, $result[FB_PHOTOS_LINK]);
			$this->assertEquals(0, $result[FB_PHOTOS_SIZE]);
			
			// make sure the db is populated.
			$dbRes = $this->getAlbum();
			
			$this->assertEquals($dbRes['aid'], $result[FB_PHOTOS_AID]);
			$this->assertEquals($dbRes['cover_pid'], $result[FB_PHOTOS_COVER_PID]);
			$this->assertEquals($dbRes['owner'], $result[FB_PHOTOS_OWNER]);
			$this->assertEquals($dbRes['name'], $result[FB_PHOTOS_NAME]);
			$this->assertEquals($dbRes[description], $result[FB_PHOTOS_DESCRIPTION]);
			$this->assertEquals($dbRes['location'], $result[FB_PHOTOS_LOCATION]);
		}catch(Exception $exc)
		{
			try
			{
				$this->removeAlbum($aid);
			}catch(Exception $e2)
			{
			}
			throw $exc;
		}
		
		$this->removeAlbum($aid);
	}

	private function removeAlbum($aid)
	{
		Api_Dao_Album::delete($aid);
	}

	private function getAlbum()
	{
		$q = Doctrine_Query::create();
		$q->from('RingsideAlbum')->where("name = 'crazy album name' AND owner = 10001");
		$albums = $q->execute();
		return $albums[0]->toArray();
	}
}

?>
