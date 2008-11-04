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
require_once ('BaseDbTestCase.php');
require_once ("ringside/api/dao/Photo.php");
require_once ("ringside/api/dao/records/RingsidePhoto.php");
require_once ("RsOpenFBDbTestUtils.php");

class PhotoTestCase extends BaseDbTestCase
{

	private function getNumAllPhotos()
	{
		$q = Doctrine_Query::create();
		$q->select('COUNT(pid) AS pid_count')->from('RingsidePhoto');
		$ret = $q->execute();
		
		return $ret[0]['pid_count'];
	}

	private function getNumPhotos($id)
	{
		$q = Doctrine_Query::create();
		$q->select('COUNT(pid) AS pid_count')->from('RingsidePhoto')->where("pid = $id");
		$ret = $q->execute();
		
		return $ret[0]['pid_count'];
	}

	private function getPhoto($id)
	{
		$q = Doctrine_Query::create();
		$q->from('RingsidePhoto')->where("pid = $id");
		$photo = $q->execute();
		return $photo[0]->toArray(true);
	}

	public function testinsertIntoDbdeleteFromDb()
	{
		$aid = 234;
		$caption = "cap";
		$link = "cvsUrl";
		$owner = 543;
		$pid = 0;
		$src = "src";
		$srcBig = "srcBig";
		$srcSmall = "srcSmall";
		
		try
		{
			$this->assertEquals(0, $this->getNumPhotos($pid));
			$pid = Api_Dao_Photo::createPhoto($aid, $caption, $link, $owner, $src, $srcBig, $srcSmall, null);
			$this->assertEquals(1, $this->getNumPhotos($pid));
			$row = $this->getPhoto($pid);
			$this->assertEquals($aid, $row['aid']);
			$this->assertEquals($caption, $row['caption']);
			$this->assertEquals($link, $row['link']);
			$this->assertEquals($owner, $row['owner']);
			$this->assertEquals($pid, $row['pid']);
			$this->assertEquals($src, $row['src']);
			$this->assertEquals($srcBig, $row['src_big']);
			$this->assertEquals($srcSmall, $row['src_small']);
		}catch(Exception $exception)
		{
			Api_Dao_Photo::deletePhoto($pid);
			throw $exception;
		}
		Api_Dao_Photo::deletePhoto($pid);
		$this->assertEquals(0, $this->getNumPhotos($aid));
	}

	public function testautoincrementIntoDb()
	{
		$pid = null;
		$aid = 234;
		$caption = "cap";
		$link = "cvsUrl";
		$owner = 543;
		$src = "src";
		$srcBig = "srcBig";
		$srcSmall = "srcSmall";
		
		try
		{
			$numRows = $this->getNumAllPhotos();
			$pid = Api_Dao_Photo::createPhoto($aid, $caption, $link, $owner, $src, $srcBig, $srcSmall, null);
			$this->assertEquals($numRows + 1, $this->getNumAllPhotos());
			$this->assertNotNull($pid);
			$row = $this->getPhoto($pid);
			
			$this->assertEquals($aid, $row['aid']);
			$this->assertEquals($caption, $row['caption']);
			$this->assertEquals($link, $row['link']);
			$this->assertEquals($owner, $row['owner']);
			$this->assertEquals($pid, $row['pid']);
			$this->assertEquals($src, $row['src']);
			$this->assertEquals($srcBig, $row['src_big']);
			$this->assertEquals($srcSmall, $row['src_small']);
		}catch(Exception $exception)
		{
			Api_Dao_Photo::deletePhoto($pid);
			throw $exception;
		}
		Api_Dao_Photo::deletePhoto($pid);
		$this->assertEquals($numRows, $this->getNumAllPhotos());
	}

}
?>
