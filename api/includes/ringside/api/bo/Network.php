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
require_once 'ringside/api/dao/Network.php';
require_once 'ringside/api/dao/User.php';
require_once 'ringside/api/bo/DomainService.php';

/**
 * @author mlugert@ringsidenetworks.com
 */
class Api_Bo_Network
{

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $userId
	 * @param unknown_type $props
	 * @return unknown
	 */
	public static function createNetwork($userId, $props)
	{
		if(empty($userId) || Api_Dao_User::isUserAdmin($userId))
		{
			//create unique network key
			$uniqueKey = false;
			$netKey = '';
			while(! $uniqueKey)
			{
				$netKey = '';
				for($k = 0; $k < 32; $k ++)
					$netKey .= dechex(mt_rand(1, 15));
				$uniqueKey = Api_Dao_Network::checkUniqueKey($netKey);
			}
			
			$eprops = array();
			foreach($props as $name=>$val)
			{
				$eprops[$name] = $val;
			}
			if ( ! isset($eprops['name']) ) {
			    $eprops['name'] = $netKey;
			}
			$ret = Api_Dao_Network::createNetwork($userId, $netKey, $eprops);
			
			$netSecret = '';
			for($k = 0; $k < 32; $k ++)
			    $netSecret .= dechex(mt_rand(1, 15));
			
			$ret = $ret && Api_Bo_DomainService::create()->createDomain($netKey, $eprops['web_url'], $netKey, $netSecret);

			if($ret !== false)
			{
				return array($netKey, $netSecret);
			}
			
			return false;
		}
		
		throw new Exception('Only the administrator can create networks. ('.$userId.')');
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $nid
	 * @return unknown
	 */
	public static function deleteNetwork($nid)
	{
		return Api_Dao_Network::deleteNetwork($nid);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $nid
	 * @param unknown_type $props
	 */
	public static function setNetworkProperties($uid, $nid, $props)
	{
		Api_Dao_Network::setNetworkProperties($uid, $nid, $props);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $nids
	 * @param unknown_type $props
	 * @return unknown
	 */
	public static function getNetworkProperties($uid, $nids = array(), $props = null)
	{
		return Api_Dao_Network::getNetworkProperties($uid, $nids, $props)->toArray();
	}
}

?>
