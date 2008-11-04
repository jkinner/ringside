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
 */
class FavoritesGetFBML extends Api_DefaultRest
{
	private $item_id;
	private $lid;
	private $alid;
	private $uids;

	/**
	 * Validate Request.
	 */
	public function validateRequest()
	{
		$this->item_id = $this->getRequiredApiParam('iid');
		$this->lid = $this->getApiParam('lid');
		$this->alid = $this->getApiParam('alid');
		
		if($this->isEmpty($this->alid) && $this->isEmpty($this->lid))
		{
			$this->alid = 0;
		}
		
		$uid_string = $this->getApiParam('uids', $this->getUserId());
		if(strlen($uid_string) > 0)
		{
			$this->uids = explode(',', $uid_string);
		}else
		{
			$this->uids = array();
		}
	}

	/**
	 * Gets the FBML description of the item that the user set.
	 *
	 * @param int $iid the item for which to retrieve the user's FBML description
	 * @param array $uids optional - list of uids to retrieve FBML for, otherwise the logged in user
	 * @param int $lid optional - the list to retrieve the FBML description for, otherwise the default list
	 */
	public function execute()
	{
		$favs = Api_Bo_Favorites::getFBML($this->getAppId(), $this->item_id, $this->alid, $this->lid, $this->uids);
		$ret = array();
		
		if(count($favs > 0))
		{
			foreach($favs as $fav)
			{
				$ret['favorite'][] = array('uid' => $fav['user_id'], 'fbml' => $fav['fbml']);
			}
		}
		
		return $ret;
	}

}
?>
