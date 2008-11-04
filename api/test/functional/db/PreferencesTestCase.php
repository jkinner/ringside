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
require_once ('BaseDbTestCase.php');
require_once ("RsOpenFBDbTestUtils.php");
require_once ("ringside/api/dao/AppPrefs.php");

define('PREFERENCES_UID_1', 15000);
define('PREFERENCES_UID_2', 15001);
define('PREFERENCES_AID_1', 15100);

class Api_Dao_AppPrefsTestCase extends BaseDbTestCase
{

	public static function hasAppPrefs($appId, $userId)
	{
		if(! isset($appId) || ! isset($userId))
		{
			throw new Exception("Parameter Missing!", 0);
		}
		$appPrefs = Api_Dao_AppPrefs::getAppPrefsByAppIdAndUserId($appId, $userId);
		
		if(count($appPrefs) > 0)
		{
			return true;
		}
		
		return false;
	}

	public static function providerSetPreferences()
	{
		$preferences = array();
		$preferences[0] = 'a';
		
		$test[] = array(PREFERENCES_UID_1, PREFERENCES_AID_1, $preferences, $preferences);
		
		$preferences2 = array();
		$preferences2[1] = 'b';
		$preferences2[5] = 'c';
		
		$test[] = array(PREFERENCES_UID_1, PREFERENCES_AID_1, $preferences2, $preferences + $preferences2);
		
		return $test;
	}

	/**
	 * @dataProvider providerSetPreferences
	 */
	public function testSetPreferences($userId, $appId, $preferences, $expected)
	{
		$prefs = Api_Dao_AppPrefs::getAppPrefsByAppIdAndUserId($appId, $userId);
		$values = json_decode($prefs[0]->value, true);
		
		foreach($preferences as $key=>$value)
		{
			$values[$key] = $value;
		}
		$hasPrefs = self::hasAppPrefs($appId, PREFERENCES_UID_1, $values);
		
		if(! $hasPrefs)
		{
			Api_Dao_AppPrefs::createAppPrefs($appId, $userId, $values);
		}else
		{
			Api_Dao_AppPrefs::updateAppPrefs($appId, $userId, $values);
		}
		
		$prefs = Api_Dao_AppPrefs::getAppPrefsByAppIdAndUserId($appId, $userId);
		$this->assertEquals(json_encode($expected), $prefs[0]->value);
		
		$values = json_decode($prefs[0]->value, true);
		foreach($expected as $key=>$value)
		{
			$this->assertEquals($value, $values[$key], "requested ($key)");
		}
	}

	public function testSetUnsetPreferences()
	{
		$preferences = array();
		$preferences[0] = 'alpha';
		$preferences[4] = 'gamma';
		$preferences[99] = 'beta';
		
		$prefs = array();
		
		foreach($preferences as $key=>$value)
		{
			$prefs[$key] = $value;
		}
		
		Api_Dao_AppPrefs::createAppPrefs(PREFERENCES_AID_1, PREFERENCES_UID_2, $prefs);
		
		$appPrefs = Api_Dao_AppPrefs::getAppPrefsByAppIdAndUserId(PREFERENCES_AID_1, PREFERENCES_UID_2);
		$prefs = json_decode($appPrefs[0]->value, true);
		$this->assertEquals(json_encode($preferences), $appPrefs[0]->value);
		
		foreach($preferences as $key=>$value)
		{
			$this->assertEquals($value, $prefs[$key]);
			
			unset($prefs[$key]);
			Api_Dao_AppPrefs::updateAppPrefs(PREFERENCES_AID_1, PREFERENCES_UID_2, $prefs);
			
			$checkPref = Api_Dao_AppPrefs::getAppPrefsByAppIdAndUserId(PREFERENCES_AID_1, PREFERENCES_UID_2);
			$checkPrefs = json_decode($checkPref[0]->value, true);
			$this->assertEquals(null, $checkPrefs[$key]);
			$checkPref = null;
		
		}
	
	}

	public function testBadGetPreferences()
	{
		$prefs = Api_Dao_AppPrefs::getAppPrefsByAppIdAndUserId(0, 0);
		$this->assertEquals(0, count($prefs));
	}

}
?>
