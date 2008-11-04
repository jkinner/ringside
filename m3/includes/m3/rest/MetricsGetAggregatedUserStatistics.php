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
require_once 'ringside/api/bo/Users.php';
require_once 'ringside/api/bo/Friends.php';
require_once 'ringside/api/bo/App.php';
require_once 'ringside/api/bo/Photos.php';
require_once 'ringside/api/bo/Util.php';

/**
 * M3 API that returns aggregated statistics related
 * to all the users registered in this local Ringside server.
 *
 * @author John Mazzitelli
 */
class MetricsGetAggregatedUserStatistics extends M3_AbstractRest
{
    private $totalUsers;
    
    /**
     * Returns an associative array that contains the statistics
     * related to users.
     *
     * @return array associative array of statistics
     */
    public function execute()
    {
        $_pastTimestamp = Api_Bo_Util::getPastTimestamp(24 * 60 * 60); // "recent data" is for past 24 hours

        $this->totalUsers = Api_Bo_Users::getTotalCountOfUserProfiles();

        $_stats = array();
        $_stats['total_users'] = $this->totalUsers;
        $_stats['recently_added_users'] = Api_Bo_Users::getTotalCountOfUserProfiles($_pastTimestamp);
        $_stats['recently_added_friends'] = Api_Bo_Friends::getTotalCountOfFriends($_pastTimestamp);
        $_stats['friends_per_user'] = $this->getAverageCountOfFriendsPerUser();
        $_stats['recently_registered_apps_per_user'] = $this->getAverageCountOfRecentlyRegisteredAppsPerUser($_pastTimestamp);
        $_stats['mapped_identities_per_user'] = $this->getAverageCountOfMappedIdentitiesPerUser();
        $_stats['photos_per_user'] = $this->getAverageCountOfPhotosPerUser();

        return $_stats;
    }
    
        /**
     * @return float number of friends that the average user has 
     */
    private function getAverageCountOfFriendsPerUser()
    {
        $_totalFriends = Api_Bo_Friends::getTotalCountOfFriends();
        return (float) $_totalFriends / $this->totalUsers;
    }

    /**
     * @param $pastTimestamp all friends added after this time are counted
     *                       you can use Api_Bo_Util::getPastTimestamp() to build this
     * @return float number of apps the average user has registered since the given timestamp 
     * @see Api_Bo_Util::getPastTimestamp()
     */
    private function getAverageCountOfRecentlyRegisteredAppsPerUser($pastTimestamp)
    {
        $_totalAppReg = Api_Bo_Users::getTotalCountOfUserAppRegistrations($pastTimestamp);
        return (float) $_totalAppReg / $this->totalUsers;
    }

    /**
     * @return float number of identities that the average user has 
     */
    private function getAverageCountOfMappedIdentitiesPerUser()
    {
        // TODO: RS-395 is needed to get this stat
        return 0;
    }

    /**
     * @return float number of photos that the average user has 
     */
    private function getAverageCountOfPhotosPerUser()
    {
        $_totalPhotos = Api_Bo_Photos::getTotalCountOfPhotos();
        return (float) $_totalPhotos / $this->totalUsers;
    }

}
?>