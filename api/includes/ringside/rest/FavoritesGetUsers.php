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
require_once ("ringside/api/DefaultRest.php");
require_once ("ringside/api/bo/Favorites.php");

/**
 * @author Mark Lugert mlugert@ringsidenetworks.com
 * @author Jason Kinner jkinner@ringsidenetworks.com
 *  */
class FavoritesGetUsers extends Api_DefaultRest
{
	private $uids;
	private $lid;
	private $alid;
	private $iid;

	/**
	 * Validate request. 
	 */
	public function validateRequest()
	{
		$this->iid = $this->getRequiredApiParam('iid');
		$uid_string = $this->getApiParam('uids', $this->getUserId());
		$this->lid = $this->getApiParam('lid');
		$this->alid = $this->getApiParam('alid');
		
		if($this->isEmpty($this->alid) && $this->isEmpty($this->lid))
		{
			$this->alid = 0;
		}
	
		if(strlen($uid_string) > 0)
		{
			$this->uids = explode(',', $uid_string);
		}else
		{
			$this->uids = array();
		}
	}

	/**
	 * Checks whether the user (or users) has (have) marked this item as a favorite.
	 *
	 * @param string $iid the item to check
	 * @param array $uids optional - the uids to check, otherwise the logged in user
	 * @param int $lids optional - the array of lists to check, otherwise the default list
	 *
	 * @return array users who have the item marked favorite.
	 */
	public function execute()
	{
		$users = Api_Bo_Favorites::getUsers($this->getAppId(), $this->iid, $this->alid, $this->lid, $this->uids);
		
		$retVal = array();
		if(count($users) > 0)
		{
			foreach($users as $user)
			{
				$retVal['user'][] = $user['user_id'];
			}
		}
		return $retVal;
	}

}
?>
