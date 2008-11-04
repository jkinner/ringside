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
require_once ('ringside/api/facebook/OpenFBConstants.php');
require_once ("ringside/api/OpenFBAPIException.php");
require_once ("ringside/api/DefaultRest.php");
require_once ("ringside/api/bo/Notification.php");
require_once ("ringside/api/bo/Friends.php");
/**
 * API to send email to friends of user of application.
 * Currently the API only inserts records into the database for processing.
 * This will allow testing and better back end processes to send emails from separate process.
 * There is a hook already in place if you want this process to just send emails as well.
 *
 * @author Richard Friedman
 */
class NotificationsSendEmail extends Api_DefaultRest
{
	private $recipients;
	private $subject;
	private $message;
	private $fbml;

	public function validateRequest()
	{
		$this->recipients = explode(",", $this->getRequiredApiParam('recipients'));
		$this->subject = $this->getRequiredApiParam('subject');
		$text = $this->getApiParam('text', null);
		$fbml = $this->getApiParam('fbml', null);
		if(! empty($text))
		{
			$this->fbml = false;
			$this->message = $text;
		}else if(! empty($fbml))
		{
			$this->fbml = true;
			$this->message = $fbml;
		}else
		{
			throw new OpenFBAPIException("Missing either text or fbml.", FB_ERROR_CODE_PARAMETER_MISSING);
		}
	}

	public function execute()
	{
		$response = array();
		// can only be sent to friends
		$friends = Api_Bo_Friends::getFriends($this->getUserId());
		$sendFriends = array_intersect($this->recipients, $friends);
		if(count($sendFriends) > 0)
		{
			// get Email address for each friend.
			$response['result'] = implode(",", $sendFriends);
			$mailid = Api_Bo_Notification::createMail($this->getUserId(), $this->subject);
			
			foreach($sendFriends as $friend)
			{
				Api_Bo_Notification::addUserToMail($mailid, $friend);
			}
			Api_Bo_Notification::addMessage($mailid, $this->getUserId(), $this->message, 1);
			
			Api_Bo_Notification::sendEmail($this->getUserId(), $sendFriends, $this->subject, $this->message);
		}
		return $response;
	}
}
?>
