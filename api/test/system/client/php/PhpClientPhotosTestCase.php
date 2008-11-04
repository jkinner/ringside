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

require_once("BasePhpClientTestCase.php");

class PhpClientPhotosTestCase extends BasePhpClientTestCase
{		
	public function testPhotosGetAlbums()
	{
		//$this->fbClient->printResponse = true;
		$arr = $this->fbClient->photos_getAlbums(17001, null);
		
		$al = $arr[0];
		$this->assertEquals($al["aid"], 17500);
		$this->assertEquals($al["name"], "test album 1");
		$this->assertEquals($al["owner"], 17001);
		//$this->assertEquals($al["created"], 123456);
		$this->assertEquals($al["description"], "test album 1 description");
		$this->assertEquals($al["location"], "antartica");
		$this->assertEquals(html_entity_decode($al["link"]), "http://www.ringside.com/album.php?aid=17500&id=17001");
		
		$al = $arr[1];
		$this->assertEquals($al["aid"], 17501);
		$this->assertEquals($al["name"], "test album 2");
		$this->assertEquals($al["owner"], 17001);
		//$this->assertEquals($al["created"], 1234567);
		$this->assertEquals($al["description"], "test album 2 description");
		$this->assertEquals($al["location"], "new z-land");
		$this->assertEquals(html_entity_decode($al["link"]), "http://www.ringside.com/album.php?aid=17501&id=17001");
		
		
		$this->fbClient->printResponse = false;
	}
	
	public function testPhotosCreateAlbum()
	{
		$res = $this->fbClient->call_method("facebook.photos.createAlbum",
														array("name" => "hot new album",
																"location" => "north pole",
																"description" => "bring a change of underwear"));
		$this->assertTrue(isset($res["aid"]));
		$aid = $res["aid"];
		$arr = $this->fbClient->photos_getAlbums(17001, $aid);
		
		$al = $arr[0];
		$this->assertEquals($al["aid"], $aid);
		$this->assertEquals($al["name"], "hot new album");
		$this->assertEquals($al["owner"], 17001);		
		$this->assertEquals($al["description"], "bring a change of underwear");
		$this->assertEquals($al["location"], "north pole");
	}
	
	public function testPhotosGet()
	{
		//$this->fbClient->printResponse = true;
		$res = $this->fbClient->photos_get(null, 17500, null);
		
		$this->assertEquals(count($res), 2);
		
		$p = $res[0];
		$this->assertEquals($p["pid"], 17520);
		$this->assertEquals($p["aid"], 17500);
		$this->assertEquals($p["owner"], 17001);
		$this->assertEquals($p["src"], "http://localhost/img1.jpg");
		$this->assertEquals($p["src_big"], "http://localhost/img1_big.jpg");
		$this->assertEquals($p["src_small"], "http://localhost/img1_small.jpg");
		$this->assertEquals($p["link"], "http://localhost/img1_link.jpg");
		$this->assertEquals($p["caption"], "image 1");		
		
		$p = $res[1];
		$this->assertEquals($p["pid"], 17521);
		$this->assertEquals($p["aid"], 17500);
		$this->assertEquals($p["owner"], 17001);
		$this->assertEquals($p["src"], "http://localhost/img2.jpg");
		$this->assertEquals($p["src_big"], "http://localhost/img2_big.jpg");
		$this->assertEquals($p["src_small"], "http://localhost/img2_small.jpg");
		$this->assertEquals($p["link"], "http://localhost/img2_link.jpg");
		$this->assertEquals($p["caption"], "image 2");		
				
		$res = $this->fbClient->photos_get(null, 17501, null);
		
		$this->assertEquals(count($res), 3);
		
		$p = $res[0];
		$this->assertEquals($p["pid"], 17522);
		$this->assertEquals($p["aid"], 17501);
		$this->assertEquals($p["owner"], 17001);
		$this->assertEquals($p["src"], "http://localhost/img3.jpg");
		$this->assertEquals($p["src_big"], "http://localhost/img3_big.jpg");
		$this->assertEquals($p["src_small"], "http://localhost/img3_small.jpg");
		$this->assertEquals($p["link"], "http://localhost/img3_link.jpg");
		$this->assertEquals($p["caption"], "image 3");		
		
		$p = $res[1];
		$this->assertEquals($p["pid"], 17523);
		$this->assertEquals($p["aid"], 17501);
		$this->assertEquals($p["owner"], 17001);
		$this->assertEquals($p["src"], "http://localhost/img4.jpg");
		$this->assertEquals($p["src_big"], "http://localhost/img4_big.jpg");
		$this->assertEquals($p["src_small"], "http://localhost/img4_small.jpg");
		$this->assertEquals($p["link"], "http://localhost/img4_link.jpg");
		$this->assertEquals($p["caption"], "image 4");
		
		$p = $res[2];
		$this->assertEquals($p["pid"], 17524);
		$this->assertEquals($p["aid"], 17501);
		$this->assertEquals($p["owner"], 17001);
		$this->assertEquals($p["src"], "http://localhost/img5.jpg");
		$this->assertEquals($p["src_big"], "http://localhost/img5_big.jpg");
		$this->assertEquals($p["src_small"], "http://localhost/img5_small.jpg");
		$this->assertEquals($p["link"], "http://localhost/img5_link.jpg");
		$this->assertEquals($p["caption"], "image 5");
		
		$this->fbClient->printResponse = false;
	}
	
	
	public function testPhotosGetTags()
	{
		$res = $this->fbClient->photos_getTags("17520");
		
		$t = $res[0];
		$this->assertEquals($t["pid"], 17520);
		$this->assertEquals($t["subject"], 17001);
		$this->assertEquals($t["xcoord"], 0);
		$this->assertEquals($t["ycoord"], 0);
		
		$t = $res[1];
		$this->assertEquals($t["pid"], 17520);
		$this->assertEquals($t["subject"], 17001);
		$this->assertEquals($t["xcoord"], 100);
		$this->assertEquals($t["ycoord"], 100);	
	}
	
	public function testPhotosAddTag()
	{
		//$this->fbClient->printResponse = true;
		$res = $this->fbClient->call_method("facebook.photos.addTag",
														array("pid" => "17524",
																"tag_uid" => "17001",																
																"x" => "57",
																"y" => "57"));
		$this->assertEquals($res, 1);
		
		$res = $this->fbClient->photos_getTags("17524");
		
		$t = $res[0];
		$this->assertEquals($t["pid"], 17524);
		$this->assertEquals($t["subject"], 17001);
		$this->assertEquals($t["xcoord"], 57);
		$this->assertEquals($t["ycoord"], 57);

		$this->fbClient->printResponse = false;
	}
}

?>
