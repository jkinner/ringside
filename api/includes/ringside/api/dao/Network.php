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

require_once ('ringside/api/config/RingsideApiConfig.php');
require_once ('ringside/api/dao/records/RingsideUsersNetwork.php');
require_once ('ringside/api/dao/records/RingsideRsTrustAuthority.php');

class Api_Dao_Network
{
	protected static $fieldMap;
	
	protected static $reverseFieldMap;
	
	public static function getNetworksPropertiesForUser($uid)
	{
		$q = Doctrine_Query::create();
    	        $q->select('*')->from('RingsideRsTrustAuthority');//->where('n.user_id=$uid');
		return $q->execute();
	}

	/**
	 * Field map
	 *
	 * @return unknown
	 */
	protected static function initFieldMap()
	{
		if(self::$fieldMap == null)
		{
			self::$fieldMap = array('key' => 'trust_key', 'name' => 'trust_name', 'auth_url' => 'trust_auth_url', 'login_url' => 'trust_login_url', 'canvas_url' => 'trust_canvas_url', 'web_url' => 'trust_web_url', 'social_url' => 'trust_social_url', 'auth_class' => 'trust_auth_class', 'postmap_url' => 'trust_postmap_url');
			
			self::$reverseFieldMap = array_flip(self::$fieldMap);
		}
		return self::$fieldMap;
	}

	/**
	 * Determines if this user is a network owner
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $nids
	 * @return unknown
	 */
	public static function isNetworkOwner($uid, $nids)
	{
		$unids = self::getNetworksForUser($uid);
		$ids = $unids->toArray();
		foreach($nids as $nid)
		{
			if(! in_array($nid, $ids))
				return false;
		}
		return true;
	}

	/**
	 * Returns the network ids for the user.
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $dbCon
	 * @return unknown
	 */
	public static function getNetworksForUser($uid)
	{
		$q = Doctrine_Query::create();
		$q->select('network_id')->from('RingsideUsersNetwork n')->where("user_id=$uid");
		
		return $q->execute();
	}

	public static function getNetworkProperties($uid, $nids = array(), $props = null)
	{
		self::initFieldMap();
		
		$fprops = array();
		if(($props != null) && (count($props) > 0))
		{
			foreach($props as $name)
			{
				if(! array_key_exists($name, self::$fieldMap))
				{
					throw new Exception("No such field '$name' found!");
				}
				$rname = self::$fieldMap[$name];
				$fprops[] = "$rname AS $name";
			}
		}else
		{
			foreach(self::$fieldMap as $name=>$rname)
			{
				$fprops[] = "$rname AS $name";
			}
		}
		
		$q = Doctrine_Query::create();
		
		$fldStr = implode(',', $fprops);
		$q->select($fldStr)->from('RingsideRsTrustAuthority r');
		
		if(count($nids) > 0)
		{
			foreach($nids as $indx=>$nid)
			{
				$nids[$indx] = "'$nid'";
			}
			
			$nid_list = implode(',', $nids);
			$q->where("trust_key in ($nid_list)");
		}
		
		return $q->execute();
	}

	/**
	 * gets the network name
	 *
	 * @param unknown_type $nid
	 * @param unknown_type $dbCon
	 * @return unknown
	 */
	protected static function getNetworkName($nid)
	{
		$q = Doctrine_Query::create();
		$q->select('trust_name')->from('RingsideRsTrustAuthority a')->where("trust_key='$nid'");
		
		$authorities = $q->execute();
		return $authorities->trust_name;
	}

	public static function setNetworkProperties($uid, $nid, $props)
	{
		self::initFieldMap();
		if(count($props) == 0)
			throw new Exception('No properties to set!');
		
		$oldKey = $nid;
		$newKey = $nid;
		if(array_key_exists('key', $props))
		{
			if($oldKey != $props['key'])
			{
				if(! self::checkUniqueKey($props['key']))
				{
					throw new Exception('API key specified is not unique.');
				}
			}
			$newKey = $props['key'];
		}
		
		$oldName = self::getNetworkName($oldKey, $dbCon);
		if(array_key_exists('name', $props))
		{
			if($oldName != $props['name'])
			{
				if(! self::checkUniqueName($props['name']))
				{
					throw new Exception('Network name specified is not unique.');
				}
			}
		}
		
		$q = Doctrine_Query::create();
		$rows = $q->update('RingsideRsTrustAuthority');
		foreach($props as $name=>$val)
		{
			if(! array_key_exists($name, self::$fieldMap))
			{
				throw new Exception("No such field '$name' found!");
			}
			$rname = self::$fieldMap[$name];
			$q->set($rname, "'$val'");
		}
		
		$q->where("trust_key='$nid'")->execute();
		
		if($oldKey != $newKey)
		{
			$q = Doctrine_Query::create();
			$rows = $q->update('RingsideUsersNetwork n')->set('network_id', "'$newKey'")->where("network_id='$oldKey'")->execute();
		}
	}

	public static function checkUniqueKey($val)
	{
		return self::checkUnique($val, 'trust_key');
	}

	public static function checkUniqueName($val)
	{
		return self::checkUnique($val, 'trust_name');
	}

	/**
	 * Checks to see if a trust authority is unique
	 *
	 * @param unknown_type $val
	 * @param unknown_type $fld
	 * @return unknown
	 */
	protected static function checkUnique($val, $fld)
	{
		self::initFieldMap();
		$q = Doctrine_Query::create();
		$q->select('trust_name')->from('RingsideRsTrustAuthority a')->where("$fld='$val'");
		
		$authorities = $q->execute();
		
		if(count($authorities) > 0)
		{
			return false;
		}
		return true;
	}

	/**
	 * Creates a trust authority and a users network
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $nid
	 * @param unknown_type $props
	 */
	public static function createNetwork($uid, $nid, $props)
	{
		self::initFieldMap();
		if(! self::checkUniqueKey($nid))
		{
			throw new Exception("Cannot create network, key '$nid' already exists");
		}
		
		$name = $props['name'];
		$authUrl = $props['auth_url'];
		$loginUrl = $props['login_url'];
		$canvasUrl = $props['canvas_url'];
		$webUrl = $props['web_url'];
		
		if(! self::checkUniqueName($name))
		{
			throw new Exception("Cannot create network, name '$name' already exists");
		}
		
		$authority = new RingsideRsTrustAuthority();
		$authority->trust_key = $nid;
		$authority->trust_name = $name;
		$authority->trust_auth_url = $authUrl;
		$authority->trust_login_url = $loginUrl;
		$authority->trust_canvas_url = $canvasUrl;
		$authority->trust_web_url = $webUrl;
		$authority->save();
		
//		$network = new RingsideUsersNetwork();
//		$network->user_id = $uid;
//		$network->network_id = $nid;
//		$ret = $network->trySave();
		
//		if($ret)
//		{
			return $authority->getIncremented();
//		}
		
		return false;
	}

	/**
	 * Deletes a Trust Authority and a Users Network
	 *
	 * @param unknown_type $nid
	 */
	public static function deleteNetwork($nid)
	{
		$q = new Doctrine_Query();
		$q->delete('RingsideRsTrustAuthority')->from('RingsideRsTrustAuthority a')->where("trust_key='$nid'")->execute();
		
		$q = new Doctrine_Query();
		return $q->delete('RingsideUsersNetwork')->from('RingsideUsersNetwork n')->where("network_id='$nid'")->execute();
	}

}
?>
