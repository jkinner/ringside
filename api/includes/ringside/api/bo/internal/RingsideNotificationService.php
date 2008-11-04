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
class Api_Bo_RingsideNotificationService extends Api_Bo_NotificationService
{
    public function sendNotifications($app_id, $from, $to, $subject, $body, $cc = null, $bcc = null, $attachments = null)
    {
        if ( ! is_array($to) )
        {
            $to = array();
        }

        $getFriends = Api_Dao_Friends::friendsGetFriends($from->id);
        $toids = array();
        foreach ( $to as $to_user )
        {
            $toids[] = $to->id;
        }
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
     
    public function getNotifications($app_id, $user)
    {
        $q = Doctrine_Query::create();
        $q->distinct(true);
        $q->select('box.mail_id')
        ->from('RingsideMailBox as box LEFT JOIN box.RingsideMailMessage as msg ON box.mail_id = msg.mail_id')
        ->where("box.uid = $uid AND msg.isemail = 1 AND msg.created > box.last_opened")->orderby('box.last_opened DESC, box.mail_id DESC');
        $mail = $q->execute();

        $response = array();
        $response['unread'] = count($mail);
        if($response['unread'] == 0)
        {
            $response['most_recent'] = 0;
        }else
        {
            $response['most_recent'] = $mail[0]['mail_id'];
        }
        return $response;
    }
}
?>