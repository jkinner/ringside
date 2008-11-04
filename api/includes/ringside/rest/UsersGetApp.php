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
require_once ("ringside/api/bo/App.php");

/**
 * Returns all the info for this app_id in the users_app table, for this user
 */
class UsersGetApp extends Api_DefaultRest
{
	private $app_id;
	/**
	 * Validate Request
	 */
	public function validateRequest()
	{
		$this->app_id = $this->getRequiredApiParam('app_id');
	}
	
	/**
	 * Execute the api call to get user app list.
	 */
	public function execute()
	{
		$apps = Api_Bo_App::getUserApp($this->getUserId(), $this->app_id);
		
		$response = array();
		if(count($apps) == 0)
		{
			$response ['app'] = array();
		}else
		{
			$response ['app'] = $apps[0];
		}
		
		return $response;
	}
}

?>
