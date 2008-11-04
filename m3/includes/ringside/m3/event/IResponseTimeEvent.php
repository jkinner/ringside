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

require_once ('ringside/m3/event/IEvent.php');

/**
 * An event that indicates something happened that took a certain amount of time to complete.
 * This interface introduces the "duration" attribute that indicates how long something
 * took to complete. The event kind will tell you more information about the event.
 * 
 * @author John Mazzitelli
 */
interface M3_Event_IResponseTimeEvent extends M3_Event_IEvent
{
    /**
     * Returns the time it took to complete "somethere" where that
     * "something" is defined by the rest of the event's attributes.
     * 
     * @return float the time it took to complete (usually as returned by microtime())
     */
    function getDuration();
}
?>