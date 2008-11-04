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

require_once ('PHPUnit/Framework.php');
require_once ('ringside/m3/util/StopWatch.php');

class StopWatchTestCase extends PHPUnit_Framework_TestCase
{
    public function testStopWatch()
    {
        $sw = new M3_Util_StopWatch();
        $this->assertFalse($sw->isStarted());
        $this->assertEquals(0.0, $sw->getTime());

        $sw->start();
        $this->assertTrue($sw->isStarted());
        sleep(1);
        $_time1 = $sw->getTime();
        $this->assertGreaterThan(0.9, $_time1, "Should have been at least 1s");
        $this->assertLessThan(1.2, $_time1, "Should have been about 1s");

        $sw->stop();
        $this->assertFalse($sw->isStarted());
        sleep(1);
        $_time2 = $sw->getTime();
        $this->assertEquals($_time1, $_time2, "watch was stopped - should have reported the same value as before", 0.1);

        $sw->start();
        $this->assertTrue($sw->isStarted());
        sleep(1);
        $_time2 = $sw->getTime();
        $this->assertGreaterThan(1.9, $_time2, "Should have been at least 2s now");
        $this->assertLessThan(2.2, $_time2, "Should have been close to 2s");
        
        $sw->reset();
        $this->assertFalse($sw->isStarted());
        $this->assertEquals(0.0, $sw->getTime());
    }
}

?>