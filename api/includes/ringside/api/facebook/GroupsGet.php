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

require_once ("ringside/api/OpenFBAPIException.php");
require_once ("ringside/api/DefaultRest.php");
require_once ("ringside/api/bo/Group.php");

/**
 * groups.get API
 * 
 * @author Richard Friedman
 */
class GroupsGet extends Api_DefaultRest
{
	const PARAM_USER_ID = 'uid';
	const PARAM_GROUP_IDS = 'gids';
	
	private $m_uid;
	private $m_gids;

	public function validateRequest()
	{
		$this->m_uid = $this->getApiParam(self::PARAM_USER_ID);
		if(empty($this->m_uid))
		{
			$this->m_uid = $this->getUserId();
		}
		
		$gids = $this->getApiParam(self::PARAM_GROUP_IDS);
		if($gids == null || (strlen(trim($gids)) == 0))
		{
			$this->m_gids = array();
		}else
		{
			$this->m_gids = explode(',', $gids);
		}
	}

	/**
	 * Execute the groups.get method
	 *  
	 */
	public function execute()
	{
		$groups = Api_Bo_Group::getGroups($this->getUserId(), $this->m_uid, $this->m_gids);
		$response = array();
		if(count($groups) > 0)
		{
			$response[FB_GROUP_GROUP] = array();
			
			foreach($groups as $group)
			{	
				$venue = array();
				$venue[FB_GROUP_STREET] = $group['street'];
				$venue[FB_GROUP_CITY] = $group['city'];
				$venue[FB_GROUP_STATE] = $group['state'];
				$venue[FB_GROUP_COUNTRY] = $group['country'];
				$venue['latitude'] = '';
				$venue['longitude'] = '';
				
				$gresp = array();
				$gresp[FB_GROUP_GID] =$group['gid'];
				$gresp[FB_GROUP_NAME] = $group['name'];
				$gresp[FB_GROUP_NID] = $group['nid'];
				$gresp[FB_GROUP_DESCRIPTION] = $group['description'];
				$gresp[FB_GROUP_GROUP_TYPE] = $group['group_type'];
				$gresp[FB_GROUP_GROUP_SUBTYPE] = $group['group_subtype'];
				$gresp[FB_GROUP_RECENT_NEWS] = $group['recent_news'];
				$gresp[FB_GROUP_IMAGE] = $group['pic_small'];
				$gresp[FB_GROUP_IMAGE_BIG] = $group['pic_big'];
				$gresp[FB_GROUP_IMAGE_SMALL] = $group['pic_small'];
				$gresp[FB_GROUP_CREATOR] = $group['creator'];
				$gresp[FB_GROUP_MODIFIED] = $group['modified'];
				$gresp[FB_GROUP_OFFICE] = $group['office'];
				$gresp[FB_GROUP_WEBSITE] = $group['website'];
				$gresp[FB_GROUP_EMAIL] = $group['email'];
				$gresp[FB_GROUP_VENUE] = $venue;
				
				$response[FB_GROUP_GROUP][] = $gresp;
			}
		}
		
		return $response;
	}
}
?>
