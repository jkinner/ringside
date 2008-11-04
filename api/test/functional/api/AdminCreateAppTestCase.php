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
require_once ("ringside/rest/AdminCreateApp.php");
require_once ("ringside/api/facebook/AdminGetAppProperties.php");

class AdminCreateAppTestCase extends BaseAPITestCase
{
	public static function providerTestConstructorFailures()
	{
		$tests = array();
		$aid = 17100;
		$session = array("app_id" => $aid);
		$tests[] = array(17001, $aid, $session, array());
		$aid = 3100;
		$session = array("app_id" => $aid);
		$tests[] = array(3021, $aid, $session, array("name" => "some new app"));
		return $tests;
	}
	/**
	 * @dataProvider providerTestConstructorFailures
	 */
	public function testConstructorFailures($uid, $appId, $session, $params)
	{
		$failed = false;
		try
		{
			$apiCall = $this->initRest(new AdminCreateApp(), $params, $uid, $appId);
		}catch(Exception $e)
		{
			//print "\n" . $e->getMessage() . "\n";
			$failed = true;
		}
		if(! $failed)
		{
			$this->fail("Constructor should have failed");
		}
	}
	public function testExecute()
	{
		$uid = 17001;
		$appId = 17100;
		$params = array("name" => "my new application");
		$apiCall = $this->initRest(new AdminCreateApp(), $params, $uid, $appId);
		$resp = $apiCall->execute();
		$this->assertTrue(isset($resp["app"]));
		$app = $resp["app"];
		$this->assertEquals("my new application", $app["name"]);
		$apiKey = $app["api_key"];
		$this->assertTrue(isset($apiKey));
		$secret = $app["secret"];
		$this->assertTrue(isset($secret));
		$params = array("properties" => json_encode(array("application_name", "icon_url")), "app_api_key" => $apiKey);
		$apiCall = $this->initRest(new AdminGetAppProperties(), $params, $uid, $appId);
		$response = $apiCall->execute();
		$actual = json_decode($response['result'], true);
		$this->assertEquals("my new application", $actual["application_name"]);
		$this->assertTrue($this->endsWith($actual["icon_url"], "/images/icon-app-default.png"), "failed asserting that ".$actual["icon_url"]." ended with: /images/icon-app-default.png");
	}
	protected function endsWith($str, $sub)
	{
		return (substr($str, strlen($str) - strlen($sub)) === $sub);
	}
}
?>
