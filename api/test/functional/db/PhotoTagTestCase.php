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
require_once ("ringside/api/dao/PhotoTag.php");
require_once ("ringside/api/dao/records/RingsidePhoto.php");
require_once ("ringside/api/dao/records/RingsidePhotoTag.php");

require_once ("RsOpenFBDbTestUtils.php");

class PhotoTagTestCase extends BaseDbTestCase
{

	private function getNumAllPhotoTags()
	{
		$q = Doctrine_Query::create();
		$q->select('COUNT(ptid) AS pid_count')->from('RingsidePhotoTag');
		$ret = $q->execute();
		
		return $ret[0]['pid_count'];
	}

	private function getPhotoTag($id)
	{
		$q = Doctrine_Query::create();
		$q->from('RingsidePhotoTag')->where("ptid = $id");
		$ret = $q->execute();
		
		return $ret[0]->toArray();
	}

	public function testautoincrementIntoDb()
	{
		$aid = 77;
		$pid = Api_Dao_Photo::createPhoto($aid, 'a caption', '', 2, '', '', '');		
		$this->assertTrue($pid !== false);
				
		$subjectId = 2;
		$text = "cvsUrl";
		$xcoord = 21;
		$ycoord = 43;
		
		$id;
		try
		{
			$numRows = $this->getNumAllPhotoTags();
			$id = Api_Dao_PhotoTag::createPhotoTag($pid, $subjectId, $text, $xcoord, $ycoord, null);
			$this->assertEquals($numRows + 1, $this->getNumAllPhotoTags());
			$this->assertNotNull($id);
			$row = $this->getPhotoTag($id);
			
			$this->assertEquals($id, $row['ptid']);
			$this->assertEquals($pid, $row['pid']);
			$this->assertEquals($subjectId, $row['subject_id']);
			$this->assertEquals($text, $row['text']);
			$this->assertEquals($xcoord, $row['xcoord']);
			$this->assertEquals($ycoord, $row['ycoord']);
		}catch(Exception $exception)
		{
			throw $exception;
		}
		Api_Dao_PhotoTag::deletePhotoTag($id);
		$this->assertEquals($numRows, $this->getNumAllPhotoTags());
		
		Api_Dao_Photo::deletePhoto($pid);
	}

}
?>
