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
require_once ("ringside/api/dao/UsersApp.php");
require_once ("ringside/api/dao/App.php");
require_once ("ringside/api/ServiceFactory.php");

/**
 * Sets API and secret keys for non-Ringside networks associated
 * with a given app and app developer.
 *
 * @author Mike Schachter
 * @apiName UsersSetAppKeys
 * @apiRequired app_api_key The Ringside API key of the application.
 * @apiRequired keys A JSON-encoded array of associative arrays, each
 * 				 row having the following key-value pairs:
 *					 network_id - the ID of the network
 * 		  		 api_key - API key of network
 * 		  		 secret - secret of network 	 
 * @callMethod ringside.users.setAppKeys
 * @return boolean true if set was successful.
 
 */
class AdminSetAppKeys extends Api_DefaultRest
{
	protected $m_appId;
	
	protected $m_keys;

	public function validateRequest()
	{
		$this->m_appId = $this->getRequiredApiParam('app_id');
		$this->m_keys = json_decode($this->getRequiredApiParam('keys'), true);
	}

	public function execute()
	{
		
		if(! Api_Dao_App::checkUserOwnsApp($this->getUserId(), $this->m_appId))
		{
			throw new Exception('Cannot query for app keys, user does not own app.');
		}
		
		$domainService = Api_ServiceFactory::create('DomainService');
		$keyService = Api_ServiceFactory::create('KeyService');

		foreach ( $this->m_keys as $keyset )
		{
			$domainKey = $keyset['network_id'];
			$domainId = $domainService->getNativeIdByApiKey($domainKey);
			$newApiKey = $keyset['api_key'];
			$newSecret = $keyset['secret'];
		
		    $keyService->updateKeyset($this->m_appId, $domainId, $newApiKey, $newSecret);
		}
		return true;
	}
}

?>
