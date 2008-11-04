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
require_once ("ringside/api/facebook/PhotosAddTag.php");
require_once ('ringside/api/dao/PhotoTag.php');

class PhotosAddTagTestCase extends BaseAPITestCase
{
	public function testConstructor()
	{
		$apiParams = array();
		$uid = 123;
		$apiParams = array();
		// missing pid
		try
		{
			$faf = $this->initRest(new PhotosAddTag(), $apiParams, $uid);
			$this->fail("Should have gotten an exception.");
		}catch(OpenFBAPIException $exception)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode());
		}
		// missing api_key
		$apiParams = array();
		$apiParams['pid'] = 21;
		try
		{
			$faf = $this->initRest(new PhotosAddTag(), $apiParams, $uid);
			$this->fail("Should have gotten an exception.");
		}catch(OpenFBAPIException $exception)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode());
		}
		// tags
		$tags = '{"x":"30.0","y":"40.0","tag_uid":"1234567890"}, {"x":"70.0","y":"80.0","tag_text":"some person"}';
		$apiParams = array();
		$apiParams['pid'] = 21;
		$apiParams['api_key'] = "21";
		$apiParams['tags'] = $tags;
		$faf = $this->initRest(new PhotosAddTag(), $apiParams, $uid);
		$this->assertEquals(21, $faf->getPid());
		$this->assertEquals($tags, $faf->getTags());
		$this->assertNull($faf->getTagText());
		$this->assertNull($faf->getTagUid());
		$this->assertNull($faf->getX());
		$this->assertNull($faf->getY());
		// not tags
		$apiParams = array();
		$apiParams['pid'] = 21;
		$apiParams['api_key'] = "21";
		$apiParams['x'] = 10.1;
		$apiParams['y'] = 20.2;
		$apiParams['tag_uid'] = 12345;
		$apiParams['tag_text'] = "some person";
		try
		{
			$faf = $this->initRest(new PhotosAddTag(), $apiParams, $uid);
			$this->fail("Should have gotten an exception.");
		}catch(OpenFBAPIException $exception)
		{
			$this->assertEquals(FB_ERROR_CODE_INCORRECT_SIGNATURE, $exception->getCode());
		}
		$apiParams = array();
		$apiParams['pid'] = 21;
		$apiParams['api_key'] = "21";
		$apiParams['x'] = 10.1;
		$apiParams['y'] = 20.2;
		try
		{
			$faf = $this->initRest(new PhotosAddTag(), $apiParams, $uid);
			$this->fail("Should have gotten an exception.");
		}catch(OpenFBAPIException $exception)
		{
			$this->assertEquals(FB_ERROR_CODE_INCORRECT_SIGNATURE, $exception->getCode());
		}
		$apiParams = array();
		$apiParams['pid'] = 21;
		$apiParams['api_key'] = "21";
		$apiParams['x'] = 10.1;
		$apiParams['tag_uid'] = 12345;
		try
		{
			$faf = $this->initRest(new PhotosAddTag(), $apiParams, $uid);
			$this->fail("Should have gotten an exception.");
		}catch(OpenFBAPIException $exception)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode());
		}
		$apiParams = array();
		$apiParams['pid'] = 21;
		$apiParams['api_key'] = "21";
		$apiParams['y'] = 20.2;
		$apiParams['tag_uid'] = 12345;
		try
		{
			$faf = $this->initRest(new PhotosAddTag(), $apiParams, $uid);
			$this->fail("Should have gotten an exception.");
		}catch(OpenFBAPIException $exception)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode());
		}
		$apiParams = array();
		$apiParams['pid'] = 21;
		$apiParams['api_key'] = "21";
		$apiParams['x'] = 10.1;
		$apiParams['y'] = 20.2;
		$apiParams['tag_uid'] = 12345;
		$faf = $this->initRest(new PhotosAddTag(), $apiParams, $uid);
		$this->assertEquals(21, $faf->getPid());
		$this->assertNull($faf->getTags());
		$this->assertEquals(10.1, $faf->getX());
		$this->assertEquals(20.2, $faf->getY());
		$this->assertNull($faf->getTagText());
		$this->assertEquals(12345, $faf->getTagUid());
		$apiParams = array();
		$apiParams['pid'] = 21;
		$apiParams['api_key'] = "21";
		$apiParams['x'] = 10.1;
		$apiParams['y'] = 20.2;
		$apiParams['tag_text'] = "some person";
		$faf = $this->initRest(new PhotosAddTag(), $apiParams, $uid);
		$this->assertEquals(21, $faf->getPid());
		$this->assertNull($faf->getTags());
		$this->assertEquals(10.1, $faf->getX());
		$this->assertEquals(20.2, $faf->getY());
		$this->assertEquals("some person", $faf->getTagText());
		$this->assertNull($faf->getTagUid());
	}

	public function testExecute()
	{
		$photo = RsOpenFBDbTestUtils::getPhoto3();
		$uid = 10001;
		$pid = $photo->pid;

		$tags = '{"x":"30.0","y":"40.0","tag_uid":"1234567890"}, {"x":"70.0","y":"80.0","tag_text":"some person"}';
		$apiParams = array();
		$apiParams['pid'] = $pid;
		$apiParams['api_key'] = "21";
		$apiParams['tags'] = $tags;
		$faf = $this->initRest(new PhotosAddTag(), $apiParams, $uid);
		$res = $this->getNumPhotoTags($pid);
		$this->assertEquals(0, $res);
		try
		{
			$faf->execute();
			$res = $this->getNumPhotoTags($pid);
			$this->assertEquals(2, $res, 'The two objects are not equal!');
			
			$res = $this->getPhotoTags($pid);
			foreach($res as $row)
			{
				if(strcmp("", $row['subject_id']) == 0)
				{
					$this->assertNull($row['subject_id']);
					$this->assertEquals('some person', $row['text'], "Expecting some person, but got: " . $row['text']);
					$this->assertEquals(70.0, $row['xcoord']);
					$this->assertEquals(80.0, $row['ycoord']);
				}else
				{
					$this->assertEquals(1234567890, $row['subject_id']);
					$this->assertNull($row['text']);
					$this->assertEquals(30.0, $row['xcoord']);
					$this->assertEquals(40.0, $row['ycoord']);
				}
			}
		}catch(Exception $exc)
		{
			try
			{
				$this->deletePhotoTags($pid);
			}catch(Exception $e2)
			{
			}
			throw $exc;
		}
		$this->deletePhotoTags($pid);
		
		$apiParams = array();
		$apiParams['pid'] = $pid;
		$apiParams['api_key'] = "21";
		$apiParams['tag_uid'] = 1234567890;
		$apiParams['x'] = 30.0;
		$apiParams['y'] = 40.0;
		$faf = $this->initRest(new PhotosAddTag(), $apiParams, $uid);
		
		$res = $this->getNumPhotoTags($pid);
		$this->assertEquals(0, $res);
		try
		{
			$faf->execute();
			$res = $this->getNumPhotoTags($pid);
			$this->assertEquals(1, $res);
			$ret = $this->getPhotoTags($pid);
			$row = $ret[0];
			$this->assertEquals(1234567890, $row['subject_id']);
			$this->assertNull($row['text']);
			$this->assertEquals(30.0, $row['xcoord']);
			$this->assertEquals(40.0, $row['ycoord']);
			//                    $this->assertNull( $row[ 'subject_id' ] );
		//                    $this->assertEquals( "some person", $row[ 'text' ] );
		//                    $this->assertEquals( 70.0, $row[ 'xcoord' ] );
		//                    $this->assertEquals( 80.0, $row[ 'ycoord' ] );
		}catch(Exception $exc)
		{
			try
			{
				$this->deletePhotoTags($pid);
			}catch(Exception $e2)
			{
			}
			throw $exc;
		}
		$this->deletePhotoTags($pid);
		$apiParams = array();
		$apiParams['pid'] = $pid;
		$apiParams['api_key'] = "21";
		$apiParams['tag_text'] = "some person";
		$apiParams['x'] = 70.0;
		$apiParams['y'] = 80.0;
		$faf = $this->initRest(new PhotosAddTag(), $apiParams, $uid);
		$res = $this->getNumPhotoTags($pid);
		$this->assertEquals(0, $res);
		try
		{
			$faf->execute();
			$res = $this->getNumPhotoTags($pid);
			$this->assertEquals(1, $res);
			$res = $this->getPhotoTags($pid);
			$row = $res[0];
			$this->assertNull($row['subject_id']);
			$this->assertEquals("some person", $row['text']);
			$this->assertEquals(70.0, $row['xcoord']);
			$this->assertEquals(80.0, $row['ycoord']);
		}catch(Exception $exc)
		{
			try
			{
				$this->deletePhotoTags($pid);
			}catch(Exception $e2)
			{
			}
			throw $exc;
		}
		$this->deletePhotoTags($pid);
	}

	public function getNumPhotoTags($pid)
	{
		$q = Doctrine_Query::create();
		$q->select('count(pid) as pid_count')->from('RingsidePhotoTag')->where("pid = $pid");
		$ret = $q->execute();
		
		return $ret[0]['pid_count'];
	}

	public function getPhotoTags($pid)
	{
		$tags = Api_Dao_PhotoTag::getPhotoTags(array($pid));
		return $tags->toArray();
	}

	public function deletePhotoTags($pid)
	{
		$q = Doctrine_Query::create();
		$q->delete()->from('RingsidePhotoTag pt')->where("pid=$pid");
		$q->execute();
	}
}
?>
