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

/**
 * This base object encapsulates the main information about the source of an event
 * that was triggered by a typical request to a Ringside server.
 * 
 * A "tuple" consists of the following:
 * 
 * 1. Network ID - identifies the network that is associated with the event that occurred
 * 2. Application ID - the application that is associated with the event that occurred
 * 3. User ID - the user that caused the event to trigger
 * 
 * All of these may or may not be defined. Some or all may not be set.
 * 
 * @author John Mazzitelli
 */
class M3_Event_Tuple
{
    private $networkId;
    private $applicationId;
    private $userId;
    
    public function __construct($nid = null, $aid = null, $uid = null)
    {
        $this->networkId = $nid;
        $this->applicationId = $aid;
        $this->userId = $uid;
    }
    
    public function getNetworkId()     { return $this->networkId; }
    public function getApplicationId() { return $this->applicationId; }
    public function getUserId()        { return $this->userId; }
}
?>