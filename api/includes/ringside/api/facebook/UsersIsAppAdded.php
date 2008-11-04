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
require_once ("ringside/api/bo/App.php");

/**
 * users.isAppAdded API
 * 
 * This API has been extended to check on are other users added. 
 * TODO enforce social graph security model.
 * 
 * @rest aid represents the application id to check, defaults to calling app
 * @rest uid users to check if has app, defaults to current session user. 
 * 
 * @license LGPL
 * @author Richard Friedman
 */
class UsersIsAppAdded extends Api_DefaultRest
{
	
	private $m_checkApp;
	private $m_uid;

	public function validateRequest()
	{
		$this->m_checkApp = $this->getApiParam('aid', $this->getAppId());
		$this->m_uid = $this->getApiParam('uid', $this->getUserId());
	}

	/**
	 * Execute the users.hasAppPermission method
	 */
	public function execute()
	{
		$response = array();
		
		// TODO execute permission check. 
		$hasApp = Api_Bo_App::isUsersApp($this->m_checkApp, $this->m_uid);
		
		$response['result'] = $hasApp ? '1' : '0';
		
		return $response;
	}

}

?>
