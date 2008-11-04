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
require_once( "ringside/api/facebook/FriendsGet.php" );

define( 'FRIENDS_NO_FRIENDS' , '3000' );
define( 'FRIENDS_ONE_FRIEND' , '3001' );
define( 'FRIENDS_THREE_FRIENDS' , '3002' );
define( 'AMIGO_1' , '3003' );
define( 'AMIGO_2' , '3004' );
define( 'AMIGO_3' , '3005' );
define( 'FRIENDS_NOT_FRIENDS' , '3006' );
define( 'FRIENDS_API_KEY', '1000' );
 
class FriendsGetTestCase extends BaseAPITestCase {
    
   public function testConstructor()
   {
      $faf = $this->initRest( new FriendsGet(), array(), FRIENDS_NO_FRIENDS );
      $this->assertEquals( FRIENDS_NO_FRIENDS, $faf->getUserId() );
   }

   public static function providerTestExecute() {

      return array(
      array( FRIENDS_NO_FRIENDS, null),
      array( FRIENDS_ONE_FRIEND, array(AMIGO_1) ),
      array( FRIENDS_THREE_FRIENDS, array(AMIGO_1,AMIGO_2) ),
      array( FRIENDS_NOT_FRIENDS, array(AMIGO_3) )
      );
   }

   /**
    * @dataProvider providerTestExecute
    */
   public function testExecute( $uid, $compare )
   {
      $params = array();
      $faf = $this->initRest( new FriendsGet(), $params, $uid );
      $result = $faf->execute();
      
      $this->assertArrayHasKey( 'uid', $result );
      if (!is_array($result['uid'])) {
      	if ($compare != null) fail("Expected a result but got null.");
      } else {
          $this->assertSame( count($compare) , count($result['uid']), "Arrays were not same size" );
          foreach ( $compare as $value ) {
             $this->assertTrue( in_array( $value, $result['uid'] ), "Failed array testing for $value" ) ;
          }
      }
   }
}
?>
