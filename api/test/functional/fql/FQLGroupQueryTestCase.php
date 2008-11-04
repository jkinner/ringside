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

require_once("BaseFQLTestCase.php");

class FQLGroupQueryTestCase extends BaseFQLTestCase
{
	public function testSimpleQuery()
	{
		$gid = 17800;
		$flds = array("gid","name","pic","pic_small","pic_big","description",
						  "group_type","group_subtype","recent_news",
						  "creator","update_time","office","website", "venue");
		$fql = "SELECT " . implode(",", $flds) . " FROM group WHERE gid=$gid";		
		$resp = $this->m_fqlEngine->query(17100, 17001, $fql);
		
		$this->assertTrue(array_key_exists("group", $resp));
		$this->assertTrue(is_array($resp["group"]));
		$this->assertEquals(1, count($resp["group"]));

		$g = $resp["group"][0];
		$this->assertEquals($gid, $g["gid"]);
		$this->assertEquals("group 1", $g["name"]);
		$this->assertEquals("http://localhost/pic.jpg", $g["pic"]);
		$this->assertEquals("http://localhost/smallpic.jpg", $g["pic_small"]);
		$this->assertEquals("http://localhost/bigpic.jpg", $g["pic_big"]);
		$this->assertEquals("this is group 1", $g["description"]);
		$this->assertEquals("Awesome Group", $g["group_type"]);
		$this->assertEquals("Awesome sub-group", $g["group_subtype"]);
		$this->assertEquals("No news is good news", $g["recent_news"]);
		$this->assertEquals(17001, $g["creator"]);
		$this->assertTrue(isset($g["update_time"]));
		$this->assertEquals("Suite 55", $g["office"]);
		$this->assertEquals("http://www.nowhere.com", $g["website"]);
		
		$v = $g["venue"];
		$this->assertEquals("Nowherapolis", $v["city"]);
		$this->assertEquals("ZZ", $v["state"]);
		$this->assertEquals("France", $v["country"]);
		$this->assertEquals("123 4th St.", $v["street"]);
		$this->assertEquals(55.6, $v["latitude"]);
		$this->assertEquals(38.1, $v["longitude"]);
	}	
}

?>
