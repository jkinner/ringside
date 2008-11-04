<?php
/*******************************************************************************
 * Ringside Networks, Harnessing the power of social networks.
 *
 * Copyright 2008 Ringside Networks, Inc., and individual contributors as indicated
 * by the @authors tag or express copyright attri ftware Foundation; either version 2.1 of
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

class FriendsTestCase extends PHPUnit_Framework_TestCase
{

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForSearchFriends()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testSearchFriends($userId, $query)
	{
	
	}
	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetFriendsAreFriends()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetFriendsAreFriends($uid1, $uid2)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetFriends()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetFriends($userId)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForCheckFriends()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testCheckFriends($loggedInUserId, $uid)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetAppUsers()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetAppUsers($userId, $apiKey)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForCreateFriend()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testCreateFriend($uid, $fuid)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForAcceptInvite()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testAcceptInvite($uid, $fuid, $status, $access)
	{
	
	}
}
?>
