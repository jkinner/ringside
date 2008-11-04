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
 * Friends.getAppUsers API
 */
class FriendsGetAppUsers extends Api_DefaultRest
{
	
	/**
	 * Validate Request.
	 */
	public function validateRequest( )
	{
		
	}
	
	/**
	 * Execute the Friends.getAppUsers method
	 *
	 * @return The user ids that are the friends.
	 *
	 * For example if the friend uids are 2,3,4 then the return value would be:
	 *  array( 'uid'=> (2,3,4) )
	 * )
	 */
	public function execute()
	{
		$users = Api_Bo_Friends::getAppUsers( $this->getNetworkId(), $this->getUserId(), $this->getContext()->getApiKey() );
		$response = array();
		if (null != $users) 
		{
      		foreach($users as $u)
      		{
      			$response ['uid'] [] = $u['user_id'];
      		}
		}
				
		return $response;
	}

}

?>
