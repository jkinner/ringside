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

class M3PhpClientMetricsTestCase extends BaseM3PhpClientTestCase
{
    public function testGetApiDurations()
    {
        $_c = $this->getClient();
        $_results = $_c->metricsGetApiDurations(); // call it this time to generate a metric
        $_results = $_c->metricsGetApiDurations();
        $this->assertTrue(is_array($_results));
        $this->assertArrayHasKey('m3.metrics.getApiDurations', $_results, "Should have at least seen our M3 API used to get these results");
        $this->assertGreaterThanOrEqual(1, $_results['m3.metrics.getApiDurations']['count']);
        $this->assertGreaterThanOrEqual(0, $_results['m3.metrics.getApiDurations']['min']);
        $this->assertGreaterThanOrEqual(0, $_results['m3.metrics.getApiDurations']['max']);
        $this->assertGreaterThanOrEqual(0, $_results['m3.metrics.getApiDurations']['avg']);
    }

    public function testPurgeApiDurations()
    {
        $_c = $this->getClient();
        $_c->metricsGetApiDurations(); // call several times
        $_c->metricsGetApiDurations(); // call several times
        $_c->metricsGetApiDurations(); // call several times
        $_results = $_c->metricsGetApiDurations(); // call several times
        $this->assertTrue(is_array($_results));
        $this->assertArrayHasKey('m3.metrics.getApiDurations', $_results, "Should have at least seen our M3 API used to get these results");
        $this->assertGreaterThanOrEqual(3, $_results['m3.metrics.getApiDurations']['count']);
        
        // now purge data
        $_purge = $_c->metricsPurgeApiDurations();
        $this->assertTrue($_purge == true);
        $_results = $_c->metricsGetApiDurations();
        $this->assertTrue(is_array($_results));
        $this->assertArrayNotHasKey('m3.metrics.getApiDurations', $_results);
    }
}

?>