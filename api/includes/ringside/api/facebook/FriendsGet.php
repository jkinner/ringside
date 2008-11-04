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
require_once ("ringside/api/bo/Friends.php");

/**
 * Gets the list of friends for a given user.
 * 
 * @apiName FriendsGet
 * @callMethod friends.get
 * @return Array $r['uid'] = array of friend uids. on empty returns $r['uid']=''
 */
class FriendsGet extends Api_DefaultRest
{
	private $m_uid;

	/**
	 * Get UID if its passed in.
	 */
	public function validateRequest()
	{
		$this->m_uid = $this->getApiParam('uid', null);
	}

	/**
	 * Execute the Friends.get method
	 *
	 * @return The user ids that are the friends.
	 *
	 * For example if the friend uids are 2,3,4 then the return value would be
	 * array( uid=> array(2,3,4) )
	 *
	 */
	public function execute()
	{
		$response = array();
		
		$friends = null;
		if($this->m_uid == null)
		{
			$friends = Api_Bo_Friends::getFriends($this->getUserId());
		}else
		{
			// What type of friend check should we do?
			$friends = Api_Bo_Friends::getFriends($this->m_uid);
		}
		
		if(empty($friends))
		{
			$response['uid'] = '';
		}else
		{
			$response['uid'] = $friends;
		}
		
		return $response;
	}
}

?>
