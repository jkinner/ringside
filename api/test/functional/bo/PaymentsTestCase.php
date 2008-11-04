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

class PayementsTestCase extends PHPUnit_Framework_TestCase
{
	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetGateway()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetGateway()
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForCreateGateway()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testCreateGateway($type, $subject, $password)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetSubscriptionsByUidAndNidAndAid()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetSubscriptionsByUidAndNidAndAid($uid, $nid, $aid)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetFriendSubscriptionsByUidAndNidAndAid()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetFriendSubscriptionsByUidAndNidAndAid($friendid, $friendnid, $aid)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForCreatePlan()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testCreatePlan($aid, $planName, $price, $numFriends, $nid, $description, $length = 1, $unit = 'months')
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForDeletePlan()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testDeletePlan($planId)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetPlansByAid()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetPlansByAid($aid)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForGetSubscriptionsByUidNid()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testGetSubscriptionsByUidNid($uid, $nid)
	{
	
	}
}
?>
