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
require_once ("ringside/api/bo/Network.php");
require_once ("ringside/api/bo/Users.php");

/**
 * Gets the properties of a Ringside network (Administrator only).
 *
 * @author Mike Schachter
 * @apiName AdminGetNetworkProperties
 * @apiOptional nids A comma-separated list of network IDs to query
 * @apiOptional properties A comma-separated list of properties to query for. Possible
 * 				 values include:
 *  				 key - the network ID
 * 				 name - network name
 * 				 auth_url - the authorization url for the network (like login.php)
 *					 login_url - URL user is redirected to after login
 * 				 canvas_url - URL of canvas renderer for network.
 * 				 web_url - URL of network's web root
 * 				 social_url - URL of social rendering endpoint
 * 				 auth_class - class name of Authorization plugin
 * 				 postmap_url - URL user is redirected to after mapping takes place
 * @callMethod ringside.admin.getNetworkProperties
 * @return Array an array of associative arrays, one for each network.
 */
class AdminGetNetworkProperties extends Api_DefaultRest
{
	protected $m_nids;
	protected $m_props;
	protected static $fieldMap;

	public function validateRequest()
	{
		$this->m_nids = array();
		$nids = $this->getApiParam('nids', '');
		if((strlen(trim($nids)) > 0))
		{
			$this->m_nids = explode(',', $nids);
		}
		
		$this->m_props = array();
		$props = $this->getRequiredApiParam('properties', '');
		if((strlen(trim($props)) > 0))
		{
			$this->m_props = explode(',', $props);
		}
	}

	/**
	 * Execute the api call to get user app list.
	 */
	public function execute()
	{
		$isAdmin = Api_Bo_Users::isUserAdmin($this->getUserId());
		if(! $isAdmin)
		{
			throw new Exception('Only the administrator view network properties.');
		}
		
		$this->checkDefaultApp();
		
		$enids = array();
		foreach($this->m_nids as $nid)
		{
			$enids[] = $nid;
		}
		
		$eprops = array();
		foreach($this->m_props as $name=>$val)
		{
			$ename = $name;
			$eval = $val;
			$eprops[$ename] = $eval;
		}
		
		$props = Api_Bo_Network::getNetworkProperties($this->getUserId(), $enids, $eprops);
		
		$response = array('networks' => $props);
		return $response;
	}
}

?>
