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
require_once ("ringside/rest/AuthCreateAppSession.php");

/**
 * @author Richard Friedman rfriedman@ringsidenetworks.com
 */

define('AUTH_CREATE_APP_SESSION_USER', '8000');
define('AUTH_CREATE_APP_SESSION_APP', '8102');
define('AUTH_CREATE_APP_SESSION_APIKEY', 'test_case_key_8102');
define('AUTH_CREATE_APP_SESSION_NETWORK', '8200');

/**
 * RatingsGet supports
 * XID (param)
 * FIRST (param)
 * COUNT (param)
 * AID (formal or context)
 * UID (formal)
 *
 * Response array of comments.
 */
class AuthCreateAppSessionTestCase extends BaseAPITestCase
{

	public static function providerExecute()
	{
		
		return array(array(AUTH_CREATE_APP_SESSION_USER, AUTH_CREATE_APP_SESSION_NETWORK, 'true', AUTH_CREATE_APP_SESSION_APP, AUTH_CREATE_APP_SESSION_APIKEY, false));
	}

	/**
	 * @dataProvider providerExecute
	 */
	public function testExecute($uid, $network, $infinite, $appId, $api_key, $fail)
	{
		$params = array();
		$params['uid'] = $uid;
		$params['network_key'] = $network;
		$params['infinite'] = $infinite;
		
		$session['api_key'] = $api_key;
		$session['session_id'] = 99;
		
		try
		{
			$appSession = $this->initRest(new AuthCreateAppSession(), $params, $uid, $appId, null, $session);
			$response = $appSession->execute();
			
			$this->assertFalse($fail, "Test should have thrown exception");
			$this->assertEquals("99-ringside", $response['session_key'], "Session key");
			$this->assertEquals($uid, $response['uid'], "userid");
			$this->assertEquals(99, $appSession->getSessionValue('session_id'), "Session ID");
			$this->assertEquals("session_key", $appSession->getSessionValue('type'), "Session KEY");
			$this->assertEquals("true", $appSession->getSessionValue('approved'), "Approved?");
			$this->assertEquals($infinite, $appSession->getSessionValue('infinite'), "Infinite?");
			if($infinite == 'true')
			{
				$this->assertEquals("never", $appSession->getSessionValue('expires'), "Expires");
			}else
			{
				$this->assertGreaterThan(time() + 5 * 60 - 20, $appSession->getSessionValue('expires'), "Expires");
			}
			
			$this->assertEquals("0", $appSession->getSessionValue('call_id'), "Call ID");
			$this->assertEquals(AUTH_CREATE_APP_SESSION_APP, $appSession->getAppId(), "Application ID");
			$this->assertEquals($api_key, $appSession->getSessionValue('api_key'), "API_KEY");
			$this->assertEquals($uid, $appSession->getSessionValue('uid'), "User ID");
			
			$this->assertEquals($network, $appSession->getNetworkId(), "Network ID");
		
		}catch(OpenFBAPIException $e)
		{
			$this->assertTrue($fail, "Should NOT have thrown exception ", $e->getTraceAsString());
		}
	
	}

}

?>
