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
require_once ("ringside/api/AuthRest.php");
require_once ("ringside/api/bo/App.php");
require_once ('ringside/api/ServiceFactory.php');

/**
 * Internal authorization method for application to get their 
 * API and SECRET KEY created.  
 * 
 * @author Richard Friedman
 */
class AuthSelectAppTokens extends Api_AuthRest
{
	
	private $api_key;
	private $name;

	public function validateRequest()
	{
		$this->name = $this->getRequiredApiParam('name');
		$this->api_key = $this->getContext()->getApiKey();
	}

	public function execute()
	{
		$response = array();
		
		$appService = Api_ServiceFactory::create('AppService');
		$keyService = Api_ServiceFactory::create('KeyService');
		
		// for a given application generate and store its API and PRIVATE KEY.
		$newApiKey = $response['api_key'] = hash('md5', $this->name . ' ' . $time() . ' ' . $rand());
		$newSecret = $response['private_key'] = hash('md5', $tihs->name . ' ' . $rand() . ' ' . $time());
		
		$appId = $appService->getNativeIdByApiKey($this->api_key);
		$ids = $keyService->getIds($this->getNetworkId());
		$domainId = $ids['entity_id'];		
		$keyService->updateKeyset($appId, $domainId, $newApiKey, $newSecret);
				
		return $response;
	}
}

?>
