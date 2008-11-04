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

require_once ('ringside/m3/event/IResponseTimeEvent.php');
require_once ('ringside/m3/event/ITupleEvent.php');
require_once ('ringside/m3/event/SimpleEvent.php');
require_once ('ringside/m3/event/Tuple.php');

/**
 * A "tuple event" that indicates something happened that took a certain amount of time to complete.
 * 
 * @author John Mazzitelli
 * 
 * @see M3_Event_ITupleEvent
 * @see M3_Event_IResponseTimeEvent
 */
class M3_Event_ResponseTimeTupleEvent 
extends M3_Event_SimpleEvent
implements M3_Event_ITupleEvent, M3_Event_IResponseTimeEvent
{
    private $tuple;
    private $duration;

    /**
     * Creates the event.
     * 
     * @param $kind the type of response time this event represents
     * @param $when indicates when the event occurred; if not specified, the current time is used
     * @param $tuple indicates who or what causes the event to trigger
     * @param $duration the time the thing this event represents took to complete
     */
    public function __construct($kind, $when = null, M3_Event_Tuple $tuple = null, $duration = 0.0)
    {
        parent::__construct($kind, $when);
        
        if (empty($tuple))
        {
            $tuple = new M3_Event_Tuple();
        }
        
        $this->tuple = $tuple;
        $this->duration = $duration;
    }

    /**
     * @return M3_Event_Tuple the tuple identifying the user, app, and/or network that triggered the event.
     */
    public function getTuple()
    {
        return $this->tuple;
    }

    /**
     * @return float the length of time the thing this event represents took to complete
     */
    public function getDuration()
    {
        return $this->duration;
    }
}
?>