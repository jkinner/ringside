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

require_once 'ringside/api/dao/Notification.php';
require_once ('ringside/api/dao/Friends.php');
require_once ('ringside/api/dao/UserProfile.php');

/**
 * @author mlugert@ringsidenetworks.com
 */
class Api_Bo_Notification
{
	/**
	 * Sends an email notification
	 *
	 * @param Array $toids
	 * @param Array $uid
	 * @param string $notification
	 */
	public static function sendNotification($toids, $uid, $subject, $notification, $isEmail)
	{
		if($toids == null)
		{
			$mailid = Api_Dao_Notification::createMail($uid, $subject);
			Api_Dao_Notification::addUserToMail($mailid, $uid);
			Api_Dao_Notification::addMessage($mailid, $uid, $notification, $isEmail);
		}else
		{
			$getFriends = Api_Dao_Friends::friendsGetFriends($uid);
			$sendFriends = array_intersect($toids, $getFriends);
			
			if(count($sendFriends) > 0)
			{
				$mailid = Api_Dao_Notification::createMail($uid, $subject);
				// If uid sends email to others, are they really in the thread yet?  only when response exists?

				foreach($sendFriends as $friend)
				{
					Api_Dao_Notification::addUserToMail($mailid, $friend);
				}
				Api_Dao_Notification::addMessage($mailid, $uid, $notification, 0);
			}
		}
	}
	/**
	 * bool mail ( string $to , string $subject , string $message [, string $additional_headers [, string $additional_parameters ]] )
	 *
	 * @param unknown_type $from
	 * @param unknown_type $to
	 * @param unknown_type $subject
	 * @param unknown_type $message
	 */
	public static function sendEmail($from, $to, $subject, $message)
	{
		$eContacts = Api_Dao_UserProfile::getEContacts($to, $from);
		
		$toEmail = array();
		$fromEmail = "";
		foreach($eContacts as $contact)
		{
			if($contact->user_id == $from)
			{
				$fromEmail = $contact->contact_value;
			}else
			{
				$toEmail [] = $contact->contact_value;
			}
		}
		
		$headers = "From: $fromEmail\r\nReply-To: $fromEmail";
		
		// FIXME: uncomment to actually send the email
		//return mail(implode(",", $toEmail), $subject, $message, $headers);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $userId
	 * @param unknown_type $subject
	 * @return unknown
	 */
	public static function createMail($userId, $subject)
	{
		return Api_Dao_Notification::createMail($userId, $subject);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $mailid
	 * @param unknown_type $uid
	 * @param unknown_type $message
	 * @param unknown_type $isEmail
	 * @return unknown
	 */
	public static function addMessage($mailid, $uid, $message, $isEmail)
	{
		return Api_Dao_Notification::addMessage($mailid, $uid, $message, $isEmail);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $mailid
	 * @param unknown_type $uid
	 * @return unknown
	 */
	public static function addUserToMail($mailid, $uid)
	{
		return Api_Dao_Notification::addUserToMail($mailid, $uid);
	}
}
?>