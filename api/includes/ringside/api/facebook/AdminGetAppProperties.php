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
require_once ("ringside/api/bo/DomainService.php");
require_once ("ringside/api/bo/KeyService.php");
require_once ("ringside/api/bo/App.php");

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
 * @apiName AdminGetAppProperties
 * @callMethod admin.getAppProperties
 * @apiRequired aid - the ID of the application for which payment plans will be returned.
 * @return Array containing plan_id, aid, network_id, name, length, unit, price, description. 
 * 
 * @author Richard Friedman
 * @author Mike Schachter
 */
class AdminGetAppProperties extends Api_DefaultRest
{
	private static $map = null;
	private $m_nid;
	private $m_aid;
	private $m_apiKey;
	private $m_properties;
	private $m_canvasName;
	private static function loadMap()
	{
		if(self::$map == null)
		{
			self::$map['application_id'] = 'id';
			self::$map['application_name'] = 'name';
			self::$map['callback_url'] = 'callback_url';
			self::$map['post_install_url'] = 'postadd_url';
			self::$map['edit_url'] = 'edit_url';
			self::$map['dashboard_url'] = 'dashboard_url';
			self::$map['uninstall_url'] = 'postremove_url';
			self::$map['ip_list'] = 'ip_list';
			self::$map['logo_url'] = 'logo_url';
			self::$map['email'] = 'support_email';
			self::$map['description'] = 'description';
			self::$map['use_iframe'] = 'canvas_type';
			self::$map['desktop'] = 'desktop';
			self::$map['is_mobile'] = 'mobile';
			self::$map['default_fbml'] = 'default_fbml';
			self::$map['default_column'] = 'default_column';
			self::$map['message_url'] = 'message_url';
			self::$map['message_action'] = 'message_action';
			self::$map['about_url'] = 'about_url';
			self::$map['attachment_action'] = 'attachment_action';
			self::$map['attachment_callback_url'] = 'attachment_callback_url';
			self::$map['private_install'] = 'private_install';
			self::$map['installable'] = 'deployed';
			self::$map['privacy_url'] = 'privacy_url';
			self::$map['help_url'] = 'help_url';
			self::$map['sidenav_url'] = 'sidenav_url';
			self::$map['see_all_url'] = 'see_all_url';
			self::$map['tos_url'] = 'tos_url';
			self::$map['dev_mode'] = 'developer_mode';
			self::$map['preload_fql'] = 'preload_fql';
			self::$map['icon_url'] = 'icon_url';
			self::$map['canvas_url'] = 'canvas_url';
			self::$map['isdefault'] = 'isdefault';
			self::$map['api_key'] = 'api_key';
			self::$map['secret_key'] = 'secret_key';
			self::$map['author'] = 'author';
			self::$map['author_url'] = 'author_url';
			self::$map['author_description'] = 'author_description';
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
		$this->m_callingAid = $this->getAppId();
		$this->m_aid = $this->getApiParam('aid', $this->getAppId());
		$this->m_nid = $this->getApiParam('nid', $this->getNetworkId());
		$this->m_apiKey = $this->getApiParam('app_api_key');
		$this->m_canvasName = $this->getApiParam('canvas_url');
		// we expect a json_encoded list of properties.
		$this->m_properties = json_decode($this->getApiParam('properties'));
		if(empty($this->m_properties) || ! is_array($this->m_properties))
		{
			throw new OpenFBAPIException("The properties must be specified as a valid json entry.", FB_ERROR_CODE_PARAMETER_MISSING);
		}
		//		print_r( $this );
	}
	/**
	 * Process API request to get an applications properties.
	 *
	 */
	public function execute()
	{
		$appService = Api_ServiceFactory::create('AppService');
		
		$response = array();
		if($this->m_canvasName != null)
		{
			$ids = $appService->getNativeIdsByProperty('canvas_url', $this->m_canvasName);
			if(($ids == NULL) || (count($ids) == 0)) {
				throw new OpenFBAPIException("No such application known, canvas name is '{$this->m_canvasName}'", FB_ERROR_CODE_NO_APP);
			}
			$this->m_aid = $ids[0];			
			
		} else if($this->m_apiKey != null) {
		
			$id = $appService->getNativeIdByApiKey($this->m_apiKey);
			if($id == NULL) {
				throw new OpenFBAPIException("No such application known, API key is '{$this->m_apiKey}' on '{$this->m_nid}' network.", FB_ERROR_CODE_NO_APP);
			}
			$this->m_aid = $id;
		}
		
		/*
		 * You can only cross check application information if
		 * the calling application is a default application
		 */
		// TODO: SECURITY: This disables cross-app calling security if uncommented!
		if( false  &&  $this->m_aid != $this->getAppId())
		{
			$isDefault = $this->checkDefaultApp($this->m_aid);
			if(! $isDefault) {
				throw new OpenFBAPIException('Application with id ' . $this->getAppId() . ' is not a default app: ' . FB_ERROR_MSG_GRAPH_EXCEPTION, FB_ERROR_CODE_GRAPH_EXCEPTION);
			}
		}
		$app = $appService->getApp($this->m_aid);
	
		$domainService = Api_Bo_DomainService::create();
		$did = $domainService->getNativeIdByApiKey($this->m_nid);
		$keyService = Api_Bo_KeyService::create();
		$keyset = $keyService->getKeyset($this->m_aid, $did);
		$app['api_key'] = isset($keyset['api_key'])?$keyset['api_key']:'';
		$app['secret_key'] = isset($keyset['secret'])?$keyset['secret']:'';
		
		$response = array();
		if($app != NULL) {
			foreach($this->m_properties as $prop) {
				if(! isset(self::$map[$prop])) {
					throw new OpenFBAPIException(FB_ERROR_MSG_PARAMETER_MISSING, FB_ERROR_CODE_PARAMETER_MISSING);
				}
				$key = self::$map[$prop];
				if(! isset($app[$key])) {
					$response[$prop] = '';
				} else {
					$response[$prop] = $app[$key];
				}
			}
		}
		return array('result' => json_encode($response));
	}
}
?>
