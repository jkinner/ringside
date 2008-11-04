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

require_once ('ringside/m3/event/Tuple.php');
require_once ('ringside/m3/event/IEvent.php');

/**
 * This is an event that contains a "tuple" of information about the event.
 * An event can have a tuple that may or may not have all of the tuple attributes
 * defined.  Depending on the kind of event (which is another piece of data all events have), it's
 * possible that there is no user ID, for example. Listeners processing these events should examine
 * the tuple included in the event to see what is and is not supplied.
 * 
 * @author John Mazzitelli
 * 
 * @see M3_Event_Tuple
 */
interface M3_Event_ITupleEvent extends M3_Event_IEvent
{
    /**
     * The tuple of information that provides more information about the event,
     * most specifically about where it came from and who triggered it.
     *
     * @return M3_Event_Tuple the tuple related to this event
     */
    function getTuple();
}
?>