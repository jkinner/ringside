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
require_once ("ringside/api/ServiceFactory.php");
require_once ("ringside/rest/AdminGetServerInfo.php");

class AdminCreateApp extends Api_DefaultRest
{
	protected $m_appName;

	public function validateRequest()
	{
		//make sure calling application is a default application
		$this->checkDefaultApp();
		//check to make sure name is set
		$this->m_appName = $this->getRequiredApiParam("name");
	}

	public function execute()
	{
		$appService = Api_ServiceFactory::create('AppService');
		$keyService = Api_ServiceFactory::create('KeyService');
	
		//check to make sure name isn't already taken
		$ids = $appService->getNativeIdsByProperty('name', $this->m_appName);
		
		if (($ids != NULL) && (count($ids) > 0))
		{
			throw new OpenFBAPIException("An application named '" . $this->m_appName . "' already exists.");
		}
		//create unique API and secret keys
		$apiKey = "";
		$secret = "";
		$uniqueKeys = false;
		while(! $uniqueKeys)
		{
			//create API key
			for($k = 0; $k < 32; $k ++)
			{
				$apiKey .= dechex(mt_rand(1, 15));
			}
			//create secret
			for($k = 0; $k < 32; $k ++)
			{
				$secret .= dechex(mt_rand(1, 15));
			}
			//check for uniqueness
			$uniqueKeys = $keyService->isUnique($apiKey, $secret);
		}
		//create icon url
		$infoApi = new AdminGetServerInfo($this->getUserId(), array(), $this->getSession());
		$sinfo = $infoApi->execute();
		$wurl = $sinfo['result']['web_url'];
		if($wurl == 'http://:') {
			$wurl = 'http://localhost:8080';
		}
		$iconUrl = $wurl . '/images/icon-app-default.png';
		
		//create app		
		$appProps = array('isdefault' => 0, 'icon_url' => $iconUrl);
		$id = $appService->createApp($this->m_appName, $apiKey, $secret, $appProps);
		
		//return response
		$response = array();
		if($id !== false)
		{
			$response["app"] = array();
			$response['app']['application_id'] = $id;
			$response["app"]["name"] = $this->m_appName;
			$response["app"]["api_key"] = $apiKey;
			$response["app"]["secret"] = $secret;
		} else {
		    error_log('Error creating application');
		}
		return $response;
	}
}
?>
