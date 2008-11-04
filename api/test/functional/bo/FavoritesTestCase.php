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

class FavoritesTestCase extends PHPUnit_Framework_TestCase
{

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetUsers()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetUsers($app_id, $item_id, $alid, $lid, $uids)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetLists()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetLists($uid, $app_id)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForCreateList()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testCreateList($name, $app_id, $uid)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForDeleteFavorite()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testDeleteFavorite($app_id, $uid, $item_id, $alid, $lid)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetFavorites()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetFavorites($app_id, $uids, $alid, $lid)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForSetFavorite()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testSetFavorite($app_id, $uid, $item_id, $alid, $lid, $fbml)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetFBML()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetFBML($app_id, $item_id, $alid, $lid, $uids)
	{
	
	}

}
?>
