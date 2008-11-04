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

require_once ('BaseAPITestCase.php');
require_once ("ringside/api/OpenFBAPIException.php");
require_once ("ringside/api/facebook/NotificationsSend.php");
require_once ("ringside/api/facebook/NotificationsGet.php");

define('NOTIFSEND_USER_NOTHING', '12099');
define('NOTIFSEND_USER_SENDFROM', '12012');
define('NOTIFSEND_USER_SENDTO_1', '12013');
define('NOTIFSEND_USER_SENDTO_2', '12014');
define('NOTIFSEND_USER_NOTFRIEND', '12015');

class NotificationsSendTestCase extends BaseAPITestCase
{
	public static function providerTestConstructor()
	{
		return array(array(NOTIFSEND_USER_NOTHING, array(), false), array(NOTIFSEND_USER_NOTHING, array("notification" => "msg", "to_ids" => ''), true), array(NOTIFSEND_USER_NOTHING, array("notification" => "msg", "to_ids" => implode(",", array(NOTIFSEND_USER_NOTHING, NOTIFSEND_USER_NOTHING))), true));
	}

	/**
	 * @dataProvider providerTestConstructor
	 */
	public function testConstructor($uid, $params, $pass)
	{
		try
		{
			$notif = $this->initRest(new NotificationsSend(), $params, $uid);
			$this->assertTrue($pass, "This test should have failed with exception!");
			$this->assertNotNull($notif, "Object missing!");
		
		}catch(OpenFBAPIException $exception)
		{
			$this->assertFalse($pass, "This test should have not thrown an exception! " . $exception);
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode());
		}
	}

	public static function providerTestExecute()
	{
		return array(array(NOTIFSEND_USER_SENDFROM, "AppToMe", array(), true), array(NOTIFSEND_USER_SENDFROM, "MeToYou", array(NOTIFSEND_USER_SENDTO_1), true), array(NOTIFSEND_USER_SENDFROM, "MeToUs", array(NOTIFSEND_USER_SENDTO_1, NOTIFSEND_USER_SENDTO_2), true), array(NOTIFSEND_USER_SENDFROM, "MeToStranger", array(NOTIFSEND_USER_NOTFRIEND), false));
	}

	/**
	 * @dataProvider providerTestExecute
	 */
	public function testExecute($uid, $message, $to_ids, $checkFriends)
	{
		try
		{
			$compare = array();
			
			// get sending user pre-information
			$params = array();
			$method = $this->initRest(new NotificationsGet(), $params, $uid);
			$getResponse = $method->execute();
			$compare[$uid] = $getResponse['messages']['most_recent'];
			
			if(count($to_ids) > 0)
			{
				foreach($to_ids as $user)
				{
					$params = array();
					$method = $this->initRest(new NotificationsGet(), $params, $user);
					$getResponse = $method->execute();
					$compare[$user] = $getResponse['messages']['most_recent'];
				}
			}
			
			$params = array("notification" => $message, "to_ids" => implode(",", $to_ids));
			$method = $this->initRest(new NotificationsSend(), $params, $uid);
			$sendResponse = $method->execute();
			$this->assertSame(count($sendResponse), 0, "Result should have been EMPTY");
			
			$params = array();
			$method = $this->initRest(new NotificationsGet(), $params, $uid);
			$getResponse = $method->execute();
			$test = $getResponse['messages']['most_recent'];
			if(count($to_ids) > 0)
			{
				$this->assertEquals($compare[$uid], $test, "Most recent should not have changed for self $uid");
			}else
			{
				$this->assertGreaterThan($compare[$uid], $test, "Most recent sould have increased for self $uid");
			}
			
			if(count($to_ids) > 0)
			{
				foreach($to_ids as $user)
				{
					$params = array();
					$method = $this->initRest(new NotificationsGet(), $params, $user);
					$getResponse = $method->execute();
					$test = $getResponse['messages']['most_recent'];
					if($checkFriends)
					{
						$this->assertGreaterThan($compare[$user], $test, "Most recent should have changed for friend $user");
					}else
					{
						$this->assertEquals($compare[$user], $test, "Most recent should have not changed for stranger $user");
					}
				
				}
			}
		
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Exception should have not be thrown " . $exception->getTraceAsString());
		}
	}

}

?>
