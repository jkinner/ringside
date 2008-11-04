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
require_once ("ringside/api/dao/UserAppSession.php");
require_once ("ringside/rest/UsersGetAppSession.php");

define('USER_APP_SESS_USER', '35100');
define('USER_APP_SESS_APP_NORMAL', '35000');
define('USER_APP_SESS_APP_DEFAULT', '35001');

class UsersGetAppSessionTestCase extends BaseAPITestCase
{

	public static function providerTestConstructor()
	{
		
		return array(// no app_id passed in
		array(USER_APP_SESS_USER, array(), USER_APP_SESS_APP_DEFAULT, false), // with app_id
		array(USER_APP_SESS_USER, array("app_id" => USER_APP_SESS_APP_NORMAL), USER_APP_SESS_APP_DEFAULT, true), // null user (no user calling)
		array(null, array("app_id" => USER_APP_SESS_APP_NORMAL), USER_APP_SESS_APP_DEFAULT, false), // nul users with user passed in
		array(null, array("app_id" => USER_APP_SESS_APP_NORMAL, "user_id" => USER_APP_SESS_USER), USER_APP_SESS_APP_DEFAULT, true));
	}

	/**
	 * @dataProvider providerTestConstructor
	 */
	public function testConstructor($uid, $params, $appId, $pass)
	{
		try
		{
			$uas = $this->initRest(new UsersGetAppSession(), $params, $uid, $appId);
			$this->assertTrue($pass);
		}catch(Exception $exception)
		{
			$this->assertFalse($pass);
		}
	}

	public static function providerTestExecute()
	{
		$tests = array();
		
		$response = array();
		$response['infinite'] = 'true';
		$response['session_key'] = 'uas-sess-1';
		$tests[] = array(USER_APP_SESS_USER, USER_APP_SESS_APP_NORMAL, USER_APP_SESS_USER, USER_APP_SESS_APP_DEFAULT, true, $response);
		
		return $tests;
	}

	/**
	 * @dataProvider providerTestExecute
	 */
	public function testExecute($uid, $aid, $ctxUid, $ctxAid, $defaultApp, $expected)
	{
		try
		{
			$infinite = $expected['infinite'] == 'true'? 1 : 0;
			$uas = Api_Dao_UserAppSession::getUserAppSession($uid, $aid);
			if(count($uas) == 0)
			{
				Api_Dao_UserAppSession::createUserAppSession($aid, $uid, $infinite, $expected['session_key']);
			}else
			{
				Api_Dao_UserAppSession::updateUserAppSession($aid, $uid, $infinite, $expected['session_key']);
			}
		}catch(Exception $e)
		{
			$this->fail("Could not setup test properly");
		}
		
		$params = array();
		if($ctxUid != $uid)
		{
			$params['user_id'] = $uid;
		}
		$params['app_id'] = $aid;
		
		$response = null;
		try
		{
			$method = $this->initRest(new UsersGetAppSession(), $params, $ctxUid, $ctxAid);
			$response = $method->execute();
		
		}catch(OpenFBAPIException $exception)
		{
			$this->assertFalse($defaultApp, "Unexpected exception " . $exception);
			return;
			//         $this->fail( "No exception expected " . $exception->getCode() );
		}
		
		// Validate that the calling application is a default application.
		$this->assertTrue($defaultApp, "Expected an error, app was not supposed to be a default app.");
		
		if(empty($response))
		{
			$this->assertArrayHasKey('session', $response, "Expected empty results (ie session='') ");
		}else
		{
			$this->assertEquals($expected['infinite'], $response['infinite'], "infinite response does not match");
			$this->assertEquals($expected['session_key'], $response['session_key'], "Session key response does not match.");
		}
	
	}
}
?>
