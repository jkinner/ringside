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
require_once ('ringside/api/config/RingsideApiConfig.php');
require_once ('ringside/api/dao/records/RingsideMail.php');
require_once ('ringside/api/dao/records/RingsideMailBox.php');
require_once ('ringside/api/dao/records/RingsideMailMessage.php');
/**
 * Email Notification Class
 */
class Api_Dao_Notification
{
	/**
	 * Creates an entry in the mail table but only populates the uid field.
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $dbCon
	 * @return Mail ID
	 */
	public static function createMail($uid, $subject)
	{
		$mail = new RingsideMail();
		$mail->uid = $uid;
		$mail->subject = $subject;
		$ret = $mail->trySave();
		if($ret)
		{
			return $mail->getIncremented();
		}
		return false;
	}
	/**
	 * Creates a Mail Box entry in the DB for this user.
	 *
	 * @param unknown_type $mailid
	 * @param unknown_type $uid
	 * @return unknown
	 */
	public static function addUserToMail($mailid, $uid)
	{
		$s = strtotime("-1 minute");
		$date = date('Y-m-d h:i:s', $s);

		$box = new RingsideMailBox();
		$box->mail_id = $mailid;
		$box->uid = $uid;
		$box->last_opened = $date;
		$ret = $box->trySave();
		if($ret)
		{
			return $box->getIncremented();
		}
		return false;
	}
	/**
	 * Adds a Notification Message
	 *
	 * @param unknown_type $mailid
	 * @param unknown_type $uid
	 * @param unknown_type $message
	 * @param unknown_type $isEmail
	 * @return unknown
	 */
	public static function addMessage($mailid, $uid, $message, $isEmail)
	{
		$msg = new RingsideMailMessage();
		$msg->mail_id = $mailid;
		$msg->uid = $uid;
		$msg->fbml = $message;
		$msg->isemail = $isEmail;
		$ret = $msg->trySave();
		if($ret)
		{
			return $msg->getIncremented();
		}
		return false;
	}
}
?>
