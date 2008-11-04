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
require_once( "ringside/api/bo/Subscriptions.php");
require_once( "Payment/Process.php" );
require_once( "Validate/US.php" );
require_once( "Validate.php" );

/**
 * Subscribe a user to an application.
 * 
 * @author Brian R. Robinson brobinson@ringsidenetworks.com
 */
class SubscribeUserToApp extends Api_DefaultRest
{
	private $uid;
   	private $nid;
   	private $aid;
   	private $planid;
   	private $subscriptionid;
   	private $cctype;   	
   	private $ccn;
   	private $expdate;
   	private $firstname;
   	private $lastname;
   	private $email;
   	private $phone;
   
   /**
    * Process input parameters in header.
    *
    * @param unknown_type $aid The application id for which to retrieve payment plans.
    */
   	public function validateRequest( ) {

//    	error_log( 'SubscribeUserToApp, api session: ' . var_export( $session, true ) );
//        error_log( 'SubscribeUserToApp, params: ' . var_export( $apiParams, true ) );

    	$this->aid = $this->getRequiredApiParam( 'aid' );
        $this->planid = $this->getRequiredApiParam( 'planid');
      	$this->uid = $this->getRequiredApiParam( 'uid' );
      	$this->nid = $this->getApiParam( 'nid', $this->getNetworkId() );
      	$this->cctype = $this->getRequiredApiParam( 'cctype' );
        $this->ccn = $this->getRequiredApiParam( 'ccn' );
        $this->expdate = $this->getRequiredApiParam( 'expdate' );

        $firstName = $this->getApiParam( 'firstname' );
    	if( $firstName != null ) {
            $this->firstname = $firstName;
        }
        
        $lastName = $this->getApiParam( 'lastname' );
        if( $lastName != null ) {
            $this->lastname = $lastName;
        }
        
        $email = $this->getApiParam('email');
        if( $email != null ) {
            $this->email = $email;
        }
        
        $phone = $this->getApiParam('phone');
        if( $phone != null ) {
            $this->phone = $phone;
        }
   	}
   
	public function execute() {

    	$this->checkDefaultApp( $this->aid );
    	if( isset( $this->email ) && !Validate::email( $this->email ) ) {
    		throw new OpenFBAPIException( 'Email address "' . $this->email . '" not valid.' );
    	}
    	if( isset( $this->phone ) && !Validate_US::phoneNumber( $this->phone ) ) {
            throw new OpenFBAPIException( 'Phone number "' . $this->phone . '" not valid.' );
    	}
    	
    	$result = Api_Bo_Subscriptions::createAppSubscription( $this->uid, $this->nid, $this->aid, $this->planid, $this->ccn,
            $this->cctype, $this->expdate, $this->firstname, $this->lastname, $this->email, $this->phone );
            
        return $result;
    }
}
?>
