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
require_once ('ringside/api/ServiceFactory.php');


/**
 * Returns the public information available for the given Application.
 * 
 * @author Ringside Networks
 * @apiName ApplicationGetPublicInfo
 * @callMethod application.getPublicInfo
 * @apiRequired One of the following is required: AppId, ApiKey, or Canvas Name.  Passing in more than one still works, but is less efficient.
 * @apiOptional int AppId The application ID.
 * @apiOptional string ApiKey The API Key.
 * @apiOptional string CanvasName The Canvas name as set up in the developer app for this application.
 * @error_code 95 Invalid Parameter
 * @error_code 900 No application exist for given parameters
 * @return Array containing application display_name, description, api_key, canvas_url, app_id, icon_url
 * company_name, and logo_url 
 */
class ApplicationGetPublicInfo extends Api_DefaultRest
{
	
	private static $map = null;
	private $m_aid;
	private $m_apiKey;
	private $m_properties;
	private $m_canvasName;
	
	private static function loadMap()
	{
		if(self::$map == null)
		{
			self::$map ['display_name'] = 'name';
			self::$map ['description'] = 'description';
			self::$map ['api_key'] = 'api_key';
			self::$map ['canvas_url'] = 'canvas_url';
			self::$map ['app_id'] = 'id';
			self::$map ['icon_url'] = 'icon_url';
			self::$map ['company_name'] = 'author';
			self::$map ['logo_url'] = 'logo_url';
		}
	
	}
	/**
	 * Validate Request
	 */
	public function validateRequest()
	{
		self::loadMap();
		
		$this->m_aid = $this->getApiParam( 'aid' );
		$this->m_apiKey = $this->getApiParam( 'app_api_key' );
		$this->m_canvasName = $this->getApiParam( 'canvas_url' );
	
	}
	
	/**
	 * Process API request to get an applications properties.
	 *
	 */
	public function execute()
	{
		
		$response = array();
		$appService = Api_ServiceFactory::create('AppService');
		//query by canvas name
		if($this->m_canvasName != null)
		{
			$ids = $appService->getNativeIdsByProperty('canvas_url', $this->m_canvasName);
			if (($ids == NULL) || (count($ids) == 0))
			{
				throw new OpenFBAPIException("No such application known." . $this->m_canvasName, FB_ERROR_CODE_NO_APP);
			}
			$this->m_aid = $ids[0];
		}
		
		//query by API key
		if($this->m_apiKey != null)
		{
			$id = $appService->getNativeIdByApiKey($this->m_apiKey);
			if (($id == NULL) || ($id === false))
			{
				throw new OpenFBAPIException("No such application known." . $this->m_canvasName, FB_ERROR_CODE_NO_APP);
			}
			$this->m_aid = $id;
		}
		
		$app_info = null;
		if($this->m_aid != null)
		{
			$app_info = $appService->getApp($this->m_aid);
		}else
		{
			throw new OpenFBAPIException("Invalid Parameters: Api Key, Canvas Name or Application ID are required!", FB_ERROR_CODE_INVALID_PARAMETER);
		}
		
		$response = array();
		if(! empty($app_info))
		{
			foreach(self::$map as $prop=>$key)
			{
				
				if(! isset($app_info [0] [$key]))
				{
					$response [$prop] = '';
				}else
				{
					$response [$prop] = $app_info [0] [$key];
				}
			}
		}
		
		return array('result' => json_encode($response));
	}

}
?>
