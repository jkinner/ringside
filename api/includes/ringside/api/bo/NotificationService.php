<?php
 /*
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
  */

/**
 * Document this file.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
abstract class Api_Bo_NotificationService
{
   public static function create($implClass = null)
   {
       return Api_ServiceFactory::create('Api_Bo_NotificationService', 'Api_Bo_NotificationServiceImpl', $implClass);
   }
   
   /**
    * Sends notifications using the channel defined by the service implementation.
    *
    * @param integer					$app_id			the ID of the app sending the notification
    * @param RingsideUser			$from				the sender of the notification
    * @param array(RingsideUser) $to 				list of RingsideUser to send to
    * @param string 					$subject 		the subject of the message (if available)
    * @param array					$body 			the body of the message or an array of alternatives, keyed by MIME type
    * @param array(RingsideUser) $cc 				optional - list of RingsideUser to carbon copy (if available)
    * @param array(RingsideUser) $bcc 				optional - list of RingsideUser to blind carbon copy (if available)
    * @param array 					$attachments	optional - any attachments to the message (if available)
    */
   public abstract function sendNotifications($app_id, $from, $to, $subject, $body, $cc = null, $bcc = null, $attachments = null);
   
   public abstract function getNotifications($app_id, $user);
}
?>