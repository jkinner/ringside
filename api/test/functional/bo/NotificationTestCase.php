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

class NotificationTestCase extends PHPUnit_Framework_TestCase
{

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForSendNotification()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testSendNotification($toids, $uid, $subject, $notification, $isEmail)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForSendEmail()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testSendEmail($from, $to, $subject, $message)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForCreateMail()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testCreateMail($userId, $subject)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForAddMessage()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testAddMessage($mailid, $uid, $message, $isEmail)
	{
	
	}

	/**
	 * Use providers to test many different scenarios
	 */
	public static function provideForAddUserToMail()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	public function testAddUserToMail($mailid, $uid)
	{
	
	}
}
?>
