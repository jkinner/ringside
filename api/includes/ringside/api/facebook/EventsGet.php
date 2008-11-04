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
require_once 'ringside/api/DefaultRest.php';
require_once 'ringside/api/facebook/OpenFBConstants.php';
require_once 'ringside/api/bo/Events.php';
/**
 * events.get API
 * 
 * @author Richard Friedman
 */
class EventsGet extends Api_DefaultRest
{
	private $m_uid;
	private $m_eids;
	private $m_start_time;
	private $m_end_time;
	private $m_rsvp_status;

	public function validateRequest()
	{
		$this->m_uid = $this->getApiParam('uid');
		
		// client sends empty 'eids' param when none are specified
		$eids = $this->getApiParam('eids');
		if(! empty($eids) && (strlen(trim($eids)) > 0))
		{
			$this->m_eids = explode(',', $eids);
		}else
		{
			$this->m_eids = null;
		}
		$this->m_start_time = $this->getApiParam('start_time');
		if($this->m_start_time == '0')
		{
			$this->m_start_time = null;
		}
		$this->m_end_time = $this->getApiParam('end_time');
		if($this->m_end_time == '0')
		{
			$this->m_end_time = null;
		}
		$this->m_rsvp_status = $this->getApiParam('rsvp_status');
	}

	public function execute()
	{
		$events = Api_Bo_Events::getEvents($this->getUserId(), $this->m_uid, $this->m_start_time, $this->m_end_time, $this->m_eids, $this->m_rsvp_status);
		$response = array();
		
		if(! empty($events))
		{
			$response[FB_EVENTS_EVENT] = array();
			foreach($events as $event)
			{
				$venue = array(FB_EVENTS_STREET => $event['street'], FB_EVENTS_CITY => $event['city'], FB_EVENTS_STATE => $event['state'], FB_EVENTS_COUNTRY => $event['country'], FB_EVENTS_LATITUDE => '', FB_EVENTS_LONGITUDE => '');
				$response[FB_EVENTS_EVENT][] = array(FB_EVENTS_EID => $event['eid'], FB_EVENTS_NAME => $event['name'], FB_EVENTS_TAGLINE => $event['tagline'], FB_EVENTS_NID => $event['nid'], FB_EVENTS_PIC => $event['pic'], FB_EVENTS_PIC_BIG => $event['pic'], FB_EVENTS_PIC_SMALL => $event['pic'], FB_EVENTS_HOST => $event['host'], FB_EVENTS_DESCRIPTION => $event['description'], FB_EVENTS_EVENT_TYPE => $event['event_type'], FB_EVENTS_EVENT_SUBTYPE => $event['event_subtype'], FB_EVENTS_START_TIME => $event['start_time'], FB_EVENTS_END_TIME => $event['end_time'], FB_EVENTS_CREATOR => $event['uid'], FB_EVENTS_MODIFIED => $event['modified'], FB_EVENTS_LOCATION => $event['location'], FB_EVENTS_VENUE => $venue);
			}
		}
		
		return $response;
	}
}

?>
