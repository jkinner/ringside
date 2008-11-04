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

class fbProfilePicTestCase extends PHPUnit_Framework_TestCase {

   public static function providerTestFbProfilePic() {
      
      $mockUid = 12345;
      $get_userinfo_result = array( "facebook.users.getInfo"=>array( array( 'first_name'=>'John', "last_name"=>"Smith", "pic"=>"http://photome/photo.jpg" ) ));

      $tag = "<fb:profile-pic uid='12345' size=\"square\" linked=\"false\" />";
      $result = '<img src="http://photome/photo.jpg" alt="John Smith" width="50"/>';
      $tests[] = array ( $mockUid, $get_userinfo_result, $tag, $result );
      
      $tag = "<fb:profile-pic uid='12345' />";
      $result = '<a href=\'' . RingsideSocialConfig::$webRoot . '/profile.php?id='.$mockUid.'\'><img src="http://photome/photo.jpg" alt="John Smith" width="50"/></a>';
      $tests[] = array ( $mockUid, $get_userinfo_result, $tag, $result );
      
      return $tests;
   }
   
   /**
    * @dataProvider providerTestFbProfilePic
    */   
   public function testFbProfilePic( $mockUid, $mockMethodResults, $parseString, $expected ) {
      
        $ma = new MockApplication();
        $ma->uid = $mockUid;
        $ma->client = new MockClient();
        $ma->client->method = $mockMethodResults;
        
        $parser = new RingsideSocialDslParser( $ma );
        
        $results = $parser->parseString( $parseString );
        
        $this->assertEquals( $expected, $results, "$expected != $results" );
   }
    
}

?>
