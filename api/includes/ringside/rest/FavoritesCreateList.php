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
class FavoritesCreateList extends Api_DefaultRest
{
	private $name;

	/**
	 * Validate request.
	 */
	public function validateRequest()
	{
		$this->name = $this->getRequiredApiParam('name');
	}

	/**
	 * Creates a list of favorites for the logged in user.
	 *
	 * @param $name the name of the list
	 * @return int the identifier of the list
	 */
	public function execute()
	{
		$ret = Api_Bo_Favorites::createList($this->name, $this->getAppId(), $this->getUserId());
		
		$response = array();
		$response['result'] = $ret === false?'0':$ret;
		
		return $response;
	}

}
?>
