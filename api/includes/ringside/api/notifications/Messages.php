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
require_once ("ringside/api/notifications/INotification.php");
require_once 'ringside/api/dao/records/RingsideMailMessage.php';
require_once 'ringside/api/dao/records/RingsideMailBox.php';

/**
 * Enter description here...
 *
 */
class Messages implements INotification
{

	public function get($uid, $params)
	{
		$q = Doctrine_Query::create();
		$q->distinct(true);
		$q->select('box.mail_id')
			->from('RingsideMailBox as box LEFT JOIN box.RingsideMailMessage as msg ON box.mail_id = msg.mail_id')
			->where("box.uid = $uid AND msg.isemail = 0 AND msg.created > box.last_opened")
			->orderby('box.last_opened DESC, box.mail_id DESC');
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
