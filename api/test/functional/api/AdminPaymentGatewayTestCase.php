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
require_once('BaseAPITestCase.php');
require_once('ringside/api/OpenFBAPIException.php');
require_once('ringside/rest/AdminSetPaymentGateway.php');
require_once('ringside/rest/AdminGetPaymentGateway.php');

class AdminPaymentGatewayTestCase extends BaseAPITestCase
{

	public function testExecute() 
    {
    	//test non-admin user trying to provision gateway.  should not be allowed.
        $uid = 36101;
        $appId = 36110;
            
        $params = array( 'type' => 'AuthorizeNetSubscriptions', 'subject' => '23xS4XXt5',
            'password' => '255AKw3697uzbDnM' );
   	$method = $this->initRest( new AdminSetPaymentGateway(), $params, $uid, $appId );
        try {
            $resp = $method->execute();
            $this->fail( 'Should have thrown an exception since only an admin user can provision a gateway' );
        }
        catch( OpenFBAPIException $e ) {
            //do nothing, this is expected behavior
        }
        
        //test admin user provisioning gateway.  should be allowed.
    	$uid = 36100;
	   	$params = array( 'type' => 'AuthorizeNetSubscriptions', 'subject' => '23xS4XXt5',
            'password' => '255AKw3697uzbDnM' );
	   	$method = $this->initRest( new AdminSetPaymentGateway(), $params, $uid, $appId );
	   	$resp = $method->execute();
	   	
	   	$this->assertTrue( array_key_exists('type', $resp[ 'gateway' ] ) );
	   	$this->assertTrue( array_key_exists('subject', $resp[ 'gateway' ] ) );
        $this->assertTrue( array_key_exists('password', $resp[ 'gateway' ] ) );
	   	$this->assertEquals( 'AuthorizeNetSubscriptions', $resp[ 'gateway' ][ 'type' ] );
        $this->assertEquals( '23xS4XXt5', $resp[ 'gateway' ][ 'subject' ] );
        $this->assertEquals( sha1( '255AKw3697uzbDnM' ), sha1( $resp[ 'gateway' ][ 'password' ] ) );
        
        //test non-admin user retrieving gateway information.  should not be allowed.
        $uid = 36101;
        $params = array();
        $method = $this->initRest( new AdminGetPaymentGateway(), $params, $uid, $appId );
        try {
            $resp = $method->execute();
            fail( 'Should have thrown an exception since only an admin user can retrieve gateway information' );
        }
        catch( OpenFBAPIException $e ) {
            //do nothing, this is expected behavior
        }
        
        //test admin user retrieving gateway information.  should be allowed.
        $uid = 36100;
        $params = array();
        $method = $this->initRest( new AdminGetPaymentGateway(), $params, $uid, $appId );
        $response = $method->execute();
        
        $this->assertTrue( array_key_exists('type', $resp[ 'gateway' ] ) );
        $this->assertTrue( array_key_exists('subject', $resp[ 'gateway' ] ) );
        $this->assertTrue( array_key_exists('password', $resp[ 'gateway' ] ) );
        $this->assertEquals( 'AuthorizeNetSubscriptions', $resp[ 'gateway' ][ 'type' ] );
        $this->assertEquals( '23xS4XXt5', $resp[ 'gateway' ][ 'subject' ] );
        $this->assertEquals( sha1( '255AKw3697uzbDnM' ), sha1( $resp[ 'gateway' ][ 'password' ] ) );

        //test adding a second gateway.  should not be allowed.
        $params = array( 'type' => 'AuthorizeNetSubscriptions', 'subject' => '23xS4XXt5',
            'password' => '255AKw3697uzbDnM' );
        $method = $this->initRest( new AdminSetPaymentGateway(), $params, $uid, $appId );
        try {
            $resp = $method->execute();
            $this->fail( 'Should have thrown an exception since there may only be one payment gateway provisioned.' );
        }
        catch( OpenFBAPIException $e ) {
        	//do nothing, this is expected behavior
        }
    }
}
?>
