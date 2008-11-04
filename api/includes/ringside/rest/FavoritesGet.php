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
class FavoritesGet extends Api_DefaultRest
{
	private $uids;
	private $lid;
	private $alid;

	/**
	 * Validate request.
	 */
	public function validateRequest()
	{
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
	 * Gets the favorite items for users.
	 *
	 * @param Array $uids the users for whom to retrieve favorite items, or the logged in user if not specified
	 * @param int $lid optional - the list that is used to organize the favorites, or the default list if not specified
	 * @param int $alid optional - the list that is used to organize the favorites for the app, otherwise the default list for the app
	 *
	 * @return unknown
	 */
	public function execute()
	{
		$favs = Api_Bo_Favorites::getFavorites($this->getAppId(), $this->uids, $this->alid, $this->lid);
		$retVal = array();
		
		if(count($favs) > 0)
		{
			$retVal['favorite'] = array();
			$i = 0;
			foreach($favs as $fav)
			{
				$retVal['favorite'][$i]['name'] = $fav['name'];
				$retVal['favorite'][$i]['iid'] = $fav['item_id'];
				$retVal['favorite'][$i]['uids'][] = $fav['user_id'];
				$retVal['favorite'][$i]['lid'] = $fav['list_id'];
				$retVal['favorite'][$i]['alid'] = $fav['app_list_id'];
				$i ++;
			}
		}
		
		return $retVal;
	}

}
?>
