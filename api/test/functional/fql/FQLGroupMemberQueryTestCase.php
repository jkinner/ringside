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

class FQLGroupMemberQueryTestCase extends BaseFQLTestCase
{
	public function testSimpleQuery()
	{
		$gid = 17800;
		$fql = "SELECT uid FROM group_member WHERE gid=$gid";		
		$resp = $this->m_fqlEngine->query(17100, 17001, $fql);
		
		//print_r($resp);
		
		$this->assertTrue(array_key_exists("group_member", $resp));
		$this->assertEquals(4, count($resp["group_member"]));
		
		$this->assertEquals(17001, $resp["group_member"][0]["uid"]);
		$this->assertEquals(17002, $resp["group_member"][1]["uid"]);
		$this->assertEquals(17003, $resp["group_member"][2]["uid"]);
		$this->assertEquals(17004, $resp["group_member"][3]["uid"]);
		
		$gid = 17801;
		$fql = "SELECT uid FROM group_member WHERE gid=$gid";		
		$resp = $this->m_fqlEngine->query(17100, 17001, $fql);
		
		$this->assertTrue(array_key_exists("group_member", $resp));
		$this->assertEquals(2, count($resp["group_member"]));
		
		$this->assertEquals(17001, $resp["group_member"][0]["uid"]);
		$this->assertEquals(17002, $resp["group_member"][1]["uid"]);
		
		$uid = 17001;
		$fql = "SELECT gid FROM group_member WHERE uid=$uid";		
		$resp = $this->m_fqlEngine->query(17100, 17001, $fql);
		
		$this->assertTrue(array_key_exists("group_member", $resp));
		$this->assertEquals(2, count($resp["group_member"]));
		
		$this->assertEquals(17800, $resp["group_member"][0]["gid"]);
		$this->assertEquals(17801, $resp["group_member"][1]["gid"]);
	}	
}

?>
