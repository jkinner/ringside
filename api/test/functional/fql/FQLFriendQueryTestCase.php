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

class FQLFriendQueryTestCase extends BaseFQLTestCase
{
	public function testFriendQuery()
	{
		$uid = 17001;
		$fql = "SELECT uid2 FROM friend WHERE uid1=$uid";
		$resp = $this->m_fqlEngine->query(17100, $uid, $fql);			
		
		$finfo = $resp["friend_info"];		
		$this->assertEquals(3, count($finfo));		
		$this->assertEquals("17002", $finfo[0]["uid2"]);
		$this->assertEquals("17003", $finfo[1]["uid2"]);
		$this->assertEquals("17004", $finfo[2]["uid2"]);
		
		$fql = "SELECT uid1 FROM friend WHERE uid2=$uid";
		$resp = $this->m_fqlEngine->query(17100, $uid, $fql);			
		
		$finfo = $resp["friend_info"];
		$this->assertEquals(3, count($finfo));		
		$this->assertEquals("17002", $finfo[0]["uid1"]);
		$this->assertEquals("17003", $finfo[1]["uid1"]);
		$this->assertEquals("17004", $finfo[2]["uid1"]);
	}
	
	public function testRestrictedAccess()
	{
		$uid = 17001;
		//can't see friends of 17005, 17001 is no friend of 17005,
		//and if they can't dance, then they ain't no friends of mine.
		$fql = "SELECT uid2 FROM friend WHERE uid1=17005";
		$resp = $this->m_fqlEngine->query(17100, $uid, $fql);
				
		$finfo = $resp["friend_info"];		
		$this->assertEquals(0, count($finfo));
	}
}

?>
