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
require_once ("ringside/api/facebook/PhotosGetAlbums.php");

class PhotosGetAlbumsTestCase extends BaseAPITestCase
{

	public function testConstructor()
	{
		$uid = 123;
		
		// missing uid and aids
		$apiParams = array();
		$apiParams['api_key'] = "32";
		try
		{
			$faf = $this->initRest(new PhotosGetAlbums(), $apiParams, $uid);
			$this->fail("Should have gotten an exception.");
		}catch(OpenFBAPIException $exception)
		{
			$this->assertEquals(FB_ERROR_CODE_INCORRECT_SIGNATURE, $exception->getCode());
		}
		
		// no more exceptions
		

		$apiParams = array();
		$apiParams['api_key'] = "32";
		$apiParams['aids'] = "56, 57, 58";
		$faf = $this->initRest(new PhotosGetAlbums(), $apiParams, $uid);
		$this->assertEquals("", $faf->getAlbumUserId());
		$this->assertEquals("56, 57, 58", implode(",", $faf->getAids()));
		
		$apiParams = array();
		$apiParams['api_key'] = "32";
		$apiParams['uid'] = "10001";
		$faf = $this->initRest(new PhotosGetAlbums(), $apiParams, $uid);
		$this->assertEquals("10001", $faf->getAlbumUserId());
		$this->assertEquals(0, count($faf->getAids()));
		
		$apiParams = array();
		$apiParams['uid'] = "10001";
		$apiParams['api_key'] = "32";
		$apiParams['aids'] = "56, 57, 58";
		$faf = $this->initRest(new PhotosGetAlbums(), $apiParams, $uid);
		$this->assertEquals("10001", $faf->getAlbumUserId());
		$this->assertEquals("56, 57, 58", implode(",", $faf->getAids()));
	}

	private function assertAlbum($row, $albumId, $userId, $title, $desc, $loc, $numPics)
	{
		$this->assertEquals($albumId, $row[FB_PHOTOS_AID]);
		$this->assertEquals($userId, $row[FB_PHOTOS_OWNER]);
		$this->assertEquals($title, $row[FB_PHOTOS_NAME]);
		$this->assertEquals($desc, $row[FB_PHOTOS_DESCRIPTION]);
		$this->assertEquals($loc, $row[FB_PHOTOS_LOCATION]);
		$this->assertEquals(Api_Bo_Photos::createAlbumLink($albumId, $userId), $row[FB_PHOTOS_LINK]);
		$this->assertEquals($numPics, $row[FB_PHOTOS_SIZE]);
	}

	public function testExecute()
	{
		$albumId_1 = RsOpenFBDbTestUtils::getAlbum1();
		$albumId_2 = RsOpenFBDbTestUtils::getAlbum2();
		$uid = 10001;
		
		$apiParams = array();
		$apiParams['api_key'] = "32";
		$apiParams['uid'] = 10001;
		$faf = $this->initRest(new PhotosGetAlbums(), $apiParams, $uid);
		$result = $faf->execute();
		
		/* just aids
		$apiParams = array();
		$apiParams['api_key'] = "32";
		$apiParams['aids'] = $albumId_1 . ", " . $albumId_2;
		$faf = $this->initRest(new PhotosGetAlbums(), $apiParams, $uid);
		$result = $faf->execute();
		*/
		
		$fi = $result[FB_PHOTOS_ALBUM];
		$this->assertEquals(2, count($fi));
		$row = $fi[0];
		$this->assertAlbum($row, $row[FB_PHOTOS_AID], "10001", "pa 1", "desc 1", "loc 1", 2);
		$row = $fi[1];
		$this->assertAlbum($row, $row[FB_PHOTOS_AID], "10001", "pa 2", "desc 2", "loc 2", 1);
		
		// just uid
		$apiParams = array();
		$apiParams['api_key'] = "32";
		$apiParams['uid'] = 10001;
		$faf = $this->initRest(new PhotosGetAlbums(), $apiParams, $uid);
		
		$result = $faf->execute();
		$fi = $result[FB_PHOTOS_ALBUM];
		$this->assertEquals(2, count($fi));
		$row = $fi[0];
		$this->assertAlbum($row, $row[FB_PHOTOS_AID], "10001", "pa 1", "desc 1", "loc 1", 2);
		$row = $fi[1];
		$this->assertAlbum($row, $row[FB_PHOTOS_AID], "10001", "pa 2", "desc 2", "loc 2", 1);
		
		// both aids and uid
		$apiParams = array();
		$apiParams['api_key'] = "32";
		$apiParams['uid'] = 10001;
		$apiParams['aids'] = $albumId_1 . ", " . $albumId_2;
		$faf = $this->initRest(new PhotosGetAlbums(), $apiParams, $uid);
		
		$result = $faf->execute();
		$fi = $result[FB_PHOTOS_ALBUM];
		$this->assertEquals(2, count($fi));
		$row = $fi[0];
		$this->assertAlbum($row, $row[FB_PHOTOS_AID], "10001", "pa 1", "desc 1", "loc 1", 2);
		$row = $fi[1];
		$this->assertAlbum($row, $row[FB_PHOTOS_AID], "10001", "pa 2", "desc 2", "loc 2", 1);
		
		// both aids and uid
		$apiParams = array();
		$apiParams['api_key'] = "32";
		$apiParams['uid'] = 10001;
		$apiParams['aids'] = $albumId_1;
		$faf = $this->initRest(new PhotosGetAlbums(), $apiParams, $uid);
		
		$result = $faf->execute();
		$fi = $result[FB_PHOTOS_ALBUM];
		$this->assertEquals(1, count($fi));
		$row = $fi[0];
		$this->assertAlbum($row, $row[FB_PHOTOS_AID], "10001", "pa 1", "desc 1", "loc 1", 2);
		
		// both aids and uid
		$apiParams = array();
		$apiParams['api_key'] = "32";
		$apiParams['uid'] = 9999;
		$apiParams['aids'] = $albumId_1;
		$faf = $this->initRest(new PhotosGetAlbums(), $apiParams, $uid);
		
		$result = $faf->execute();
		$fi = $result[FB_PHOTOS_ALBUM];
		$this->assertEquals(0, count($fi));
	}

}
?>
