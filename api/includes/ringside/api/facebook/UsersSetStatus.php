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

require_once( "ringside/api/OpenFBAPIException.php" );
require_once( "ringside/api/DefaultRest.php" );
require_once( "ringside/api/bo/Users.php" );

/**
 * users.isAppAdded API
 * 
 * @author Richard Friedman
 */
class UsersSetStatus extends Api_DefaultRest {

	private $m_status;
	private $m_clear;
	
    public function validateRequest( ) {
		
        $this->m_clear = $this->getApiParam('clear');
        if ( $this->m_clear == null ) {
			throw new OpenFBAPIException( "clear parameter must be specified.",  FB_ERROR_CODE_PARAMETER_MISSING );
		}
        
        if ( $this->m_clear == 0 ) {
        	$this->m_status = $this->getRequiredApiParam('status');
        } else if ( $this->m_clear == 1 ) {
        	$this->m_status = '';
        } else {
        	 throw new OpenFBAPIException( "clear parameter must be 1 or 0.",  FB_ERROR_CODE_INCORRECT_SIGNATURE );
        }
		
    }

    /**
     * Execute the users.hasAppPermission method
     *  
     */
    public function execute() 
    {
    	 $ret = Api_Bo_Users::setStatus($this->getUserId(), $this->getAppId(), $this->getSessionValue('api_key'), $this->getNetworkId(), $this->m_status, $this->m_clear);
    	 
    	 $response = array();
    	 $response['inserted'] = $ret !== false ? 1 : 0;
    	 return $response;
    }

}

?>
