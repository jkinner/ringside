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

require_once 'ringside/api/bo/M3MeasApiCall.php';

// phing -Dsuite=bo -Dtest=M3MeasApiCallTestCase test-unit

class M3MeasApiCallTestCase extends PHPUnit_Framework_TestCase
{
	/**
	 * Setup any non test specific data here
	 */
	protected function setUp()
	{
	}

	/**
	 * Delete non test specific data here
	 */
	protected function tearDown()
	{
	}

	public static function provideInsertPurge()
	{
		$tests = array();
		$tests[] = array(new M3_Event_ResponseTimeTupleEvent("one", null, new M3_Event_Tuple(1,2,3), 5.4321));
		$tests[] = array(new M3_Event_ResponseTimeTupleEvent("two", null, new M3_Event_Tuple(), 5.4321)); // tuple has all nulls
		
		return $tests;
	}

	/**
	 * @dataProvider provideInsertPurge
	 */
	public function testInsertPurge(M3_Event_ResponseTimeTupleEvent $event)
    {
        $this->assertGreaterThan(0, Api_Bo_M3MeasApiCall::insert($event), "Failed to insert API call");
        $this->assertGreaterThan(0, Api_Bo_M3MeasApiCall::purge(-1), "Failed to purge all API calls");
	}

	public function testGetApiDurations()
    {
        $_event11 = new M3_Event_ResponseTimeTupleEvent("one", null, new M3_Event_Tuple(1,2,3), 10);
        $_event12 = new M3_Event_ResponseTimeTupleEvent("one", null, new M3_Event_Tuple(3,2,1), 2);
        $this->assertGreaterThan(0, Api_Bo_M3MeasApiCall::insert($_event11), "Failed to insert API call 11");
        $this->assertGreaterThan(0, Api_Bo_M3MeasApiCall::insert($_event12), "Failed to insert API call 12");
        
        $_dstats = Api_Bo_M3MeasApiCall::getApiDurations();
        $_durations = $_dstats->getStatsArray();
        $this->assertTrue($_durations != false);
        $this->assertEquals(1, count($_durations));
        $this->assertEquals(2, $_durations['one']['count']);
        $this->assertEquals(10, $_durations['one']['max']);
        $this->assertEquals(2, $_durations['one']['min']);
        $this->assertEquals(6, $_durations['one']['avg']);

        $_event21 = new M3_Event_ResponseTimeTupleEvent("two", null, new M3_Event_Tuple(11,22,33), 500);
        $_event22 = new M3_Event_ResponseTimeTupleEvent("two", null, new M3_Event_Tuple(33,22,11), 400);
        $this->assertGreaterThan(0, Api_Bo_M3MeasApiCall::insert($_event21), "Failed to insert API call 21");
        $this->assertGreaterThan(0, Api_Bo_M3MeasApiCall::insert($_event22), "Failed to insert API call 22");
        $_dstats = Api_Bo_M3MeasApiCall::getApiDurations();
        $_durations = $_dstats->getStatsArray();
        $this->assertTrue($_dstats !== false);
        $this->assertEquals(2, count($_durations));
        
        $this->assertGreaterThan(0, Api_Bo_M3MeasApiCall::purge(-1), "Failed to purge all API calls");
	}
}
?>