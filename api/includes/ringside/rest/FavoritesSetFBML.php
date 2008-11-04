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
class FavoritesSetFBML extends Api_DefaultRest
{
	private $item_id;
	private $lid;
	private $alid;
	private $fbml;

	/**
	 * Validate request. 
	 */
	public function validateRequest()
	{
		$this->item_id = $this->getRequiredApiParam('iid');
		$this->fbml = $this->getRequiredApiParam('fbml');
		$this->lid = $this->getApiParam('lid');
		$this->alid = $this->getApiParam('alid');
		
		// alid and lid can't both be set, throw an exception here
		if($this->isEmpty($this->alid) && $this->isEmpty($this->lid))
		{
			$this->alid = 0;
		}
	}

	/**
	 * Sets the FBML description of the favorite within the list.
	 *
	 * @param int $iid the item to describe with the FBML.
	 * @param string $fbml the FBML description to associate with the item in the favorites list.
	 * @param int $lid optional - the list on which to set the description. The default list will be used as the default.
	 */
	public function execute()
	{
		$ret = Api_Bo_Favorites::setFavorite($this->getAppId(), $this->getUserId(), $this->item_id, $this->alid, $this->lid, $this->fbml);
		
		$response = array();
		$response['result'] = $ret !== false ? '1' : '0';
		
		return $response;
	}

}
?>
