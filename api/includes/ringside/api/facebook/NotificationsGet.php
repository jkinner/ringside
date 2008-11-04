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
require_once ("ringside/api/OpenFBAPIException.php");
require_once ("ringside/api/DefaultRest.php");
define('NOTIFICATIONS_PLUGINS', 'ringside/api/notifications/');
/**
 * groups.get API
 *
 * @author Richard Friedman
 */
class NotificationsGet extends Api_DefaultRest
{
	public $notifications = array('messages' => 'Messages', 'pokes' => 'Pokes', 'shares' => 'Shares');
	public $invites = array('friend_requests' => 'FriendRequests', 'group_invites' => 'GroupInvites', 'event_invites' => 'EventInvites');
	/**
	 * Validate request.
	 */
	public function validateRequest()
	{
	}
	/**
	 *  notifications.get
	 */
	public function execute()
	{
		$response = array();
		// Loop notifications
		foreach($this->notifications as $key=>$className)
		{
			try
			{
				require_once (NOTIFICATIONS_PLUGINS . $className . '.php');
				$method = new $className();
				$response[$key] = $method->get($this->getUserId(), $this->getApiParams());
			}catch(Exception $exception)
			{
			    error_log("When retrieving notifications: {$exception->getMessage()}");
			    error_log($exception->getTraceAsString());
				$response[$key] = array('unread' => '0', 'most_recent' => '0');
			}
		}
		// Loop invites
		foreach($this->invites as $key=>$className)
		{
			try
			{
				require_once (NOTIFICATIONS_PLUGINS . $className . '.php');
				$method = new $className();
				$response[$key] = $method->get($this->getUserId(), $this->getApiParams());
			}catch(Exception $exception)
			{
				$response[$key] = '';
			}
		}
		return $response;
	}
}
/**
<!-- EXAMPLE Notifications Response 

<notifications_get_response >

  <messages><unread>2</unread><most_recent>1198083159</most_recent></messages>
  <pokes><unread>0</unread><most_recent>0</most_recent></pokes>
  <shares><unread>0</unread><most_recent>0</most_recent></shares>
  <friend_requests list="true">
    <uid>1052953040</uid>
  </friend_requests>
  <group_invites list="true">
    <gid>7921366986</gid>
  </group_invites>
  <event_invites list="true">
    <eid>15420435421</eid>
  </event_invites>

</notifications_get_response>
 -->
 **/
?>
