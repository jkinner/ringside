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
require_once( "ringside/api/bo/App.php" );

class AdminGetAppList extends Api_DefaultRest
{	
    public function validateRequest() {
    }
    
    /**
     * Execute the api call to get user app list.
     */
    public function execute()
    {
    	//$ret = Api_Bo_App::getAllApplications();
    	
    	// networkID cannot be set if we are returning all apps from a domain
    	// It's a security risk to accept domain keys via the API parameters
    	if($this->getNetworkId()) {
	        $domainService = Api_ServiceFactory::create('DomainService');
	        $ret = $domainService->getAppsByApiKey($this->getNetworkId());
        }
        else {
	        $appService = Api_ServiceFactory::create('AppService');
            $ret = $appService->getAllApps();
        }
        
    	$response = array();
		if ( empty($ret) ) {
			$response['apps'] = array();
		} else {
			$response['apps'] = $ret;
		}

		return $response;
    }
}

?>
