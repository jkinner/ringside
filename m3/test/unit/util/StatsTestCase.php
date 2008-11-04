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
require_once ('ringside/m3/util/Stats.php');

class StatsTestCase extends PHPUnit_Framework_TestCase
{
    public function testStats()
    {
        $s = new M3_Util_Stats();
        $this->assertEquals(0, $s->getCount("foo"));
        $this->assertEquals(0, $s->getMin("foo"));
        $this->assertEquals(0, $s->getMax("foo"));
        $this->assertEquals(0, $s->getAverage("foo"));

        $s->addValue("foo", 1);
        $this->assertEquals(1, $s->getCount("foo"), "1wrong count");
        $this->assertEquals(1, $s->getMin("foo"), "1wrong min");
        $this->assertEquals(1, $s->getMax("foo"), "1wrong max");
        $this->assertEquals(1, $s->getAverage("foo"), "1wrong avg");

        $s->addValue("foo", 3);
        $this->assertEquals(2, $s->getCount("foo"), "2wrong count");
        $this->assertEquals(1, $s->getMin("foo"), "2wrong min");
        $this->assertEquals(3, $s->getMax("foo"), "2wrong max");
        $this->assertEquals(2, $s->getAverage("foo"), "2wrong avg");

        $s->addValue("foo", 5);
        $this->assertEquals(3, $s->getCount("foo"), "3wrong count");
        $this->assertEquals(1, $s->getMin("foo"), "3wrong min");
        $this->assertEquals(5, $s->getMax("foo"), "3wrong max");
        $this->assertEquals(3, $s->getAverage("foo"), "3wrong avg");
        
        $this->assertEquals(array("foo"), $s->getKeys());

        $s->addValue("bar", -1);
        $this->assertEquals(1, $s->getCount("bar"), "1wrong count");
        $this->assertEquals(-1, $s->getMin("bar"), "1wrong min");
        $this->assertEquals(-1, $s->getMax("bar"), "1wrong max");
        $this->assertEquals(-1, $s->getAverage("bar"), "1wrong avg");

        $s->addValue("bar", -3);
        $this->assertEquals(2, $s->getCount("bar"), "2wrong count");
        $this->assertEquals(-3, $s->getMin("bar"), "2wrong min");
        $this->assertEquals(-1, $s->getMax("bar"), "2wrong max");
        $this->assertEquals(-2, $s->getAverage("bar"), "2wrong avg");

        $s->addValue("bar", -5);
        $this->assertEquals(3, $s->getCount("bar"), "3wrong count");
        $this->assertEquals(-5, $s->getMin("bar"), "3wrong min");
        $this->assertEquals(-1, $s->getMax("bar"), "3wrong max");
        $this->assertEquals(-3, $s->getAverage("bar"), "3wrong avg");
        
        $this->assertEquals(array("foo", "bar"), $s->getKeys());

        $s->addValue("wot gorilla?", 1.5123);
        $this->assertEquals(1, $s->getCount("wot gorilla?"), "1wrong count");
        $this->assertEquals(1.5123, $s->getMin("wot gorilla?"), "1wrong min");
        $this->assertEquals(1.5123, $s->getMax("wot gorilla?"), "1wrong max");
        $this->assertEquals(1.5123, $s->getAverage("wot gorilla?"), "1wrong avg");

        $s->addValue("wot gorilla?", -0.575);
        $this->assertEquals(2, $s->getCount("wot gorilla?"), "2wrong count");
        $this->assertEquals(-0.575, $s->getMin("wot gorilla?"), "2wrong min");
        $this->assertEquals(1.5123, $s->getMax("wot gorilla?"), "2wrong max");
        $this->assertEquals(0.46865, $s->getAverage("wot gorilla?"), "2wrong avg");

        $s->addValue("wot gorilla?", 1.1);
        $this->assertEquals(3, $s->getCount("wot gorilla?"), "3wrong count");
        $this->assertEquals(-0.575, $s->getMin("wot gorilla?"), "2wrong min");
        $this->assertEquals(1.5123, $s->getMax("wot gorilla?"), "2wrong max");
        $this->assertEquals(0.6791, $s->getAverage("wot gorilla?"), "3wrong avg");
        
        $this->assertEquals(array("foo", "bar", "wot gorilla?"), $s->getKeys());
        
        $arr = $s->getStatsArray();
        $this->assertArrayHasKey("foo", $arr);
        $this->assertArrayHasKey(M3_Util_Stats::COUNT, $arr['foo']);
        $this->assertArrayHasKey(M3_Util_Stats::MIN, $arr['foo']);
        $this->assertArrayHasKey(M3_Util_Stats::MAX, $arr['foo']);
        $this->assertArrayHasKey(M3_Util_Stats::AVERAGE, $arr['foo']);
        $this->assertArrayNotHasKey(M3_Util_Stats::KEY, $arr['foo']);

        $this->assertArrayHasKey("bar", $arr);
        $this->assertArrayHasKey(M3_Util_Stats::COUNT, $arr['bar']);
        $this->assertArrayHasKey(M3_Util_Stats::MIN, $arr['bar']);
        $this->assertArrayHasKey(M3_Util_Stats::MAX, $arr['bar']);
        $this->assertArrayHasKey(M3_Util_Stats::AVERAGE, $arr['bar']);
        $this->assertArrayNotHasKey(M3_Util_Stats::KEY, $arr['bar']);

        $this->assertArrayHasKey("wot gorilla?", $arr);
        $this->assertArrayHasKey(M3_Util_Stats::COUNT, $arr['wot gorilla?']);
        $this->assertArrayHasKey(M3_Util_Stats::MIN, $arr['wot gorilla?']);
        $this->assertArrayHasKey(M3_Util_Stats::MAX, $arr['wot gorilla?']);
        $this->assertArrayHasKey(M3_Util_Stats::AVERAGE, $arr['wot gorilla?']);
        $this->assertArrayNotHasKey(M3_Util_Stats::KEY, $arr['wot gorilla?']);

        $flatArr = $s->getStatsFlatArray();
        $this->assertArrayNotHasKey("foo", $flatArr);
        $this->assertArrayNotHasKey("bar", $flatArr);
        $this->assertArrayNotHasKey("wot gorilla?", $flatArr);
        $this->assertEquals(3, count($flatArr));
        for($i = 0; $i < count($flatArr); $i++)
        {
            $this->assertArrayHasKey(M3_Util_Stats::COUNT, $flatArr[$i]);
            $this->assertArrayHasKey(M3_Util_Stats::MIN, $flatArr[$i]);
            $this->assertArrayHasKey(M3_Util_Stats::MAX, $flatArr[$i]);
            $this->assertArrayHasKey(M3_Util_Stats::AVERAGE, $flatArr[$i]);
            $this->assertArrayHasKey(M3_Util_Stats::KEY, $flatArr[$i]);
        }
        
        $unflattened = M3_Util_Stats::unflattenArray($flatArr);
        $this->assertSame($arr, $unflattened, "unflattening flat array should produce the same as the stats array");
    }
}

?>