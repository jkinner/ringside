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

require_once 'ringside/m3/AbstractRest.php';
require_once 'ringside/m3/event/DispatcherFactory.php';

/**
 * M3 API that returns metrics on API calls made by applications.
 *
 * @author John Mazzitelli
 */
class MetricsGetApiDurations extends M3_AbstractRest
{
    /**
     * Returns an array containing tabular data describing all the duration metrics
     * for APIs.  The array is a flat list, with each internal element of the list being
     * an associative array with keys: key, count, min, max, avg. The "key" value
     * is the name of the API method; the remaining values are the duration
     * metrics for that API. Note that this follows the convention of returning
     * a top-most associative array with a single element keyed with 'api_duration'. That
     * element value is the flat array described above.
     *
     * @return array all API duration metrics.
     */
    public function execute()
    {
        $_listener = M3_Event_DispatcherFactory::createApiInvocationListener();
        $_stats = $_listener->getApiDurations();
        $_arr = $_stats->getStatsFlatArray(); // we want it flat so the response can be described via XML Schema
        return array('api_duration' => $_arr);
    }
}
?>