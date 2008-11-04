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

require_once('SuiteTestUtils.php');
require_once('ringside/api/clients/RingsideApiClientsConfig.php');
require_once('ringside/api/clients/RingsideApiClientsRest.php');

class BasePhpClientTestCase extends PHPUnit_Framework_TestCase
{
	protected $fbClient;	

	public function setUp()
	{	
		parent::setUp();

		require_once("sql/AllTests-teardown.sql");
		require_once("sql/AllTests-teardown.sql");
		
		$GLOBALS['facebook_config']['debug'] = false;
		$this->initClient(17001);
	}
		
	protected function initClient($uid)
	{
		unset($this->fbClient);
		$this->fbClient = new RingsideApiClientsRest("test_case_key-17100", "secretkey", null, RingsideApiClientsConfig::$serverUrl );
		$authToken = $this->fbClient->auth_createToken();		
		$res = $this->fbClient->auth_approveToken($uid);		
		$this->assertEquals("1", $res["result"]);		
		$this->fbClient->auth_getSession($authToken);
		
		$this->assertTrue(isset($this->fbClient));
	}
	
	public function testInit()
	{
		$this->assertNotNull($this->fbClient);
	}
	
}


?>
