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

require_once 'ringside/api/config/RingsideApiConfig.php';
require_once 'ringside/api/dao/records/RingsideM3MeasApiCall.php';
require_once 'ringside/api/bo/Util.php';
require_once 'ringside/m3/event/ResponseTimeTupleEvent.php';
require_once 'ringside/m3/util/Stats.php';

/**
 * Provides access to M3 measurements related to API calls.
 * 
 * @author John Mazzitelli
 */
class Api_Bo_M3MeasApiCall
{
    /**
     * Adds the given response time event as a new API call measurement data point.
     * 
     * @param M3_Event_ResponseTimeTupleEvent the API invocation event where the event "kind"
     *                                        is the name of the API that was invoked
     * 
     * @return int the ID of the new row in the database, false on error
     */
    public function insert(M3_Event_ResponseTimeTupleEvent $event)
    {
        $obj = new RingsideM3MeasApiCall();
        $obj->nid = $event->getTuple()->getNetworkId();
        $obj->aid = $event->getTuple()->getApplicationId();
        $obj->uid = $event->getTuple()->getUserId();
        $obj->api_name = $event->getKind();
        $obj->duration = $event->getDuration();

        if ($obj->nid <= 0) $obj->nid = null;
        if ($obj->aid <= 0) $obj->aid = null;
        if ($obj->uid <= 0) $obj->uid = null;

        $ret = $obj->trySave();

        if($ret)
        {
            return $obj->getIncremented();
        }

        return false;
    }

    /**
     * Deletes all API call measurement data points older than the given amount of seconds.
     * 
     * @param $oldest the age (in seconds) of the oldest records that will be kept. Records
     *                that are older than this value will be purged. Pass in -1 to purge everything.
     * 
     * @return the number of rows purged, or false on error
     */
    public function purge($oldest)
    {
        $q = Doctrine_Query::create();
        $q->delete('RingsideM3MeasApiCall')
          ->from('RingsideM3MeasApiCall m')
          ->where( 'created < ?', Api_Bo_Util::getPastTimestamp($oldest));
        return $q->execute();
    }
    
    /**
     * Returns statistics containing API duration data for all APIs.
     * 
     * @return M3_Util_Stats API duration statistics
     */
    public function getApiDurations()
    {
        $q = Doctrine_Query::create();
        $q->select('m.api_name api_name, count(m.id) count, min(m.duration) min, max(m.duration) max, avg(m.duration) avg')
          ->from('RingsideM3MeasApiCall m')
          ->groupBy('m.api_name');
        $_executeResults = $q->execute();
        $_listArray = Api_Bo_Util::convertCollectionAsListToArray($_executeResults);
        
        $_stats = array();
        foreach ($_listArray as $_arr)
        {
            $_stats[$_arr['api_name']] =
                array( M3_Util_Stats::COUNT   => $_arr['count'],
                       M3_Util_Stats::MIN     => $_arr['min'],
                       M3_Util_Stats::MAX     => $_arr['max'],
                       M3_Util_Stats::AVERAGE => $_arr['avg'] );
        }
        
        return new M3_Util_Stats($_stats);
    }
}

?>
