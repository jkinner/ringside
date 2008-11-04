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
require_once( 'BaseAPITestCase.php' );
require_once( "ringside/api/OpenFBAPIException.php" );
require_once( "ringside/rest/SubscribeUsersToApp.php" );
require_once( "ringside/rest/SubscriptionsGet.php" );
require_once( "ringside/rest/AdminGetPaymentGateway.php" );
require_once( "ringside/rest/AdminSetPaymentGateway.php" );

/**
 * @author Brian R. Robinson brobinson@ringsidenetworks.com
 */
class SubscribeUsersToAppTestCase extends BaseAPITestCase 
{
    public static function providerExecute() {
    	$friends2 = '36071, 36072';
        $params2 = array( 'uid'=>36070, 'nid'=>36020, 'aid'=>36060, 'planid'=>36083,
            'cctype'=>'100', 'ccn'=>'4111111111111111', 'expdate'=>'01/2013', 'friends'=>$friends2, 
            'firstname'=>'George', 'lastname'=>'Costanza', 'email'=>'george@costanza.com', 'phone'=>'555-555-5555' );

        $friends3 = '36071, 36072, 36073';
        $params3 = array( 'uid'=>36070, 'nid'=>36020, 'aid'=>36060, 'planid'=>36083,
            'cctype'=>'100', 'ccn'=>'4111111111111111', 'expdate'=>'01/2013', 'friends'=>$friends3, 
            'firstname'=>'Jerry', 'lastname'=>'Seinfeld', 'email'=>'george@costanza.com', 'phone'=>'555-555-5555' );
        return array(
            array( 36070, $params2, 36060, false ),
            array( 36070, $params3, 36060, true )
        );
    }

    /**
     * @dataProvider providerExecute
     */
    public function testExecute( $uid, $params, $appId, $expectingException )
    {
        try {
            //check for provisioned payment gateway
            $otheruid = 36100;
            $otherAppId = 36110;
            $gatewayParams = array();
            $method = $this->initRest( new AdminGetPaymentGateway(), $gatewayParams, $otheruid, $otherAppId );
            $response = $method->execute();

            if( !array_key_exists( 'gateway', $response ) || empty( $response[ 'gateway' ] ) ) {
                //provision payment gateway
                $gatewayParams = array( 'type' => 'AuthorizeNetSubscriptions', 'subject' => '23xS4XXt5',
                    'password' => '255AKw3697uzbDnM' );
                $method = $this->initRest( new AdminSetPaymentGateway(), $gatewayParams, $otheruid, $otherAppId );
                $resp = $method->execute();
            }
            
            //add
            $api = $this->initRest( new SubscribeUsersToApp(), $params, $uid, $appId );
            $save_error_level = error_reporting(E_ERROR);
            $result = $api->execute();
            error_reporting($save_error_level);
            if( $expectingException ) {
                $this->fail( 'Exception should have been thrown but was not.' );
            }
//          error_log( 'params: ' . print_r( $params, true ) );
//            error_log( 'result: ' . print_r( $result, true ) );
            $this->assertTrue( array_key_exists( 'subscription', $result ) );
            $this->assertTrue( array_key_exists( 'gateway_subscription_id', $result[ 'subscription' ] ) );
            $this->assertTrue( isset( $result[ 'subscription' ][ 'gateway_subscription_id' ] ) && 
                !empty( $result[ 'subscription' ][ 'gateway_subscription_id' ] ) );
            $this->assertTrue( array_key_exists( 'uid', $result[ 'subscription' ] ) );
            $this->assertEquals( $params[ 'uid' ], $result[ 'subscription' ][ 'uid' ] );
            $this->assertTrue( array_key_exists( 'aid', $result[ 'subscription' ] ) );
            $this->assertEquals( $params[ 'aid' ], $result[ 'subscription' ][ 'aid' ] );
            $this->assertTrue( array_key_exists( 'nid', $result[ 'subscription' ] ) );
            $this->assertEquals( $params[ 'nid' ], $result[ 'subscription' ][ 'nid' ] );
            $this->assertTrue( array_key_exists( 'planid', $result[ 'subscription' ] ) );
            $this->assertEquals( $params[ 'planid' ], $result[ 'subscription' ][ 'planid' ] );
            if( isset( $params[ 'firstname' ] ) ) $this->assertTrue( array_key_exists( 'firstname', $result[ 'subscription' ] ) );
            if( isset( $params[ 'lastname' ] ) ) $this->assertTrue( array_key_exists( 'lastname', $result[ 'subscription' ] ) );
            if( isset( $params[ 'email' ] ) ) $this->assertTrue( array_key_exists( 'email', $result[ 'subscription' ] ) );
            if( isset( $params[ 'phone' ] ) ) $this->assertTrue( array_key_exists( 'phone', $result[ 'subscription' ] ) );
            
            //get
            $api = $this->initRest( new SubscriptionsGet(), $params, $uid, $appId );
            $result = $api->execute();
//            error_log( 'subscription get result: ' . print_r( $result, true ) );
            
            //verify
            $this->assertTrue( !empty( $result ) );
            $this->assertTrue( array_key_exists( 'subscriptions', $result ) );
            $subscription = $result[ 'subscriptions' ][0];
            $this->assertEquals( $params[ 'uid' ], $subscription[ 'uid' ] );
            $this->assertEquals( $params[ 'nid' ], $subscription[ 'network_id' ] );
            $this->assertEquals( $params[ 'planid' ], $subscription[ 'plan_id' ] );
            $this->assertEquals( $params[ 'aid' ], $subscription[ 'aid' ] );
            $this->assertTrue( isset( $subscription[ 'gateway_subscription_id' ] ) );
            $this->assertTrue( !empty( $subscription[ 'gateway_subscription_id' ] ) );
            
        } catch ( OpenFBAPIException $e ) {
            if( !$expectingException ) {
                $this->fail( "Exception not expected " . $e->getMessage() . ' - ' . $e->getTraceAsString() );
            }
        }
    }
    
}

?>
