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
require_once ("ringside/api/dao/Album.php");
require_once ("db/RsOpenFBDbTestUtils.php");

class AlbumTestCase extends BaseDbTestCase
{
	public function getNumAlbums($id)
	{
		$q = Doctrine_Query::create();
		$q->select('count(aid) as aid_count')->from('RingsideAlbum')->where("aid = $id");
		$ret = $q->execute();
		
		return $ret[0]['aid_count'];
	}

	private function getAlbum($id)
	{
		$q = Doctrine_Query::create();
		$q->from('RingsideAlbum')->where("aid = $id");
		$albums = $q->execute();
		return $albums[0]->toArray();
	}

	private function getNumAllAlbums()
	{
		$q = Doctrine_Query::create();
		$q->select('count(aid) as aid_count')->from('RingsideAlbum');
		$ret = $q->execute();
		
		return $ret[0]['aid_count'];
	}

	public function testinsertIntoDbdeleteFromDb()
	{
		$aid = 9999234;
		$coverPid = 5;
		$created = 345;
		$description = "cvsUrl";
		$location = "234";
		$modified = 234;
		$name = "cvsUrl";
		$owner = 543;
		
		try
		{
			$this->assertEquals(0, $this->getNumAlbums($aid));
			//$aid = Api_Dao_Album::createAlbum($coverPid, $created, $description, $location, $modified, $name, $owner);
			$aid = Api_Dao_Album::createAlbum($coverPid, $description, $location, $name, $owner);
			$this->assertEquals(1, $this->getNumAlbums($aid));
			$row = $this->getAlbum($aid);
			$this->assertEquals($aid, $row['aid']);
			$this->assertEquals($coverPid, $row['cover_pid']);
			$this->assertEquals($description, $row['description']);
			$this->assertEquals($location, $row['location']);
			$this->assertEquals($name, $row['name']);
			$this->assertEquals($owner, $row['owner']);
		}catch(Exception $exception)
		{
			Api_Dao_Album::delete($aid);
			throw $exception;
		}
		Api_Dao_Album::delete($aid);
		$this->assertEquals(0, $this->getNumAlbums($aid));
	}

	public function testautoincrementIntoDb()
	{
		$coverPid = 5;
		$created = 345;
		$description = "cvsUrl";
		$location = "234";
		$modified = 234;
		$name = "cvsUrl";
		$owner = 543;
		
		$aid = null;
		try
		{
			$numRows = $this->getNumAllAlbums();
			//$aid = Api_Dao_Album::createAlbum($coverPid, $created, $description, $location, $modified, $name, $owner);
			$aid = Api_Dao_Album::createAlbum($coverPid, $description, $location, $name, $owner);
			$this->assertTrue($aid != false);
			$this->assertEquals($numRows + 1, $this->getNumAllAlbums());
			$row = $this->getAlbum($aid);
			$this->assertEquals($aid, $row['aid']);
			$this->assertEquals($coverPid, $row['cover_pid']);			
			$this->assertEquals($description, $row['description']);
			$this->assertEquals($location, $row['location']);			
			$this->assertEquals($name, $row['name']);
			$this->assertEquals($owner, $row['owner']);
		}catch(Exception $exception)
		{
			Api_Dao_Album::delete($aid);
			throw $exception;
		}
		Api_Dao_Album::delete($aid);
		$this->assertEquals($numRows, $this->getNumAllAlbums());
	}

}
?>
