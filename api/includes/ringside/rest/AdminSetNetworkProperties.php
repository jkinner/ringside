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
require_once 'ringside/api/DefaultRest.php';
require_once 'ringside/api/bo/Users.php';
require_once 'ringside/api/bo/Network.php';

/**
 * Sets the properties of a Ringside network (Administrator only).
 *
 * @author Mike Schachter
 * @apiName AdminSetNetworkProperties
 * @apiRequired nid The network ID.
 * @apiRequired properties JSON-encoded array of associative arrays,
 * 			    where each row can have the following key-value pairs:
 *  				 key - the network ID
 * 				 name - network name
 * 				 auth_url - the authorization url for the network (like login.php)
 *					 login_url - URL user is redirected to after login
 * 				 canvas_url - URL of canvas renderer for network.
 * 				 web_url - URL of network's web root
 * 				 social_url - URL of social rendering endpoint
 * 				 auth_class - class name of Authorization plugin
 * 				 postmap_url - URL user is redirected to after mapping takes place
 * @callMethod ringside.admin.setNetworkProperties
 * @return boolean true if network properties were set successfully.
 */
class AdminSetNetworkProperties extends Api_DefaultRest
{
	protected $m_nid;
	
	protected $m_props;
	
	protected static $fieldMap;

	public function validateRequest()
	{
		$this->m_nid = $this->getRequiredApiParam('nid');
		$this->m_props = json_decode($this->getRequiredApiParam('properties'), true);
	}

	/**
	 * Execute the api call to get user app list.
	 */
	public function execute()
	{
		$isAdmin = Api_Bo_Users::isUserAdmin($this->getUserId());
		if(! $isAdmin)
		{
			throw new Exception('Only the administrator set network properties.');
		}
		
		$this->checkDefaultApp();
		
		$enid = $this->m_nid;
		$eprops = array();
		foreach($this->m_props as $name=>$val)
		{
			$ename = $name;
			$eval = $val;
			$eprops[$ename] = $eval;
		}
		Api_Bo_Network::setNetworkProperties($this->getUserId(), $enid, $eprops);
		
		return true;
	}
}

?>
