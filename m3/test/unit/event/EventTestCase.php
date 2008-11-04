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
require_once ('ringside/m3/event/Tuple.php');
require_once ('ringside/m3/event/IEvent.php');
require_once ('ringside/m3/event/IListener.php');
require_once ('ringside/m3/event/DispatcherFactory.php');

class TEST_DummyEvent implements M3_Event_IEvent
{
    public $id = 0;

    const KIND = 'dummykind';

    public function getKind()
    {
        return TEST_DummyEvent::KIND;
    }

    public function getOccurred()
    {
        return microtime(true);
    }
}

class SimpleEventListener implements M3_Event_IListener
{
    private $numEvents;
    private $lastEvent;

    public function eventTriggered(M3_Event_IEvent $event)
    {
        $this->numEvents++;
        $this->lastEvent = $event;
    }

    public function getNumEvents() { return $this->numEvents; }
    public function getLastEvent() { return $this->lastEvent; }
}

class EventTestCase extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $_e = new TEST_DummyEvent();
        $this->assertTrue(TEST_DummyEvent::KIND === $_e->getKind());
    }

    public function testDispatch()
    {
        $dispatcher = M3_Event_DispatcherFactory::getSimpleDispatcher();
        $listener = new SimpleEventListener();
        $dispatcher->addListener($listener);
        $dispatcher->dispatchEvent( new TEST_DummyEvent());
        $dispatcher->dispatchEvent( new TEST_DummyEvent());
        $this->assertEquals(2, $listener->getNumEvents());
        $this->assertEquals(TEST_DummyEvent::KIND, $listener->getLastEvent()->getKind());
    }

    public function testRemoveListener()
    {
        $event1 = new TEST_DummyEvent();
        $event2 = new TEST_DummyEvent();
        $event1->id = 1;
        $event2->id = 2;

        $dispatcher = M3_Event_DispatcherFactory::getSimpleDispatcher();
        $listener1 = new SimpleEventListener();
        $listener2 = new SimpleEventListener();
        $dispatcher->addListener($listener1);
        $dispatcher->addListener($listener2);
        $dispatcher->addListener($listener2); // just making sure this doesn't break things - should be a no-op

        $dispatcher->dispatchEvent($event1);
        $dispatcher->removeListener($listener2);
        $dispatcher->removeListener($listener2); // just making sure this doesn't break things - should be a no-op
        $dispatcher->dispatchEvent($event2);
        $this->assertEquals(2, $listener1->getNumEvents());
        $this->assertEquals(1, $listener2->getNumEvents());
        $this->assertEquals(TEST_DummyEvent::KIND, $listener1->getLastEvent()->getKind());
        $this->assertEquals(TEST_DummyEvent::KIND, $listener2->getLastEvent()->getKind());
        $this->assertEquals(2, $listener1->getLastEvent()->id);
        $this->assertEquals(1, $listener2->getLastEvent()->id);

        // just some sanity checking
        $this->assertType("M3_Event_IEvent", $listener1->getLastEvent());
        $this->assertType("M3_Event_IEvent", $listener2->getLastEvent());
        $this->assertType("TEST_DummyEvent", $listener1->getLastEvent());
        $this->assertType("TEST_DummyEvent", $listener2->getLastEvent());
    }

    public function testTupleConstruct()
    {
        $t = new M3_Event_Tuple(null, null, null);
        $this->assertNull($t->getNetworkId());
        $this->assertNull($t->getApplicationId());
        $this->assertNull($t->getUserId());

        $t = new M3_Event_Tuple(111, null, null);
        $this->assertEquals(111, $t->getNetworkId());
        $this->assertNull($t->getApplicationId());
        $this->assertNull($t->getUserId());

        $t = new M3_Event_Tuple(111, 222, null);
        $this->assertEquals(111, $t->getNetworkId());
        $this->assertEquals(222, $t->getApplicationId());
        $this->assertNull($t->getUserId());

        $t = new M3_Event_Tuple(111, 222, 333);
        $this->assertEquals(111, $t->getNetworkId());
        $this->assertEquals(222, $t->getApplicationId());
        $this->assertEquals(333, $t->getUserId());
    }
}

?>