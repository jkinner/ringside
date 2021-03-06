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

require_once( "ringside/api/DefaultRest.php" );
require_once('ringside/social/IdMappingService.php');

/**
 * Returns all the info for this app_id in the users_app table, for this user
 */
class AdminMapUser extends Api_DefaultRest
{
    /** The subject ID on the calling network */
    private $sid;

    /** The network ID of the calling network */
    private $nid;
    
    /** The network ID of the other network */
    private $snid;

    /** The user ID on the other network */
    private $uid;

    /** The application ID performing the mapping */
    private $aid;
    
    /**
     * Validate required params.
     */
    public function validateRequest( ) {

        $this->uid = $this->getRequiredApiParam('uid');
        $this->nid = $this->getApiParam('nid', $this->getNetworkId());
        $this->sid = $this->getRequiredApiParam('sid');
        $this->snid = $this->getRequiredApiParam('snid');
        $this->aid = $this->getApiParam('aid', $this->getAppId());
    }

    /**
     * Execute the api call to get user app list.
     */
    public function execute()
    {
        error_log("Mapping user in the right method");
        $response = 1;
        $mapper = Social_IdMappingService::create();
        $mapper->link($this->aid, $this->nid, $this->uid, $this->snid, $this->sid);
        $response = 0;

        return $response;
    }
}

?>
