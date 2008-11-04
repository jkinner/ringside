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
require_once ("ringside/api/bo/DomainService.php");

// TODO: REMOVE after filling in method implementation
require_once 'ringside/web/config/RingsideWebConfig.php';

/**
 * Retrieve information about the calling application.
 * This might even be useful for connecting applications
 * real time.   For example Aplication A can end point for
 * application B if user has both installed.
 *
 * Extension: You can supply the following parameters:
 *  "canvas_url" - the canvas url of the application
 *  "api_key" - the API key of the application
 *
 * @apiName AdminGetDomainProperties
 * @callMethod admin.getDomainProperties
 * @apiRequired aid - the ID of the application for which payment plans will be returned.
 * @return Array containing plan_id, aid, network_id, name, length, unit, price, description. 
 * 
 * @author Richard Friedman
 * @author Mike Schachter
 */
class AdminGetDomainProperties extends Api_DefaultRest
{
	private static $map = null;
	private $m_nid;
	private $m_apiKey;
	private $m_properties;

	private static function loadMap()
	{
		if(self::$map == null)
		{
			self::$map['url'] = 'url';
			self::$map['domain_name'] = 'name';
			self::$map['post_map_url'] = 'postmap_url';
			self::$map['api_key'] = 'api_key';
			self::$map['secret_key'] = 'secret_key';
			self::$map['resize_url'] = 'resize_url';
			self::$map['owner'] = 'owner';
			self::$map['owner_url'] = 'owner_url';
			self::$map['owner_description'] = 'owner_description';
		}
	}
	
	/**
	 * Validate Request.
	 *
	 */
	public function validateRequest()
	{
		$this->checkRequiredParam('properties');
		self::loadMap();
		$this->m_nid = $this->getApiParam('nid');
		$this->m_apiKey = $this->getApiParam('domain_api_key');

		// we expect a json_encoded list of properties.
		$this->m_properties = json_decode($this->getApiParam('properties'));
		if(empty($this->m_properties) || ! is_array($this->m_properties))
		{
			throw new OpenFBAPIException("The properties must be specified as a valid json entry.", FB_ERROR_CODE_PARAMETER_MISSING);
		}
	}
	
	/**
	 * Process API request to get an applications properties.
	 *
	 */
	public function execute()
	{
		$response = array();
		
		$ds = Api_Bo_DomainService::create();
		$domain = null;
		if ( ! isset($this->m_nid) )
		{
		    $this->m_nid = $ds->getNativeIdByApiKey($this->m_apiKey);
		}
	    $domain = $ds->getDomain($this->m_nid);
		
	    error_log("Retrieved domain for ".$this->m_nid.":".var_export($domain, true));
		if(! empty($domain))
		{
			foreach($this->m_properties as $prop)
			{
				if(! isset(self::$map[$prop]))
				{
					throw new OpenFBAPIException(FB_ERROR_MSG_PARAMETER_MISSING, FB_ERROR_CODE_PARAMETER_MISSING);
				}
				$key = self::$map[$prop];
				if(! isset($domain[$key]))
				{
					$response[$prop] = '';
				}else
				{
					$response[$prop] = $domain[$key];
				}
			}
		}
		return array('result' => json_encode($response));
	}
}
?>
