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
require_once ('ringside/m3/event/IListener.php');

/**
 * Represents an object that can dispatch events to a set of listeners.
 * Users of the event subsystem will usually get an instance of one of these
 * in order to emit events.
 *
 * @see M3_Event_IListener
 *
 * @author John Mazzitelli
 */
interface M3_Event_IDispatcher
{
    /**
     * Dispatches an event to all of its listeners.
     *
     * @return string identifies the kind of event that this is
     *
     */
    function dispatchEvent(M3_Event_IEvent $event);

    /**
     * Adds the given listener so all events coming from this
     * M3_Event_IDispatcher will be dispatched to that listener.
     *
     * If the given listener already exists, it will still be assured to
     * be listening to all events.
     * 
     * @param $rListener the listener that will receive all events from this dispatcher
     */
    function addListener(M3_Event_IListener& $rListener);
    
    /**
     * Removes the given listener so all events coming from this
     * M3_Event_IDispatcher will no longer be dispatched to that listener.
     *
     * If the given listener does not exist, this function will do nothing.
     *
     * @param $listener the listener that will receive all events from this dispatcher
     */
    function removeListener(M3_Event_IListener& $listener);
    
    /**
     * Clears the dispatcher's internal list of listeners. After this call is made,
     * all events to be dispatched by this dispatcher will be dropped since there will
     * be no listeners to process them. You can add more listeners by later calling
     * the addListener method. 
     */
    function removeAllListeners();
    
    /**
     * @return an array of all listeners currently registered
     */
    function getAllListeners();
}

?>