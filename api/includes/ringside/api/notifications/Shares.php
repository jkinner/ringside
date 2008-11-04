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
require_once 'ringside/api/dao/records/RingsideShare.php';
require_once 'ringside/api/notifications/INotification.php';

class Shares implements INotification
{

	public function get($uid, $params)
	{
		$response = array();
		
		$q = Doctrine_Query::create();
		$q->select('shareid')->from('RingsideShare')->where("uid = $uid AND opened = 0")->orderby('created DESC');
		$shares = $q->execute();
		
		$response['unread'] = count($shares);
		if($response['unread'] == 0)
		{
			$response['most_recent'] = 0;
		}else
		{
			$response['most_recent'] = $shares[0]['shareid'];
		}
	
		return $response;
	}

}

?>
