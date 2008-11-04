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
require_once ("ringside/api/facebook/EventsGetMembers.php");

define('EVENTS_MEMBERS_APPID', '4100');

define('EVENTS_MEMBMER_USER_1', '4020');
define('EVENTS_MEMBMER_USER_2', '4021');
define('EVENTS_MEMBMER_USER_3', '4022');
define('EVENTS_MEMBMER_USER_4', '4023');
define('EVENTS_MEMBMER_USER_5', '4024');
define('EVENTS_MEMBMER_NO_EVENTS', '4025');

define('EVENT_WITH_NO_MEMBERS', '4120');
define('EVENT_WITH_ONE_MEMBERS', '4121');
define('EVENT_WITH_MANY_MEMBERS', '4122');
define('PRIVATE_EVENT_WITH_MEMBERS', '4123');
define('CLOSED_EVENT_WITH_MEMBERS', '4124');

class EventsGetMembersTestCase extends BaseAPITestCase
{
	
	public function testConstructor()
	{
		
		// throw exception
		try
		{
		   $params = array();
		   $method = $this->initRest( new EventsGetMembers(), $params, EVENTS_NOSUCHUSER );
			$this->assertFalse($method != null);
		}catch(OpenFBAPIException $exception)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode());
		}
		
		// Work nicely
		try
		{
		   $params = array('eid' => '1');
		   $method = $this->initRest( new EventsGetMembers(), $params, EVENTS_NOSUCHUSER );
			$this->assertTrue($method != null);
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("exception thrown, not good " . $exception);
		}
	
	}
	
	public function testNoSuchEvent()
	{
		try
		{
		   $params = array("eid" => '1'); 
		   $method = $this->initRest( new EventsGetMembers(), $params, EVENTS_MEMBMER_NO_EVENTS );
			$result = $method->execute();
			$this->assertArrayNotHasKey('attending', $result, $result);
			$this->assertTrue(count($result) == 0, "Number of items incorrect " . count($result));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
	
	}
	
	public function testGetOneMembers()
	{
		
		try
		{
		   $params = array("eid" => EVENT_WITH_ONE_MEMBERS); 
		   $method = $this->initRest( new EventsGetMembers(), $params, EVENTS_MEMBMER_NO_EVENTS );
			$result = $method->execute();
			$this->assertArrayHasKey('attending', $result, $result);
			$this->assertTrue(count($result ['attending'] ['uid']) == 1, "Number of items incorrect " . count($result));
			$this->assertArrayHasKey('declined', $result, $result);
			$this->assertTrue(count($result ['declined'] ['uid']) == 0, "Number of items incorrect " . count($result));
			$this->assertArrayHasKey('unsure', $result, $result);
			$this->assertArrayHasKey('not_replied', $result, $result);
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
	
	}
	
	public function testGetManyMembers()
	{
		
		try
		{
		   $params = array("eid" => EVENT_WITH_MANY_MEMBERS); 
		   $method = $this->initRest( new EventsGetMembers(), $params, EVENTS_MEMBMER_NO_EVENTS );
			$result = $method->execute();
			$this->assertArrayHasKey('attending', $result, $result);
			$this->assertTrue(count($result ['attending'] ['uid']) == 2, "Number of items incorrect " . count($result));
			$this->assertArrayHasKey('declined', $result, $result);
			$this->assertTrue(count($result ['declined'] ['uid']) == 1, "Number of items incorrect " . count($result));
			$this->assertArrayHasKey('unsure', $result, $result);
			$this->assertTrue(count($result ['unsure'] ['uid']) == 1, "Number of items incorrect " . count($result));
			$this->assertArrayHasKey('not_replied', $result, $result);
			$this->assertTrue(count($result ['not_replied'] ['uid']) == 1, "Number of items incorrect " . count($result));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
	
	}
	
	public function testGetWithPrivateMembers()
	{
		try
		{
		   $params = array("eid" => PRIVATE_EVENT_WITH_MEMBERS); 
		   $method = $this->initRest( new EventsGetMembers(), $params, EVENTS_MEMBMER_NO_EVENTS );
			$result = $method->execute();
			$this->assertArrayNotHasKey('attending', $result, $result);
			$this->assertTrue(count($result) == 0, "Number of items incorrect 0 != " . count($result));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
		
		try
		{
		   $params = array("eid" => PRIVATE_EVENT_WITH_MEMBERS); 
		   $method = $this->initRest( new EventsGetMembers(), $params, EVENTS_MEMBMER_USER_1 );
			$result = $method->execute();
			$this->assertArrayHasKey('attending', $result, $result);
			$this->assertTrue(count($result ['attending'] ['uid']) == 2, "Number of items incorrect " . count($result));
			$this->assertArrayHasKey('declined', $result, $result);
			$this->assertTrue(count($result ['declined'] ['uid']) == 1, "Number of items incorrect " . count($result));
			$this->assertArrayHasKey('unsure', $result, $result);
			$this->assertTrue(count($result ['unsure'] ['uid']) == 1, "Number of items incorrect " . count($result));
			$this->assertArrayHasKey('not_replied', $result, $result);
			$this->assertTrue(count($result ['not_replied'] ['uid']) == 1, "Number of items incorrect " . count($result));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
	
	}
	
	public function testGetWithClosedMembers()
	{
		try
		{
		   $params = array("eid" => CLOSED_EVENT_WITH_MEMBERS); 
		   $method = $this->initRest( new EventsGetMembers(), $params, EVENTS_MEMBMER_NO_EVENTS );
			$result = $method->execute();
			$this->assertArrayNotHasKey('attending', $result, $result);
			$this->assertTrue(count($result) == 0, "Number of items incorrect 0 != " . count($result));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
		
		try
		{
		   $params = array("eid" => CLOSED_EVENT_WITH_MEMBERS); 
		   $method = $this->initRest( new EventsGetMembers(), $params, EVENTS_MEMBMER_USER_1 );
			$result = $method->execute();
			$this->assertArrayHasKey('attending', $result, $result);
			$this->assertTrue(count($result ['attending'] ['uid']) == 2, "Number of items incorrect " . count($result));
			$this->assertArrayHasKey('declined', $result, $result);
			$this->assertTrue(count($result ['declined'] ['uid']) == 1, "Number of items incorrect " . count($result));
			$this->assertArrayHasKey('unsure', $result, $result);
			$this->assertTrue(count($result ['unsure'] ['uid']) == 1, "Number of items incorrect " . count($result));
			$this->assertArrayHasKey('not_replied', $result, $result);
			$this->assertTrue(count($result ['not_replied'] ['uid']) == 1, "Number of items incorrect " . count($result));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
	
	}
}
