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

require_once ('ringside/api/DefaultRest.php');
require_once ('ringside/api/bo/Network.php');

/**
 * Creates a Ringside network when supplied with a
 * unique name. Only the administrator user is allowed
 * to create a network.
 *
 * @author Mike Schachter
 * @apiName AdminCreateNetwork
 * @apiRequired name - unique network name
 * @apiRequired auth_url - the authorization url for the network (like login.php)
 * @apiRequired login_url - URL user is redirected to after login
 * @apiRequired canvas_url - URL of canvas renderer for network.
 * @apiRequired web_url - URL of network's web root
 * @callMethod ringside.admin.createNetwork
 * @return Array containing name and key of new network.
 */
class AdminCreateNetwork extends Api_DefaultRest
{
	protected $m_appName;
	
	protected $m_properties;

	public function validateRequest()
	{
		
		//make sure calling application is a default application
		$this->checkDefaultApp();
		
		$this->m_properties = array();
		// Ad-hoc networks have no name
		$this->m_properties['name'] = $this->getApiParam('name');
		$this->m_properties['auth_url'] = $this->getRequiredApiParam('auth_url');
		$this->m_properties['login_url'] = $this->getRequiredApiParam('login_url');
		$this->m_properties['canvas_url'] = $this->getRequiredApiParam('canvas_url');
		$this->m_properties['web_url'] = $this->getRequiredApiParam('web_url');
	}

	public function execute()
	{
		$netKeys = Api_Bo_Network::createNetwork($this->getUserId(), $this->m_properties);
		
		//return response
		$response = array();
		if($netKeys !== false)
		{
			$response['network'] = array();
			$response['network']['name'] = isset($this->m_properties['name'])?$this->m_properties['name']:$netKeys[0];
			$response['network']['key'] = $netKeys[0];
			$response['network']['secret'] = $netKeys[1];
		}
		
		return $response;
	}
}

?>
