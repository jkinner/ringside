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

require_once ('ringside/m3/event/ResponseTimeTupleEvent.php');
require_once ('ringside/m3/event/SimpleDispatcher.php');
require_once ('ringside/m3/util/StopWatch.php');

/**
 * A dispatcher that can send "response time tuple" events.  The response time
 * is determined by a caller calling "startTimer" followed later by
 * a call to "stopTimer".  The amount of time between the start and stop
 * calls is known as the "response time" - an event is emitted to indicate
 * this reponse time duration (the kind of event is defined by the value
 * passed to the constructor).
 *
 * Because this extends M3_Event_SimpleDispatcher, the caller is free to
 * dispatch standalone events as well (via the dispatchEvent method).
 *
 * @author John Mazzitelli
 */
class M3_Event_ResponseTimeTupleDispatcher extends M3_Event_SimpleDispatcher
{
    private $stopWatch;
    private $eventKind;
    private $tuple;

    /**
     * Creates a dispatcher that is associated with a request that has the given tuple.
     *
     * @param $kind the kind of events that will be dispatched to the listeners when the timer is stopped
     * @param $tuple identifies where current request is coming from - all events will be associated with this tuple
     */
    public function __construct($kind = "unknown", M3_Event_Tuple $tuple)
    {
        $this->eventKind = $kind;
        $this->tuple = $tuple;
        $this->stopWatch = new M3_Util_StopWatch();
    }

    /**
     * Returns the kind of events this dispatcher will dispatch to its listeners when
     * the timer is stopped. All events emitted when the timer is stopped will be of the
     * same "kind".
     * 
     * @return string the kind of events
     * 
     * @see M3_Event_IEvent->getKind()
     */
    public function getKind()
    {
        return $this->eventKind;
    }
    
    /**
     * Starts the timer within this dispatcher.  Caller should eventually call stop()
     * which will emit a M3_Event_ResponseTimeTupleEvent.
     */
    public function startTimer()
    {
        $this->stopWatch->start();
    }

    /**
     * Stops the timer (if it was started) and emits a M3_Event_ResponseTimeTupleEvent, dispatching it to
     * all listeners.
     */
    public function stopTimer()
    {
        $this->stopWatch->stop();
        $_duration = $this->stopWatch->getTime();
        $this->stopWatch->reset();

        $_responseTimeEvent = new M3_Event_ResponseTimeTupleEvent($this->eventKind, null, $this->tuple, $_duration);
        $this->dispatchEvent($_responseTimeEvent);
    }
}
?>