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
require_once ('BaseDbTestCase.php');
require_once ("ringside/api/dao/UserAppSession.php");

class UserAppSessionTestCase extends BaseDbTestCase
{
	public static function provideForUserSession()
	{
		$tests = array();
		$tests[] = array("15151", "120201", "UserAppSession");
		
		return $tests;
	}

	/**
	 * @dataProvider provideForUserSession
	 */
	public function testUserSession($uid, $aid, $sessionKey)
	{
		try
		{
			$userAppSession = Api_Dao_UserAppSession::getUserAppSession($uid, $aid);
		}catch(Exception $exception)
		{
			$this->fail("Unexpected get none exception " . $exception);
		}
		
		$this->assertEquals(0, count($userAppSession), "There should be no existing user app sesion for uid ($uid) aid ($aid)");
		$infinite = 0;
		$ret = Api_Dao_UserAppSession::createUserAppSession($aid, $uid, $infinite, $sessionKey.'-1');
		$this->assertTrue($ret !== false);
		
		$userAppSession = null;
		try
		{
			$userAppSession = Api_Dao_UserAppSession::getUserAppSession($uid, $aid);
		}catch(Exception $exception)
		{
			$this->fail("Unexpected get first exception " . $exception);
		}
		
		$this->assertNotNull($userAppSession[0], "Inserted and should not be null");
		$this->assertEquals($uid, $userAppSession[0]->uid);
		$this->assertEquals($aid, $userAppSession[0]->aid);
		$this->assertEquals($sessionKey . "-1", $userAppSession[0]->session_key);
		$this->assertEquals(0, $userAppSession[0]->infinite);
		
		try
		{
			$ret = Api_Dao_UserAppSession::updateUserAppSession($aid, $uid, $infinite, $sessionKey.'-2');
			$this->assertGreaterThan(0, $ret);
		}catch(Exception $exception)
		{
			$this->fail("Unexpected updated exception " . $exception);
		}
		$userAppSession = null;
		
		try
		{
			$userAppSession = Api_Dao_UserAppSession::getUserAppSession($uid, $aid);
		}catch(Exception $exception)
		{
			$this->fail("Unexpected get updated exception " . $exception);
		}
		$this->assertNotNull($userAppSession[0], "Updated db, should not be null");
		$this->assertEquals($uid, $userAppSession[0]->uid);
		$this->assertEquals($aid, $userAppSession[0]->aid);
		$this->assertEquals($sessionKey . "-2", $userAppSession[0]->session_key);
		$this->assertEquals(0, $userAppSession[0]->infinite);
		
		try
		{
			$ret = Api_Dao_UserAppSession::deleteUserAppSession($aid, $uid);
			$this->assertEquals(1, $ret);
		}catch(Exception $exception)
		{
			$this->fail("Unexpected delete exception " . $exception);
		}
		$userAppSession = null;
		
		try
		{
			$userAppSession = Api_Dao_UserAppSession::getUserAppSession($uid, $aid);
		}catch(Exception $exception)
		{
			$this->fail("Unexpected get deleted  exception " . $exception);
		}
		$this->assertEquals(0, count($userAppSession), "Just deleted but still exists uid ($uid) aid ($aid)");
	}

}
?>
