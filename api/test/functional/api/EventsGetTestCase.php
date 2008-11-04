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
require_once ("ringside/api/facebook/EventsGet.php");

define('EVENTS_APPID', '4100');
define('EVENTS_USER_WITH_ONE_EVENT', '4001');
define('EVENTS_USER_NO_EVENTS', '4002');
define('EVENTS_USER_WITH_PRIVATE_EVENTS', '4003');
define('EVENTS_USER_WITH_SEVEN_EVENTS', '4004');
define('EVENTS_USER_EVENT_AUTHOR', '4005');
define('EVENTS_USER_NOFRIENDS', '4006');
define('EVENTS_USER_TIME_EVENTS', '4007');
define('EVENTS_NOSUCHUSER', '4099');

define('EVENTS_EVENT_PRIVATE', '4101');
define('EVENTS_EVENT_OPEN_1', '4102');
define('EVENTS_EVENT_OPEN_2', '4103');
define('EVENTS_EVENT_OPEN_3', '4104');
define('EVENTS_EVENT_CLOSED_1', '4105');
define('EVENTS_EVENT_CLOSED_2', '4106');
define('EVENTS_EVENT_CLOSED_3', '4107');

define('EVENTS_EVENT_NOW', '4110');
define('EVENTS_EVENT_YESTERDAY', '4111');
define('EVENTS_EVENT_TOMORROW', '4112');

class EventsGetTestCase extends BaseAPITestCase
{

	
	public function testConstructor()
	{
		
		// no parameters
		try
		{
			$params = array();
			$method = $this->initRest(new EventsGet(), $params, EVENTS_NOSUCHUSER);
			
			$this->assertTrue($method != null);
		}catch(OpenFBAPIException $exception)
		{
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode());
		}
	
	}

	public function testEventUserWithOneEvent()
	{
		
		// pass in uid
		try
		{
			$params = array();
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_WITH_ONE_EVENT);
			$result = $method->execute();
			$this->assertArrayHasKey('event', $result, $result);
			$this->assertTrue(count($result['event']) == 1, "Number of items incorrect 1 != " . count($result['event']));
			$this->assertSame($result['event'][0]['eid'], EVENTS_EVENT_OPEN_1);
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
		
		// pass in uid in params
		try
		{
			$params = array('uid' => EVENTS_USER_WITH_ONE_EVENT);
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_EVENT_AUTHOR);
			$result = $method->execute();
			$this->assertArrayHasKey('event', $result, $result);
			$this->assertTrue(count($result['event']) == 1, "Number of items incorrect " . count($result['event']));
			$this->assertSame($result['event'][0]['eid'], EVENTS_EVENT_OPEN_1);
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
	
	}

	public function testUserNotFriends()
	{
		
		// pass in uid in params
		try
		{
			$params = array('uid' => EVENTS_USER_WITH_ONE_EVENT);
			$method = $this->initRest(new EventsGet(), $params, EVENTS_NOSUCHUSER);
			$result = $method->execute();
			$this->assertArrayNotHasKey('event', $result, $result);
			$this->assertTrue(count($result) == 0, "Number of items incorrect " . count($result));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
	
	}

	public function testEventUserWithManyEvents()
	{
		try
		{
			$params = array();
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_WITH_SEVEN_EVENTS);
			$result = $method->execute();
			$this->assertArrayHasKey('event', $result, $result);
			$this->assertEquals(count($result['event']), 7, "Number of items incorrect " . count($result['event']));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
		
		// Note result is 6, user has 6
		try
		{
			$params = array("uid" => EVENTS_USER_WITH_SEVEN_EVENTS);
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_NO_EVENTS);
			$result = $method->execute();
			$this->assertArrayHasKey('event', $result, $result);
			$this->assertTrue(count($result['event']) == 6, "Number of items incorrect " . count($result['event']));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
	
	}

	public function testEventUserWithNoEvents()
	{
		try
		{
			$params = array();
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_NO_EVENTS);
			$result = $method->execute();
			$this->assertTrue(empty($result), $result);
			$this->assertEquals(count($result['event']), 0, "Number of items incorrect 0 != " . count($result['event']));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
		
		try
		{
			$params = array("uid" => EVENTS_USER_NO_EVENTS);
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_WITH_SEVEN_EVENTS);
			$result = $method->execute();
			$this->assertTrue(empty($result), $result);
			$this->assertEquals(count($result['event']), 0, "Number of items incorrect 0 != " . count($result['event']));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
	
	}

	public function testEventUserWithPrivateEvents()
	{
		// User is authed user
		try
		{
			$params = array();
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_WITH_PRIVATE_EVENTS);
			$result = $method->execute();
			$this->assertArrayHasKey('event', $result, $result);
			$this->assertTrue(count($result['event']) == 1, "Number of items incorrect " . count($result['event']));
			$this->assertTrue($result['event'][0]['eid'] == EVENTS_EVENT_PRIVATE, $result['event']);
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
		
		// User is not authed
		try
		{
			$params = array("uid" => EVENTS_USER_WITH_PRIVATE_EVENTS);
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_NO_EVENTS);
			$result = $method->execute();
			$this->assertTrue(empty($result), $result);
			$this->assertEquals(count($result['event']), 0, "Number of items incorrect 0 != " . count($result['event']));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
	
	}

	public function testEventGetByEIDUserHasNoEvents()
	{
		// Authed user has no events, request close, open, private
		try
		{
			$params = array("eids" => EVENTS_EVENT_CLOSED_1 . "," . EVENTS_EVENT_CLOSED_2 . "," . EVENTS_EVENT_PRIVATE);
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_NO_EVENTS);
			$result = $method->execute();
			$this->assertArrayHasKey('event', $result, $result);
			$this->assertTrue(count($result['event']) == 2, "Number of items incorrect " . count($result['event']));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
	}

	public function testEventGetByEIDwithPrivateEvents()
	{
		// Authed user has private events request closed, open, private should get all
		try
		{
			$params = array("eids" => EVENTS_EVENT_CLOSED_1 . "," . EVENTS_EVENT_CLOSED_2 . "," . EVENTS_EVENT_PRIVATE);
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_WITH_PRIVATE_EVENTS);
			$result = $method->execute();
			$this->assertArrayHasKey('event', $result, $result);
			$this->assertTrue(count($result['event']) == 3, "Number of items incorrect " . count($result['event']));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
	}

	public function testEventGetByEIDandUIDandFriends()
	{
		// Authed user has no events, uid passed in has private and they are friends
		try
		{
			$params = array("uid" => EVENTS_USER_WITH_SEVEN_EVENTS, "eids" => EVENTS_EVENT_CLOSED_1 . "," . EVENTS_EVENT_CLOSED_2 . "," . EVENTS_EVENT_CLOSED_3 . "," . EVENTS_EVENT_PRIVATE);
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_NO_EVENTS);
			$result = $method->execute();
			$this->assertArrayHasKey('event', $result, $result);
			$this->assertTrue(count($result['event']) == 3, "Number of items incorrect " . count($result['event']));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
	}

	public function testEventGetByEIDandUIDandNotFriends()
	{
		
		// Authed user has no events, uid passed in has private and they are NOT friends
		try
		{
			$params = array("uid" => EVENTS_USER_NOFRIENDS, "eids" => EVENTS_EVENT_CLOSED_1 . "," . EVENTS_EVENT_CLOSED_2 . "," . EVENTS_EVENT_PRIVATE);
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_NO_EVENTS);
			$result = $method->execute();
			$this->assertArrayNotHasKey('event', $result, $result);
			$this->assertTrue(count($result) == 0, "Number of items incorrect " . count($result));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
		
		// Authed user has no events, uid passed in has private and they are NOT friends
		try
		{
			$params = array("uid" => EVENTS_USER_WITH_SEVEN_EVENTS, "eids" => EVENTS_EVENT_CLOSED_1 . "," . EVENTS_EVENT_CLOSED_2 . "," . EVENTS_EVENT_PRIVATE);
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_NOFRIENDS);
			$result = $method->execute();
			$this->assertArrayNotHasKey('event', $result, $result);
			$this->assertTrue(count($result) == 0, "Number of items incorrect " . count($result));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
	
	}

	public function testEventFilterRsvpStatusBadData()
	{
		try
		{
			$params = array("rsvp_status" => "whole lot of nothing");
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_WITH_SEVEN_EVENTS);
			$result = $method->execute();
			$this->assertTrue(empty($result), $result);
			$this->assertEquals(count($result['event']), 0, "Number of items incorrect 0 != " . count($result['event']));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
	}

	public function testEventFilterRsvpStatusAttending()
	{
		try
		{
			$params = array("rsvp_status" => "attending");
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_WITH_SEVEN_EVENTS);
			$result = $method->execute();
			$this->assertArrayHasKey('event', $result, $result);
			$this->assertTrue(count($result['event']) == 2, "Number of items incorrect " . count($result['event']));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
	}

	public function testEventFilterRsvpStatusNotSure()
	{
		try
		{
			$params = array("rsvp_status" => "unsure");
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_WITH_SEVEN_EVENTS);
			$result = $method->execute();
			$this->assertArrayHasKey('event', $result, $result);
			$this->assertTrue(count($result['event']) == 2, "Number of items incorrect " . count($result['event']));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
	}

	public function testEventFilterRsvpStatusDeclined()
	{
		try
		{
			$params = array("rsvp_status" => "declined");
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_WITH_SEVEN_EVENTS);
			
			$result = $method->execute();
			$this->assertArrayHasKey('event', $result, $result);
			$this->assertTrue(count($result['event']) == 2, "Number of items incorrect " . count($result['event']));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
	}

	public function testEventFilterRsvpStatusNotReplied()
	{
		try
		{
			$params = array("rsvp_status" => "not_replied");
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_WITH_SEVEN_EVENTS);
			
			$result = $method->execute();
			$this->assertArrayHasKey('event', $result, $result);
			$this->assertTrue(count($result['event']) == 1, "Number of items incorrect " . count($result['event']));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
	}

	public function testEventFilterStart()
	{
		
		try
		{
			$params = array("start_time" => $this->time);
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_TIME_EVENTS);
			$result = $method->execute();
			
			$this->assertArrayHasKey('event', $result, $result);
			$this->assertTrue(count($result['event']) == 2, "Number of items incorrect " . count($result['event']));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
	}

	public function testEventFilterEnd()
	{
		
		try
		{
			$params = array("end_time" => $this->time);
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_TIME_EVENTS);
			$result = $method->execute();
			$this->assertArrayHasKey('event', $result, $result);
			$this->assertTrue(count($result['event']) == 2, "Number of items incorrect " . count($result['event']));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
	}

	public function testEventFilterStartEnd()
	{
		
		try
		{
		   // Find today's event
			$params = array("start_time" => $this->time - 10, "end_time" => $this->time + 10);
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_TIME_EVENTS);
			$result = $method->execute();
			$this->assertArrayHasKey('event', $result, $result);
			$this->assertTrue(count($result['event']) == 1, "Number of items incorrect " . count($result['event']));
			$this->assertSame($result['event'][0]['eid'], EVENTS_EVENT_NOW);
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
		
		try
		{
		   // Find yesterdays event  
			$params = array("start_time" => $this->time - 10 - 1440, "end_time" => $this->time + 10 - 1440);
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_TIME_EVENTS);
			$result = $method->execute();
			$this->assertArrayHasKey('event', $result, $result);
			$this->assertTrue(count($result['event']) == 1, "Number of items incorrect " . count($result['event']));
			$this->assertSame($result['event'][0]['eid'], EVENTS_EVENT_YESTERDAY);
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
		
		try
		{
		   // Find tomorrows event
			$params = array("start_time" => $this->time - 10 + 1440, "end_time" => $this->time + 10 + 1440);
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_TIME_EVENTS);
			$result = $method->execute();
			$this->assertArrayHasKey('event', $result, $result);
			$this->assertTrue(count($result['event']) == 1, "Number of items incorrect " . count($result['event']));
			$this->assertSame($result['event'][0]['eid'], EVENTS_EVENT_TOMORROW);
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
		
		try
		{
			$params = array("start_time" => $this->time - 10 - 1440, "end_time" => $this->time + 10);
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_TIME_EVENTS);
			$result = $method->execute();
			$this->assertArrayHasKey('event', $result, $result);
			$this->assertTrue(count($result['event']) == 2, "Number of items incorrect " . count($result['event']));
			$this->assertSame($result['event'][0]['eid'], EVENTS_EVENT_NOW);
			$this->assertSame($result['event'][1]['eid'], EVENTS_EVENT_YESTERDAY);
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
		
		try
		{
			$params = array("start_time" => $this->time - 10, "end_time" => $this->time + 10 + 1440);
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_TIME_EVENTS);
			$result = $method->execute();
			$this->assertArrayHasKey('event', $result, $result);
			$this->assertTrue(count($result['event']) == 2, "Number of items incorrect " . count($result['event']));
			$this->assertSame($result['event'][0]['eid'], EVENTS_EVENT_NOW);
			$this->assertSame($result['event'][1]['eid'], EVENTS_EVENT_TOMORROW);
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
		
		try
		{
			$params = array("start_time" => $this->time - 10 - 1440, "end_time" => $this->time + 10 + 1440);
			$method = $this->initRest(new EventsGet(), $params, EVENTS_USER_TIME_EVENTS);
			$result = $method->execute();
			$this->assertArrayHasKey('event', $result, $result);
			$this->assertTrue(count($result['event']) == 3, "Number of items incorrect " . count($result['event']));
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
	
	}
}
