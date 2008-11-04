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

require_once ('ringside/m3/util/DatastoreEnum.php');
require_once ('ringside/m3/event/Tuple.php');
require_once ('ringside/m3/event/IDispatcher.php');
require_once ('ringside/m3/event/SimpleDispatcher.php');
require_once ('ringside/m3/event/ResponseTimeTupleDispatcher.php');
require_once ('ringside/m3/db/ApiInvocationListener.php');

/**
 * This factory contains static methods that allow the caller to obtain
 * event dispatchers and their listeners.
 *
 * There can be several dispatchers within the system, each organized to
 * send events for different subsystems.
 * 
 * Listeners are responsible for accepting events and storing information
 * about those events. Listeners also typically provide methods to
 * obtain information about the information they previously stored.
 * This allows listeners to provide aggregated information about event
 * data it has been collecting.
 *
 * @see M3_Event_IDispatcher
 * @see M3_Event_IListener
 *
 * @author John Mazzitelli
 */
class M3_Event_DispatcherFactory
{
    private static $simple;
    
    /**
     * Returns a simple event dispatcher that simply implements the dispatcher interface
     * with no additional functionality added to it. This returns a singletion dispatcher,
     * so it is reused each time this method is called (i.e. you get the same object
     * back no matter how many times you call this method).
     * 
     * @return simple dispatcher singleton
     */
    public static function getSimpleDispatcher()
    {
        if (!isset(self::$simple))
        {
            self::$simple = new M3_Event_SimpleDispatcher(); 
        }

        return self::$simple;
    }
    
    /**
     * Creates a new dispatcher that can handle response time tuple events that
     * occur due to an API being invoked.
     * 
     * @param $kind the API name which will be the event kind when emitted by the dispatcher
     * @param $tuple the client that is making the current request
     * 
     * @return M3_Event_ResponseTimeTupleDispatcher dispatcher for the events
     */
    public static function createApiResponseTimeTupleDispatcher($kind, M3_Event_Tuple $tuple)
    {
        $_dispatcher = new M3_Event_ResponseTimeTupleDispatcher($kind, $tuple);
        $_listener = self::createApiInvocationListener();
        $_dispatcher->addListener($_listener);
        return $_dispatcher;
    }
    
    /**
     * Creates a new listener that can listen for events when API invocations occur.
     * The returned listener object also provides methods that can be used to retrieve
     * aggregated information about the API invocations that have occurred.
     * 
     * @return M3_Metric_IApiInvocationListener the listener created
     */
    public static function createApiInvocationListener()
    {
        $_ds = M3_Util_Settings::getDatastore();

        if ($_ds->isDB())
        {
           $_listener = new M3_Db_ApiInvocationListener();
        }

        return $_listener;
    }
}
?>