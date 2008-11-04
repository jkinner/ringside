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

require_once ('ringside/m3/event/IListener.php');

/**
 * Interface to those listener implementations that listen for
 * events that occur when applications invoke server-side REST APIs.
 *
 * All implementations have the responsibility for storing their
 * metrics in some backing store and providing methods for extracting
 * the data from that backing store.
 * 
 * @author John Mazzitelli
 */

interface M3_Metric_IApiInvocationListener
extends M3_Event_IListener
{
    /**
     * Returns a stats object that provides you tabular API duration data.
     * You get count/min/max/average of all API calls.
     * You get no application or user information in the stats, you just
     * get the overall duration metrics for APIs.
     * 
     * @return M3_Util_Stats tabular data of all API duration metrics
     */
    function getApiDurations();
    
    /**
     * Purges all the API invocation data that was stored by this listener.
     * 
     * @return number of data deleted, or false on error
     */
    function deleteAllData();
}

?>