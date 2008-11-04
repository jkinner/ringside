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
require_once ("ringside/api/facebook/NotificationsGet.php");

define('NOTIFGET_USER_NOTHING', '1');
define('NOTIFGET_USER_NOTIFICATIONS', '12004');
define('NOTIFGET_USER_INVITES', '12009');
define('NOTIFGET_USER_EVERYTHING', '12010');
define('NOTIFGET_USER_POKES', '12001');
define('NOTIFGET_USER_MOSTRECENT_POKES', '12005');
define('NOTIFGET_USER_SHARES', '12002');
define('NOTIFGET_USER_MESSAGES', '12003');
define('NOTIFGET_USER_FRIEND_REQUESTS', '12007');
define('NOTIFGET_USER_EVENTS', '12006');
define('NOTIFGET_USER_GROUPS', '12008');

define('NOTIFGET_SHARE_MOSTRECENT', '12104');
define('NOTIFGET_SHARE_ALL_MOSTRECENT', '12107');
define('NOTIFGET_MAIL_MOSTRECENT', '12080');
define('NOTIFGET_NOTIF_MOSTRECENTSHARE', '12105');

define('NOTIFGET_USER_EVENTS_LIST', '12901,12902,12903,12904');
define('NOTIFGET_USER_GROUPS_LIST', '12801,12802,12803');
define('NOTIFGET_USER_FRIENDS_LIST', '12002,12003,12004');

define('NOTIFGET_USER_INVITES_EVENTS', '12905,12906,12907');

class NotificationsGetTestCase extends BaseAPITestCase
{

	public function testConstructor()
	{
		try
		{
			$notif = new NotificationsGet(NOTIFGET_USER_NOTHING, array());
			$this->assertNotNull($notif, " Object not really created");
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Exception should have not be thrown " . $exception);
		}
	
	}

	public static function providerTestExecute()
	{
		
		$compare = array();
		$compare['pokes'] = array('unread' => '0', 'most_recent' => '0');
		$compare['shares'] = array('unread' => '0', 'most_recent' => '0');
		$compare['messages'] = array('unread' => '0', 'most_recent' => '0');
		$compare['event_invites'] = array('eid' => array());
		$compare['friend_requests'] = array('uid' => array());
		$compare['group_invites'] = array('gid' => array());
		
		$userNothing = $compare;
		
		$userPokes = $compare;
		$userPokes['pokes'] = array('unread' => '4', 'most_recent' => NOTIFGET_USER_MOSTRECENT_POKES);
		
		$userShares = $compare;
		$userShares['shares'] = array('unread' => '4', 'most_recent' => NOTIFGET_SHARE_MOSTRECENT);
		
		$userMsgs = $compare;
		$userMsgs['messages'] = array('unread' => '2', 'most_recent' => NOTIFGET_MAIL_MOSTRECENT);
		
		$userNotifs = $compare;
		$userNotifs['pokes'] = array('unread' => '1', 'most_recent' => NOTIFGET_USER_MOSTRECENT_POKES);
		$userNotifs['messages'] = array('unread' => '1', 'most_recent' => NOTIFGET_MAIL_MOSTRECENT);
		$userNotifs['shares'] = array('unread' => '1', 'most_recent' => NOTIFGET_NOTIF_MOSTRECENTSHARE);
		
		$userEvents = $compare;
		$userEvents['event_invites'] = array('eid' => explode(",", NOTIFGET_USER_EVENTS_LIST));
		
		$userGroups = $compare;
		$userGroups['group_invites'] = array('gid' => explode(",", NOTIFGET_USER_GROUPS_LIST));
		
		$userFriendReqs = $compare;
		$userFriendReqs['friend_requests'] = array('uid' => explode(",", NOTIFGET_USER_FRIENDS_LIST));
		
		$userInvites = $compare;
		$userInvites['event_invites'] = array('eid' => explode(",", NOTIFGET_USER_INVITES_EVENTS));
		$userInvites['group_invites'] = array('gid' => explode(",", NOTIFGET_USER_GROUPS_LIST));
		$userInvites['friend_requests'] = array('uid' => explode(",", NOTIFGET_USER_FRIENDS_LIST));
		
		$userAll = $compare;
		$userAll['event_invites'] = array('eid' => explode(",", NOTIFGET_USER_INVITES_EVENTS));
		$userAll['group_invites'] = array('gid' => explode(",", NOTIFGET_USER_GROUPS_LIST));
		$userAll['friend_requests'] = array('uid' => explode(",", NOTIFGET_USER_FRIENDS_LIST));
		$userAll['pokes'] = array('unread' => '1', 'most_recent' => NOTIFGET_USER_MOSTRECENT_POKES);
		$userAll['messages'] = array('unread' => '1', 'most_recent' => NOTIFGET_MAIL_MOSTRECENT);
		$userAll['shares'] = array('unread' => '1', 'most_recent' => NOTIFGET_SHARE_ALL_MOSTRECENT);
		
		return array(array(NOTIFGET_USER_NOTHING, $userNothing), array(NOTIFGET_USER_NOTIFICATIONS, $userNotifs), array(NOTIFGET_USER_INVITES, $userInvites), array(NOTIFGET_USER_EVERYTHING, $userAll), array(NOTIFGET_USER_POKES, $userPokes), array(NOTIFGET_USER_SHARES, $userShares), array(NOTIFGET_USER_MESSAGES, $userMsgs), array(NOTIFGET_USER_FRIEND_REQUESTS, $userFriendReqs), array(NOTIFGET_USER_EVENTS, $userEvents), array(NOTIFGET_USER_GROUPS, $userGroups));
	}

	/**
	 * @dataProvider providerTestExecute
	 */
	public function testExecute($uid, $compare)
	{
		try
		{
			$params = array();
			$method = $this->initRest(new NotificationsGet(), $params, $uid);
			$get = $method->execute();
			
			foreach($compare as $key=>$value)
			{
				foreach($value as $cKey=>$cValue)
				{
					if(is_array($cValue) && is_array($get[$key][$cKey]))
					{
						$this->assertSame(count($cValue), count($get[$key][$cKey]), "Arrays were not same size $key $cKey");
						foreach($cValue as $lValue)
						{
							$this->assertTrue(in_array($lValue, $get[$key][$cKey]), "Failed array testing $key $cKey $lValue");
						}
					}else
					{
						$this->assertTrue($cValue == $get[$key][$cKey], "Failed testing $key $cKey $cValue != {$get[$key][$cKey]}");
					}
				}
			}
		
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Exception should have not be thrown " . $exception);
		}
	}
}
?>
