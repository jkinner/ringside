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

class PhpClientGroupsTestCase extends BasePhpClientTestCase
{	
	public function testGroupsGet()
	{
		$res = $this->fbClient->groups_get(null, null);
		
		$g = $res[0];
		$this->assertEquals($g["gid"], 17800);
		$this->assertEquals($g["name"], "group 1");
		$this->assertEquals($g["nid"], 0);
		$this->assertEquals($g["description"], "this is group 1");
		$this->assertEquals($g["group_type"], "Awesome Group");
		$this->assertEquals($g["group_subtype"], "Awesome sub-group");
		$this->assertEquals($g["recent_news"], "No news is good news");
		$this->assertEquals($g["creator"], 17001);
		$this->assertEquals($g["office"], "Suite 55");
		$this->assertEquals($g["website"], "http://www.nowhere.com");
		$this->assertEquals($g["email"], "nobody@nowhere.com");
		
		$v = $g["venue"];
		$this->assertEquals($v["street"], "123 4th St.");
		$this->assertEquals($v["city"], "Nowherapolis");
		$this->assertEquals($v["state"], "ZZ");
		$this->assertEquals($v["country"], "France");
		
		$g = $res[1];
		$this->assertEquals($g["gid"], 17801);
		$this->assertEquals($g["name"], "group 2");
		$this->assertEquals($g["nid"], 0);				
		$this->assertEquals($g["creator"], 17002);	
	}
	
	public function testGroupsGetMembers()
	{
		$res = $this->fbClient->groups_getMembers("17800");
				
		$m = $res["members"];
		$this->assertTrue(in_array("17001", $m));
		$this->assertTrue(in_array("17002", $m));
		$this->assertTrue(!in_array("17003", $m));
		$this->assertTrue(!in_array("17004", $m));
		$this->assertTrue(!in_array("17005", $m));
				
		$a = $res["admins"];
		$this->assertTrue(in_array("17001", $a));
		$this->assertTrue(!in_array("17002", $a));
		$this->assertTrue(!in_array("17003", $a));
		$this->assertTrue(!in_array("17004", $a));
		$this->assertTrue(!in_array("17005", $a));
		
		$o = $res["officers"];
		$this->assertTrue(!in_array("17001", $o));
		$this->assertTrue(in_array("17002", $o));
		$this->assertTrue(!in_array("17003", $o));
		$this->assertTrue(in_array("17004", $o));
		$this->assertTrue(!in_array("17005", $o));
		
		$n = $res["not_replied"];
		$this->assertTrue(!in_array("17001", $n));
		$this->assertTrue(!in_array("17002", $n));
		$this->assertTrue(in_array("17003", $n));
		$this->assertTrue(in_array("17004", $n));
		$this->assertTrue(!in_array("17005", $n));
		
		$res = $this->fbClient->groups_getMembers("17801");
		
		$m = $res["members"];
		$this->assertTrue(in_array("17001", $m));
		$this->assertTrue(in_array("17002", $m));
		$this->assertTrue(!in_array("17003", $m));
		$this->assertTrue(!in_array("17004", $m));
		$this->assertTrue(!in_array("17005", $m));
				
		$a = $res["admins"];
		$this->assertTrue(!in_array("17001", $a));
		$this->assertTrue(in_array("17002", $a));
		$this->assertTrue(!in_array("17003", $a));
		$this->assertTrue(!in_array("17004", $a));
		$this->assertTrue(!in_array("17005", $a));
		
		$o = $res["officers"];
		$this->assertTrue(in_array("17001", $o));
		$this->assertTrue(!in_array("17002", $o));
		$this->assertTrue(!in_array("17003", $o));
		$this->assertTrue(!in_array("17004", $o));
		$this->assertTrue(!in_array("17005", $o));
		
		$n = $res["not_replied"];
		$this->assertTrue(!in_array("17001", $n));
		$this->assertTrue(!in_array("17002", $n));
		$this->assertTrue(!in_array("17003", $n));
		$this->assertTrue(!in_array("17004", $n));
		$this->assertTrue(!in_array("17005", $n));
	}
}

?>
