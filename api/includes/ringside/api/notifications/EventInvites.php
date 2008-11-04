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
require_once 'ringside/api/notifications/INotification.php';
require_once 'ringside/api/dao/records/RingsideEventsMember.php';
require_once 'ringside/api/bo/Events.php';

/**
 * Enter description here...
 *
 */
class EventInvites implements INotification
{
	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	public function execute()
	{
		return $this->get($this->getUserId(), $this->getApiParams());
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $params
	 * @return unknown
	 */
	public function get($uid, $params)
	{
		$q = Doctrine_Query::create();
		$q->select('eid')
			->from('RingsideEventsMember')
			->where("uid = $uid and rsvp = ".Api_Bo_Events::RS_FBDB_RSVP_NOT_REPLIED);
		$members = $q->execute();
		
		$response = array();
		$response['eid'] = array();
		
		foreach($members as $member)
		{
			$response['eid'][] = $member['eid'];
		}
		
		return $response;
	}
}

?>
