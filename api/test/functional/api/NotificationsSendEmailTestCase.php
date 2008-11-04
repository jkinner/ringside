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
require_once ("ringside/api/facebook/NotificationsSendEmail.php");
require_once ("ringside/api/facebook/NotificationsGet.php");
define('NOTIFSENDEMAIL_USER_NOTHING', '12099');
define('NOTIFSENDMAIL_USER_SENDFROM', '12016');
define('NOTIFSENDMAIL_USER_SENDTO_1', '12013');
define('NOTIFSENDMAIL_USER_SENDTO_2', '12014');
define('NOTIFSENDMAIL_USER_NOTFRIEND', '12015');
class NotificationsSendEmailTestCase extends BaseAPITestCase
{
	public static function providerTestConstructor()
	{
		return array(array(NOTIFSENDEMAIL_USER_NOTHING, array(), false), array(NOTIFSENDEMAIL_USER_NOTHING, array("recipients" => "123", "text" => "test"), false), array(NOTIFSENDEMAIL_USER_NOTHING, array("recipients" => "123", "subject" => "test", "text" => "my test"), true), array(NOTIFSENDEMAIL_USER_NOTHING, array("recipients" => "123", "subject" => "test", "fbml" => "my test"), true));
	}
	/**
	 * @dataProvider providerTestConstructor
	 */
	public function testConstructor($uid, $params, $pass)
	{
		try
		{
			$notif = $this->initRest(new NotificationsSendEmail(), $params, $uid);
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
		return array(array(NOTIFSENDMAIL_USER_SENDFROM, "MeToYou", "messageToYou", array(NOTIFSENDMAIL_USER_SENDTO_1), true), array(NOTIFSENDMAIL_USER_SENDFROM, "MeToUs", "messageToUs", array(NOTIFSENDMAIL_USER_SENDTO_1, NOTIFSENDMAIL_USER_SENDTO_2), true), array(NOTIFSENDMAIL_USER_SENDFROM, "MeToStranger", "messageToThem", array(NOTIFSENDMAIL_USER_NOTFRIEND), false));
	}
	/**
	 * @dataProvider providerTestExecute
	 */
	public function testExecute($uid, $subject, $message, $toids, $checkFriends)
	{
		try
		{
			$compare = array();
			// get sending user pre-information
			$params = array();
			$method = $this->initRest(new NotificationsGet(), $params, $uid);
			$method->invites = array();
			$method->notifications = array("email" => "Email");
			$getResponse = $method->execute();
			$compare[$uid] = $getResponse['email']['most_recent'];
			if(count($toids) > 0)
			{
				foreach($toids as $user)
				{
					$params = array();
					$method = $this->initRest(new NotificationsGet(), $params, $user);
					$method->invites = array();
					$method->notifications = array("email" => "Email");
					$getResponse = $method->execute();
					$compare[$user] = $getResponse['email']['most_recent'];
				}
			}
			$params = array("subject" => $subject, "text" => $message, "recipients" => implode(",", $toids));
			$method = $this->initRest(new NotificationsSendEmail(), $params, $uid);
			$sendResponse = $method->execute();
			if($checkFriends)
			{
				$this->assertArrayHasKey('result', $sendResponse);
			}else
			{
				$this->assertArrayNotHasKey('result', $sendResponse);
			}
			$params = array();
			$method = $this->initRest(new NotificationsGet(), $params, $uid);
			$method->invites = array();
			$method->notifications = array("email" => "Email");
			$getResponse = $method->execute();
			$test = $getResponse['email']['most_recent'];
			if(count($toids) > 0)
			{
				$this->assertEquals($compare[$uid], $test, "Most recent should not have changed for self $uid");
			}else
			{
				$this->assertGreaterThan($compare[$uid], $test, "Most recent sould have increased for self $uid");
			}
			if(count($toids) > 0)
			{
				foreach($toids as $user)
				{
					$params = array();
					$method = $this->initRest(new NotificationsGet(), $params, $user);
					$method->invites = array();
					$method->notifications = array("email" => "Email");
					$getResponse = $method->execute();
					$test = $getResponse['email']['most_recent'];
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
