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
class FavoritesGetLists extends Api_DefaultRest
{
	private $uid;

	/**
	 * Validate request.
	 */
	public function validateRequest()
	{
		
		$this->uid = $this->getApiParam('uid', $this->getUserId());
	}

	/**
	 * Gets the favorites lists for a given user or for the logged-in user.
	 *
	 * @param int $uid optional - the user for whom to get the lists. If not specified, the logged in user will be used.
	 */
	public function execute()
	{
		$lists = Api_Bo_Favorites::getLists($this->uid, $this->getAppId());
		$retVal = array();
		
		if(count($lists) > 0)
		{
			$i = 0;
			foreach($lists as $list)
			{
				$retVal['list'][$i++] = array('id' => $list['id'], 'name' => $list['name']);
			}
		}
		return $retVal;
	}

}
?>
