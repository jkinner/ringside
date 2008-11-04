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

require_once 'ringside/social/dsl/RingsideSocialDslParser.php';
require_once 'MockApplication.php';
require_once 'MockClient.php';

class fbUserTestCase extends PHPUnit_Framework_TestCase {

   public static function providerTestFbUser() {
      
      $mockUid = 12345;
      $mockFriends = array( "facebook.friends.areFriends"=>array( array('are_friends'=>'1')));
      $mockNotFriends = array( "facebook.friends.areFriends"=>array( array('are_friends'=>'0')));

      return array(
         array( $mockUid , $mockFriends, '<fb:user uid="12345">This information belongs to user 12345 and should only be shown to people who can see user 12345.</fb:user>' , "This information belongs to user 12345 and should only be shown to people who can see user 12345." ),
         array( $mockUid , $mockFriends, '<fb:user uid="11111">This information belongs to user 12345 and should only be shown to people who can see user 12345.</fb:user>' , "This information belongs to user 12345 and should only be shown to people who can see user 12345." ),
         array( $mockUid , $mockNotFriends, '<fb:user uid="67890">This information belongs to user 12345 and should only be shown to people who can see user 12345.</fb:user>' , "" )
      );
   }
   
   /**
    * @dataProvider providerTestFbUser
    */
   public function testFbUser ( $mockUid, $mockMethodResults, $parseString, $expected ) {
        $ma = new MockApplication();
        $ma->uid = $mockUid;
        $ma->client = new MockClient();
        $ma->client->method = $mockMethodResults;
        
        $parser = new RingsideSocialDslParser( $ma );
        
        $results = $parser->parseString( $parseString );
        
//        error_log("Results: '$results'");
        $this->assertEquals( $expected, trim($results) );
   }
    
}

?>
      
