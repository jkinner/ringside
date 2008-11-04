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
require_once ('BaseAPITestCase.php');
require_once ("ringside/api/OpenFBAPIException.php");
require_once ("ringside/api/facebook/DataSetUserPreference.php");

define('DATA_SETPREF_USER_ID', '18000');
define('DATA_SETPREF_APP_ID', '18100');

class DataSetUserPreferenceTestCase extends BaseAPITestCase
{

	public static function providerTestConstructor()
	{
		
		return array(array(DATA_SETPREF_USER_ID, array(), array(), FB_ERROR_CODE_PARAMETER_MISSING), array(DATA_SETPREF_USER_ID, array(), array('app_id' => DATA_SETPREF_APP_ID), FB_ERROR_CODE_PARAMETER_MISSING), array(DATA_SETPREF_USER_ID, array('pref_id' => 1, 'value' => 'alpha'), array('app_id' => DATA_SETPREF_APP_ID), 0), array(DATA_SETPREF_USER_ID, array('pref_id' => - 1, 'value' => 'alpha'), array('app_id' => DATA_SETPREF_APP_ID), FB_ERROR_CODE_PARAMETER_MISSING), array(DATA_SETPREF_USER_ID, array('pref_id' => 300, 'value' => 'alpha'), array('app_id' => DATA_SETPREF_APP_ID), FB_ERROR_CODE_PARAMETER_MISSING), array(DATA_SETPREF_USER_ID, array('pref_id' => 100, 'value' => str_repeat('1234567890', 30)), array('app_id' => DATA_SETPREF_APP_ID), FB_ERROR_CODE_PARAMETER_MISSING));
	}

	/**
	 * @dataProvider providerTestConstructor
	 */
	public function testConstructor($userId, $params, $session, $code)
	{
		try
		{
			$method = $this->initRest(new DataSetUserPreference(), $params, $userId, $session['app_id']);
			$this->assertTrue($code == 0);
		}catch(OpenFBAPIException $exception)
		{
			$this->assertEquals($code, $exception->getCode());
		}
	
	}

	public static function providerTestExecute()
	{
		
		return array(array(DATA_SETPREF_USER_ID, array('pref_id' => 1, 'value' => 'alpha'), array('app_id' => DATA_SETPREF_APP_ID)), array(DATA_SETPREF_USER_ID, array('pref_id' => 0, 'value' => 'first in'), array('app_id' => DATA_SETPREF_APP_ID)), array(DATA_SETPREF_USER_ID, array('pref_id' => 200, 'value' => 'last out'), array('app_id' => DATA_SETPREF_APP_ID)), array(DATA_SETPREF_USER_ID, array('pref_id' => 30, 'value' => 'gamma ray'), array('app_id' => DATA_SETPREF_APP_ID)), array(DATA_SETPREF_USER_ID, array('pref_id' => 80, 'value' => 'http://some.url.com/and?thing=rock'), array('app_id' => DATA_SETPREF_APP_ID)), array(DATA_SETPREF_USER_ID, array('pref_id' => 80, 'value' => ''), array('app_id' => DATA_SETPREF_APP_ID)), array(DATA_SETPREF_USER_ID, array('pref_id' => 30, 'value' => '0'), array('app_id' => DATA_SETPREF_APP_ID)));
	}

	/**
	 * @dataProvider providerTestExecute
	 */
	public function testExecute($userId, $params, $session)
	{
		try
		{
			$method = $this->initRest(new DataSetUserPreference(), $params, $userId, $session['app_id']);
			$response = $method->execute();
			$this->assertTrue(empty($response), "Method response should have been empty.");
			
			// Validate it was really set?
			$preference = Api_Dao_AppPrefs::getAppPrefsByAppIdAndUserId($session['app_id'], $userId);
			$prefs = json_decode($preference[0]->value, true);
			
			$value = $prefs[$params['pref_id']];
			if($params['value'] == '0' || $params['value'] == '')
			{
				$this->assertTrue(empty($value), "Preference ({$params['pref_id']}) should have been empty!");
			}else
			{
				$this->assertEquals($params['value'], $value);
			}
		
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Exception not expected!" . $exception->getMessage());
		}
	}
}
