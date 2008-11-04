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

class PagesBoTestCase extends PHPUnit_Framework_TestCase
{

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForIsFan()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testIsFan($loggedInUser, $uid, $pageId)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForIsAdmin()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testIsAdmin($uid, $pageId)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForHasApp()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testHasApp($pageId, $appId)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetPages()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetPages($fields, $loggedInUser, $uid, $pageIds)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetPagesInfo()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetPagesInfo($fields, $loggedInUser, $uid, $pageIds, $appId)
	{
	
	}
}
?>
