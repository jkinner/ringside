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

class PhpClientFriendsTestCase extends BasePhpClientTestCase
{	
	public function testFriendsGet()
	{
		$flist = $this->fbClient->friends_get();
		$this->assertTrue(in_array(17002, $flist));
		$this->assertTrue(in_array(17003, $flist));
		$this->assertTrue(in_array(17004, $flist));
	}
	
	public function testFriendsAreFriends()
	{
		$arr = $this->fbClient->friends_areFriends(17001, 17002);
		$areFriends = $arr[0];
		$this->assertEquals($areFriends["uid1"], "17001");
		$this->assertEquals($areFriends["uid2"], "17002");
		$this->assertEquals($areFriends["are_friends"], "1");
		
		$arr = $this->fbClient->friends_areFriends(17001, 17003);
		$areFriends = $arr[0];
		$this->assertEquals($areFriends["uid1"], "17001");
		$this->assertEquals($areFriends["uid2"], "17003");
		$this->assertEquals($areFriends["are_friends"], "1");
		
		$arr = $this->fbClient->friends_areFriends(17001, 17004);
		$areFriends = $arr[0];
		$this->assertEquals($areFriends["uid1"], "17001");
		$this->assertEquals($areFriends["uid2"], "17004");
		$this->assertEquals($areFriends["are_friends"], "1");
		
		$arr = $this->fbClient->friends_areFriends(17001, 17999);
		$areFriends = $arr[0];
		$this->assertEquals($areFriends["uid1"], "17001");
		$this->assertEquals($areFriends["uid2"], "17999");
		$this->assertEquals($areFriends["are_friends"], "0");		
	}
	
	public function testFriendsAreAppUsers()
	{
		$arr = $this->fbClient->friends_getAppUsers();
		$this->assertTrue(in_array(17002, $arr));
		$this->assertTrue(in_array(17003, $arr));
		$this->assertTrue(!in_array(17005, $arr));
	}
}
?>
