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

require_once('BaseM3ApiTestCase.php');
require_once('m3/rest/MetricsGetApiDurations.php');
require_once('m3/rest/MetricsPurgeApiDurations.php');
require_once('ringside/m3/event/DispatcherFactory.php');

class MetricsPurgeApiDurationsTestCase extends BaseM3ApiTestCase
{
    public function testPurgeApiDurations()
    {
        // emit 2 metric events
        $_tuple = new M3_Event_Tuple();
        $_dispatcher = M3_Event_DispatcherFactory::createApiResponseTimeTupleDispatcher("some.api.name", $_tuple);
        $_dispatcher->startTimer();
        sleep(1);
        $_dispatcher->stopTimer();
        $_dispatcher->startTimer();
        sleep(1);
        $_dispatcher->stopTimer();

        // make sure we can aggregate those two metrics
        $_apiCall = $this->initRest( new MetricsPurgeApiDurations(), null);
        $_results = $_apiCall->execute();
        $this->assertGreaterThanOrEqual(2, $_results);

        // make sure we really deleted the data
        $_apiCall = $this->initRest( new MetricsGetApiDurations(), null);
        $_results = $_apiCall->execute();
        $this->assertTrue(is_array($_results));
        $this->assertTrue(is_array($_results['api_duration']));
        $this->assertEquals(0, count($_results['api_duration']));
    }
}
?>