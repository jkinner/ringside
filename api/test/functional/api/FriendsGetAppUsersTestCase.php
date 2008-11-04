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
require_once( "ringside/api/facebook/FriendsGetAppUsers.php" );

define( 'FRIENDS_APP_APIKEY' , 'test_case_key_3100' );
define( 'FRIENDS_FRIEND_WITHAPP1' , '3021' );
define( 'FRIENDS_FRIEND_WITHAPP2' , '3022' );
define( 'FRIENDS_FRIEND_WITHAPP3' , '3025' );
define( 'FRIENDS_FRIEND_NO_APP' , '3023' );
define( 'FRIENDS_NOTFRIEND_WITHAPP' , '3024' );

class FriendsGetAppUsersTestCase extends BaseAPITestCase {

   public static function providerTestConstructor() {
      
    return array(
         array( FRIENDS_FRIEND_WITHAPP1, array(), true ),
         array( FRIENDS_FRIEND_WITHAPP2, array( "api_key"=>FRIENDS_APP_APIKEY ), true )
      );
   }
   
   /**
    * @dataProvider providerTestConstructor
    */
   public function testConstructor( $uid, $params, $pass )
    {
        try {
            $friends = $this->initRest( new FriendsGetAppUsers(), $params, $uid );
            $this->assertTrue( $pass, "This test should have failed with exception!" );
            $this->assertNotNull($friends, "Object missing!");
        } catch ( OpenFBAPIException $exception ) {
            $this->assertFalse( $pass, "This test should have not thrown an exception! " . $exception );
           $this->assertEquals( FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode() );
        }
        
    }

   public static function providerTestExecute() {
      
    return array(
         array( FRIENDS_FRIEND_WITHAPP1, array(FRIENDS_FRIEND_WITHAPP2,FRIENDS_FRIEND_WITHAPP3) ),
         array( FRIENDS_FRIEND_WITHAPP2, array(FRIENDS_FRIEND_WITHAPP1)),
         array( FRIENDS_FRIEND_WITHAPP3, array(FRIENDS_FRIEND_WITHAPP1) ),
         array( FRIENDS_FRIEND_NO_APP, array(FRIENDS_FRIEND_WITHAPP1,FRIENDS_FRIEND_WITHAPP2) ),
         array( FRIENDS_NOTFRIEND_WITHAPP, array() )
      );
   }
    
   /**
    * @dataProvider providerTestExecute
    */
    public function testExecute( $uid, $compare )
    {
       $apiParams = array( 'api_key'=>FRIENDS_APP_APIKEY );
       $faf = $this->initRest( new FriendsGetAppUsers(), $apiParams, $uid );
        $result = $faf->execute();

        $this->assertEquals( count($compare) , count($compare)==0?count($result):count($result['uid']), "Arrays were not same size " );
        foreach ( $compare as $value ) {
           $this->assertTrue( in_array( $value, $result['uid'] ), "Failed array testing $value" ) ;
        }
        
    }
    
}
?>
