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
require_once ("ringside/api/facebook/PhotosGetTags.php");

class PhotosGetTagsTestCase extends BaseAPITestCase
{

	public function testConstructor()
	{
		$uid = 123;
		
		// missing pids
		$apiParams = array();
		try
		{
			$faf = $this->initRest(new PhotosGetTags(), $apiParams, $uid);
			$this->fail("Should have gotten an exception.");
		}catch(OpenFBAPIException $exception)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode());
		}
		
		// empty pids
		$apiParams = array();
		$apiParams['pids'] = "";
		$apiParams['api_key'] = "32";
		try
		{
			$faf = $this->initRest(new PhotosGetTags(), $apiParams, $uid);
			$this->fail("Should have gotten an exception.");
		}catch(OpenFBAPIException $exception)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode());
		}
		
		// no more exceptions
		

		$apiParams = array();
		$apiParams['pids'] = "56, 57, 58";
		$apiParams['api_key'] = "32";
		$faf = $this->initRest(new PhotosGetTags(), $apiParams, $uid);
	}

	private function assertPhotoTag($row, $pid, $subjectId, $xcoord, $ycoord, $created)
	{
		$this->assertEquals($pid, $row[FB_PHOTOS_PID]);
		$this->assertEquals($subjectId, $row[FB_PHOTOS_SUBJECT]);
		$this->assertEquals($xcoord, $row[FB_PHOTOS_XCOORD]);
		$this->assertEquals($ycoord, $row[FB_PHOTOS_YCOORD]);
	}

	public function testExecute()
	{
		$photo1 = RsOpenFBDbTestUtils::getPhoto1();
		$photo2 = RsOpenFBDbTestUtils::getPhoto2();
		
		$uid = 10001;
		
		$apiParams = array();
		$apiParams['api_key'] = "32";
		$apiParams['pids'] = 10001;
		$faf = $this->initRest(new PhotosGetTags(), $apiParams, $uid);
		$result = $faf->execute();
		
		$apiParams = array();
		$apiParams['api_key'] = "32";
		$apiParams['pids'] = $photo1->pid;
		$faf = $this->initRest(new PhotosGetTags(), $apiParams, $uid);
		
		$result = $faf->execute();
		$fi = $result[FB_PHOTOS_PHOTO_TAG];
		$this->assertEquals(2, count($fi));
		$row = $fi[0];
		$this->assertPhotoTag($row, $photo1->pid, "10001", 1.1, 1.2, 11);
		$row = $fi[1];
		$this->assertPhotoTag($row, $photo1->pid, "10002", 2.1, 2.2, 22);
		
		$apiParams = array();
		$apiParams['api_key'] = "32";
		$apiParams['pids'] = $photo2->pid;
		$faf = $this->initRest(new PhotosGetTags(), $apiParams, $uid);
		
		$result = $faf->execute();
		$fi = $result[FB_PHOTOS_PHOTO_TAG];
		$this->assertEquals(1, count($fi));
		$row = $fi[0];
		$this->assertPhotoTag($row, $photo2->pid, "10001", 3.1, 3.2, 33);
		
		$apiParams = array();
		$apiParams['api_key'] = "32";
		$apiParams['pids'] = $photo1->pid . ", " . $photo2->pid;
		$faf = $this->initRest(new PhotosGetTags(), $apiParams, $uid);
		
		$result = $faf->execute();
		$fi = $result[FB_PHOTOS_PHOTO_TAG];
		$this->assertEquals(3, count($fi));
		$row = $fi[0];
		$this->assertPhotoTag($row, $photo1->pid, "10001", 1.1, 1.2, 11);
		$row = $fi[1];
		$this->assertPhotoTag($row, $photo1->pid, "10002", 2.1, 2.2, 22);
		$row = $fi[2];
		$this->assertPhotoTag($row, $photo2->pid, "10001", 3.1, 3.2, 33);
	}

}
?>
