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
require_once ('ringside/api/bo/Group.php');
/**
 * groups.getMembers API
 * 
 * @author Richard Friedman
 */
class GroupsGetMembers extends Api_DefaultRest
{
	private $m_gid;

	public function validateRequest()
	{
		$this->m_gid = $this->getRequiredApiParam('gid');
	}

	/**
	 * Execute the groups.getMembers
	 *
	 * Return 4 lists
	 * - members
	 * - admins
	 * - officers
	 * - not_replied 
	 * 
	 */
	public function execute()
	{
		$groupMembers = Api_Bo_Group::getGroupMembers($this->m_gid);
		
		$response = array();
		
		$members = array();
		$admins = array();
		$officers = array();
		$not_replied = array();
		
		foreach($groupMembers as $member)
		{
			if($member['admin'] == true)
			{
				$admins[] = $member['uid'];
			}
			if($member['member'] == true)
			{
				$members[] = $member['uid'];
			}
			if($member['officer'] == true)
			{
				$officers[] = $member['uid'];
			}
			if($member['pending'] == true)
			{
				$not_replied[] = $member['uid'];
			}
		}
		
		$response['members'] = array('uid' => $members);
		$response['admins'] = array('uid' => $admins);
		$response['officers'] = array('uid' => $officers);
		$response['not_replied'] = array('uid' => $not_replied);
		
		return $response;
	}

}

?>