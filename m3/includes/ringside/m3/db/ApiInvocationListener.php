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

require_once ('ringside/m3/metric/IApiInvocationListener.php');
require_once ('ringside/api/bo/M3MeasApiCall.php');

/**
 * An object that receives events that indicate an API call has been invoked
 * and stores the info to the DB.
 * 
 * @author John Mazzitelli
 */
class M3_Db_ApiInvocationListener
implements M3_Metric_IApiInvocationListener
{
    /**
     * Processes the API invocation event.
     *
     * @param $event the API response time event that occurred
     */
    public function eventTriggered(M3_Event_IEvent $event)
    {
        if (!$event instanceof M3_Event_ResponseTimeTupleEvent)
        {
            throw new Exception("listener must only be given response time tuple events");
        }

        Api_Bo_M3MeasApiCall::insert($event);

        return;
    }
    
    public function getApiDurations()
    {
        $_results = Api_Bo_M3MeasApiCall::getApiDurations();
        return $_results;
    }
    
    public function deleteAllData()
    {
        return Api_Bo_M3MeasApiCall::purge(-1);
    }
    
}

?>