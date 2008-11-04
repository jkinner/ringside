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
class FavoritesDelete extends Api_DefaultRest
{
	private $iid;
	private $lid;
	private $alid;

	/**
	 * Validate request.
	 */
	public function validateRequest()
	{
		$this->iid = $this->getRequiredApiParam('iid');
		
		$this->lid = $this->getApiParam('lid');
		$this->alid = $this->getApiParam('alid');
		
		// alid and lid can't both be set, throw an exception here
		if($this->isEmpty($this->alid) && $this->isEmpty($this->lid))
		{
			$this->alid = 0;
		}
	}

	/**
	 * Execute test returing result:value.
	 * @return 0/1 in ['result']=
	 */
	/**
	 * Marks the favorite as Deleted
	 *
	 * @param int $iid
	 * @param string $lid
	 * @return Array
	 */
	public function execute()
	{
		$ret = Api_Bo_Favorites::deleteFavorite($this->getAppId(), $this->getUserId(), $this->iid, $this->alid, $this->lid);
		
		$response = array();
		$response['result'] = $ret > 0 ? '1' : '0';
		
		return $response;
	}

}
?>
