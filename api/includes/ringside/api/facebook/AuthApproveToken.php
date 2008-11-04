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
require_once( "ringside/api/AuthRest.php" );
/**
 * This is an internal API only should be used inter-system.
 * 
 * @author Richard Friedman
 */
class AuthApproveToken extends Api_AuthRest  {

   const PARAM_UID = 'uid';
   
    private $m_uid;
    private $m_authToken;
 
    public function validateRequest( ) {
        $this->m_uid = $this->getRequiredApiParam( self::PARAM_UID );
        $this->m_authToken = $this->getContext()->getAuthToken();
    }

    /**
     * This method should be invoked after a users has been authenticated. It is the responsible of the calling
     * system to have validate the user credentials.  The intial call of accessing the authorization token 
     * would have validated the user has access to the application.   This method is really for updating the 
     * session that the TOKEN has been approved for other calling applications. 
     *
     * @return The user ids that are the friends.
     * 
     * For example if the friend uids are 2,3,4 then the return value would be:
     * )
     */
    public function execute() {
        $response = array();

//        $this->checkDefaultApp();
        
		// TODO If we have an APP Key should validate user has app? 
		// TODO: Approval marker needs to be placed in the db instead of in the session and validated in AuthGetSession calls
		$this->setSessionValue( self::SESSION_APPROVED, true );
		$this->setSessionValue( self::SESSION_USER_ID , $this->m_uid );
		
		$response['result']='1';
        
        return $response;
    }
}

?>
