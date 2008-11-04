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
require_once 'ringside/api/dao/records/RingsideAppPref.php';

/**
 * Each application/user combination can have preferences. 
 * The preferences are ID=>Value, so no arbitrary string keys. 
 * The value can be 128 bytes each. 
 * Currently the underlying structure is a medium text which stores the whole maps as a json encoded string.
 * At this makes the request a single call with a large result set returned.  The map can be quickly unserialized
 * back into an array. 
 * 
 * @author Richard Friedman
 */
class Api_Dao_AppPrefs
{
	/**
	 * Get an apps preferences for a specific user.
	 *
	 * @param integer $uid
	 * @param integer $appId
	 * @param dbconnection $dbCon
	 */
	public static function getAppPrefsByAppIdAndUserId($appId, $uid)
	{
		$q = Doctrine_Query::create();
		$q->select('ap.app_id, ap.user_id, ap.value, ap.modified')->from('RingsideAppPref ap')->where("user_id = $uid and app_id = $appId");
		return $q->execute();
	}

	/**
	 * Creates an app prefs object for the given app and user combo
	 *
	 * @param unknown_type $appId
	 * @param unknown_type $userId
	 * @param unknown_type $value
	 * @return unknown
	 */
	public function createAppPrefs($appId, $userId, array $value)
	{
		$pref = new RingsideAppPref();
		$pref->app_id = $appId;
		$pref->user_id = $userId;
		$pref->value = json_encode($value);
		$ret = $pref->trySave();
		
		if($ret)
		{
			//return $pref->getIncremented();
			return true;
		}
		return false;
	}

	/**
	 * Update the app prefs for the given app and user combo
	 *
	 * @param unknown_type $appId
	 * @param unknown_type $userId
	 * @param unknown_type $value
	 */
	public function updateAppPrefs($appId, $userId, array $value)
	{
		$q = Doctrine_Query::create();
		$q->update('RingsideAppPref')->set('value', '?', json_encode($value))->where("app_id = $appId AND user_id = $userId");
		return $q->execute();
	}
}

?>
