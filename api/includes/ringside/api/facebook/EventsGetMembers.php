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
require_once 'ringside/api/bo/Events.php';
require_once ("ringside/api/OpenFBAPIException.php");
require_once ("ringside/api/DefaultRest.php");
/**
 * events.getMembers API
 */
class EventsGetMembers extends Api_DefaultRest
{
	private $m_eid;

	public function validateRequest()
	{
		$this->checkRequiredParam('eid');
		$this->m_eid = $this->getApiParam('eid');
	}

	/**
	 * Execute the groups.get method
	 *  
	 */
	public function execute()
	{
		$em = Api_Bo_Events::getMembers($this->getUserId(), $this->m_eid);
		$response = array();
		if(count($em) > 0)
		{
			$response[FB_EVENTS_MEMBERS_ATTENDING]['uid'] = array();
			$response[FB_EVENTS_MEMBERS_UNSURE]['uid'] = array();
			$response[FB_EVENTS_MEMBERS_DECLINED]['uid'] = array();
			$response[FB_EVENTS_MEMBERS_NOT_REPLIED]['uid'] = array();
			
			foreach($em as $mem)
			{
				switch($mem['rsvp'])
				{
					case Api_Bo_Events::RS_FBDB_RSVP_ATTENDING:
						$response[FB_EVENTS_MEMBERS_ATTENDING]['uid'][] = $mem['uid'];
						break;
					case Api_Bo_Events::RS_FBDB_RSVP_DECLINED:
						$response[FB_EVENTS_MEMBERS_DECLINED]['uid'][] = $mem['uid'];
						break;
					case Api_Bo_Events::RS_FBDB_RSVP_NOT_REPLIED:
						$response[FB_EVENTS_MEMBERS_NOT_REPLIED]['uid'][] = $mem['uid'];
						break;
					case Api_Bo_Events::RS_FBDB_RSVP_UNSURE:
						$response[FB_EVENTS_MEMBERS_UNSURE]['uid'][] = $mem['uid'];
						break;
				}
			}
		}
		
		return $response;
	}
}

?>