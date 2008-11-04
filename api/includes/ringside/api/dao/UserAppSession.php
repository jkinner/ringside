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
require_once 'ringside/api/dao/records/RingsideUsersAppSession.php';

/**
 * Represents a row in the OpenFB users_app table.
 */
class Api_Dao_UserAppSession
{

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $aid
	 * @return unknown
	 */
	public static function getUserAppSession($uid, $aid)
	{
		$q = Doctrine_Query::create();
		$q->from('RingsideUsersAppSession')->where("uid = $uid AND aid = $aid");
		return $q->execute();
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $aid
	 * @return unknown
	 */
	public static function isUserAppSession($uid, $aid)
	{
		$q = Doctrine_Query::create();
		$q->select('count(aid) as aid_count')->from('RingsideUsersAppSession')->where("uid = $uid AND aid = $aid");
		$ret = $q->execute();
		
		if($ret[0]['aid_count'] > 0)
		{
			return true;
		}
		
		return false;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $aid
	 * @param unknown_type $uid
	 * @param unknown_type $infinite
	 * @param unknown_type $key
	 * @return unknown
	 */
	public static function createUserAppSession($aid, $uid, $infinite = 0, $key)
	{
		$session = new RingsideUsersAppSession();
		$session->aid = $aid;
		$session->uid = $uid;
        $session->infinite = ( empty( $infinite ) ? 0 : 1 );
		$session->session_key = $key;
        $ret = $session->trySave();
		
		if($ret)
		{
			return $session->getIncremented();
		}
		
		return false;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $aid
	 * @param unknown_type $uid
	 * @return unknown
	 */
	public static function deleteUserAppSession($aid, $uid)
	{
		$q = Doctrine_Query::create();
		$q->delete('RingsideUsersAppSession')->from('RingsideUsersAppSession')->where("aid=$aid AND uid=$uid");
		return $q->execute();
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $aid
	 * @param unknown_type $uid
	 * @param unknown_type $infinite
	 * @param unknown_type $key
	 * @return unknown
	 */
	public static function updateUserAppSession($aid, $uid, $infinite = 0, $key)
	{
		$q = Doctrine_Query::create();
		$q->update('RingsideUsersAppSession')->set('infinite', '?', $infinite)->set('session_key', '?', $key)->where("aid = $aid AND uid = $uid");
		return $q->execute();
	
	}
}
?>
