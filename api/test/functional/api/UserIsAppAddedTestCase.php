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
require_once ("ringside/api/facebook/UsersIsAppAdded.php");

define('USERSISAPPADDED_APPID', '6100');
define('USERSISAPPADDED_USER_WITH_APP_ENABLED', '6001');
define('USERSISAPPADDED_USER_WITH_APP_DISABLED', '6002');
define('USERSISAPPADDED_USER_WITHOUT_APP', '6003');
define('USERSISAPPADDED_NOSUCHUSER', '6099');

class UserIsAppAddedTestCase extends BaseAPITestCase
{
	
	private $appId = USERSISAPPADDED_APPID;

	public function testConstructor()
	{
		
		// no parameters
		try
		{
			$params = array();
			$method = $this->initRest(new UsersIsAppAdded(), $params, USERSISAPPADDED_NOSUCHUSER, $this->appId);
			$this->assertTrue($method != null);
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("No exception expected " . $exception->getCode());
		}
		
		// has parameters 
		try
		{
			$params = array();
			$method = $this->initRest(new UsersIsAppAdded(), $params, USERSISAPPADDED_USER_WITHOUT_APP, $this->appId);
			$this->assertTrue($method != null);
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception->getCode());
		}
	}

	public function testUserWithAppEnabled()
	{
		
		// pass in uid
		try
		{
			$params = array();
			$method = $this->initRest(new UsersIsAppAdded(), $params, USERSISAPPADDED_USER_WITH_APP_ENABLED, $this->appId);
			$result = $method->execute();
			$this->assertArrayHasKey('result', $result, $result);
			$this->assertSame('1', $result['result']);
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception->getTraceAsString());
		}
	
	}

	public function testUserWithAppDisabled()
	{
		// pass in uid
		try
		{
			$params = array();
			$method = $this->initRest(new UsersIsAppAdded(), $params, USERSISAPPADDED_USER_WITH_APP_DISABLED, $this->appId);
			$result = $method->execute();
			$this->assertArrayHasKey('result', $result, $result);
			$this->assertSame('0', $result['result']);
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception->getCode());
		}
	}

	public function testUserWithAppNotAvailaible()
	{
		// pass in uid
		try
		{
			$params = array();
			$method = $this->initRest(new UsersIsAppAdded(), $params, USERSISAPPADDED_USER_WITHOUT_APP, $this->appId);
			$result = $method->execute();
			$this->assertArrayHasKey('result', $result, $result);
			$this->assertSame('0', $result['result']);
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception->getCode());
		}
	}
}
?>
