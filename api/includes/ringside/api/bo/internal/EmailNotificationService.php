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
class Api_Bo_EmailNotificationService extends Api_Bo_NotificationService
{
    public function sendNotifications($from, $to, $subject, $body, $cc = null, $bcc = null, $attachments = null)
    {
        if ( ! is_array($to) ) {
            $to = array($to);
        }
        
        // TODO: CC and BCC are currently not supported
        if ( $cc != null && ! is_array($cc) ) {
            $cc = array($cc);
        }
        if ( $bcc != null && ! is_array($bcc) ) {
            $bcc = array($bcc);
        }
        
        if ( $from->domain->name == 'email') {
            $recipients = array();
            
            foreach ( $to as $to_user )
            {
                if ( $to_user->domain->name == 'email' )
                {
                    $recipients = $to_user->username;
                }
            }

            $this->sendEmail($from->username, $recipients, $subject, $body, $cc, $bcc, $attachments);
        } else {
            error_log("Warning: Attempt to send email notification to non-email user: ".$to_user->domain->name.":".$to_user->username);         
        }
    }
     
    private function sendEmail($from, $recipients, $subject, $body, $cc, $bcc, $attachments)
    {
        $crlf = "\n";
        $headers = array ('From' => $from, 'Return-Path' => $from, 'Subject' => $subject );

        // Creating the Mime message
        $mime = new Mail_mime ( $crlf );

        $text = false;
        $html = false;
        
        // Setting the body of the email
        if ( $body instanceof string ) {
            $text = $body;
            $mime->setTXTBody ( $text );
        } else {
            if ( isset($body['text/html']) )
            {
                $mime->setHTMLBody ( $html );
            }
            if ( isset($body['text/plain']) )
            {
                $mime->setTXTBody ( $text );
            }
        }

        // Add an attachment
        if ( $attachments != null ) {
            foreach ( $attachments as $attachment ) {
                $mime->addAttachment ( $attachment ['file'], $attachment ['content_type'], $attachment ['file_name'], 0 );
            }
        }

        // Set body and headers ready for base mail class
        $body = $mime->get ();
        $headers = $mime->headers ( $headers );

        // Sending the email using smtp
        $mail = & Mail::factory ( "smtp", $smtp_params );
        $result = $mail->send ( $recipients, $headers, $body );
        if ( PEAR::isError($result) )
        {
            error_log("FIREALARM: Failed to send email to $to: ".$result->getMessage());
            return false;
        }
        return $result;
    }
    
    public function getNotifications($user)
    {
        // No support yet for retrieving email, maybe by POP or IMAP
        return array();
    }
}
?>