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
require_once( "ringside/api/facebook/FriendsAreFriends.php" );

   define( 'AREFRIENDS_1' , '3010' );
   define( 'AREFRIENDS_2' , '3011' );
   define( 'AREFRIENDS_3' , '3012' );
   define( 'AREFRIENDS_4' , '3013' );

class FriendsAreFriendsTestCase extends BaseAPITestCase {

   public function testConstructor()
    {
        $uid = 123;
        
        // test that uid1 is not part of the api params
        $apiParams = array();
        $apiParams[ 'uids2' ] = "2,3,4";
        $apiParams[ 'api_key' ] = "1";

        try {
            $faf = $this->initRest( new FriendsAreFriends(), $apiParams, $uid );
            $this->fail( "Should have gotten an exception." );
        } catch ( Exception $exception ) {
            $this->assertEquals( FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode() );
        }
        
        // test that uid2 is not part of api params
        $apiParams = array();
        $apiParams[ 'uids1' ] = "1,1,1";
        $apiParams[ 'api_key' ] = "1";
        
        try {
            $faf = $this->initRest( new FriendsAreFriends(), $apiParams, $uid );
            $this->fail( "Should have gotten an exception." );
        } catch ( Exception $exception ) {
            $this->assertEquals( FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode() );
        }

        // make sure array sizes are equal
        $apiParams = array();
        $apiParams[ 'uids1' ] = "1,1,1";
        $apiParams[ 'uids2' ] = "2,3";
        $apiParams[ 'api_key' ] = "1";
        
        try {
            $faf = $this->initRest( new FriendsAreFriends(), $apiParams, $uid );
            $faf->execute();
            $this->fail( "Should have gotten an exception." );
        } catch ( Exception $exception ) {
            $this->assertEquals( FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode() );
        }
        
        
        /* Test when both UIDS are empty */
        $apiParams = array();
        $apiParams[ 'api_key' ] = "1";
        try {
            $faf = $this->initRest( new FriendsAreFriends(), $apiParams, $uid );
        } catch ( Exception $exception ) {
            $this->assertEquals( FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode() );
        }

        /* Valid Test? */
        $apiParams = array();
        $apiParams[ 'uids1' ] = "1,1,1";
        $apiParams[ 'uids2' ] = "2,3,4";
        $apiParams[ 'api_key' ] = "1";
        
        $faf = $this->initRest( new FriendsAreFriends(), $apiParams, $uid );
        $this->assertEquals( $uid, $faf->getUserId() );
        $this->assertEquals( $apiParams, $faf->getContext()->getInitialRequest() );
    }

    public function testExecute()
    {
        $uid = 123;
        
        $apiParams = array();
        $apiParams[ 'uids1' ] = implode( ",", array( AREFRIENDS_1,AREFRIENDS_1,AREFRIENDS_2,AREFRIENDS_2 ));
        $apiParams[ 'uids2' ] = implode( ",", array( AREFRIENDS_2,AREFRIENDS_3,AREFRIENDS_4,AREFRIENDS_1 ));
        
        $uid = 1;
        $faf = $this->initRest( new FriendsAreFriends(), $apiParams, $uid );
        $result = $faf->execute();
        
        $fi = $result[ FB_FRIENDS_FRIEND_INFO ];
        $this->assertEquals( 4, count( $fi ) );
        $this->assertEquals( AREFRIENDS_1, $fi[ 0 ][ FB_FRIENDS_UID1 ] );
        $this->assertEquals( AREFRIENDS_2, $fi[ 0 ][ FB_FRIENDS_UID2 ] );
        $this->assertEquals( 1, $fi[ 0 ][ FB_FRIENDS_ARE_FRIENDS ] );
        
        $this->assertEquals( AREFRIENDS_1, $fi[ 1 ][ FB_FRIENDS_UID1 ] );
        $this->assertEquals( AREFRIENDS_3, $fi[ 1 ][ FB_FRIENDS_UID2 ] );
        $this->assertEquals( 1, $fi[ 1 ][ FB_FRIENDS_ARE_FRIENDS ] );
        
        $this->assertEquals( AREFRIENDS_2, $fi[ 2 ][ FB_FRIENDS_UID1 ] );
        $this->assertEquals( AREFRIENDS_4, $fi[ 2 ][ FB_FRIENDS_UID2 ] );
        $this->assertEquals( 0, $fi[ 2 ][ FB_FRIENDS_ARE_FRIENDS ] );
        
        $this->assertEquals( AREFRIENDS_2, $fi[ 3 ][ FB_FRIENDS_UID1 ] );
        $this->assertEquals( AREFRIENDS_1, $fi[ 3 ][ FB_FRIENDS_UID2 ] );
        $this->assertEquals( 1, $fi[ 3 ][ FB_FRIENDS_ARE_FRIENDS ] );
    }

}

?>
