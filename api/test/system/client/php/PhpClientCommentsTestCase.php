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

require_once('BasePhpClientTestCase.php');
require_once('ringside/api/clients/RingsideApiClientsConfig.php');

define ( 'GET_TC_USER_00', 81000 );
define ( 'GET_TC_USER_01', 81001 );
define ( 'GET_TC_USER_02', 810002 );
define ( 'GET_TC_COMMENT_AID', 34000 );
define ( 'GET_TC_AID', 34001 );
define( 'GET_TC_TEXT', "CommentsGetTestCase : " );

class CommentTestCase extends BasePhpClientTestCase {
    
//   public function setUp() {
//
//      require_once("sql/AllTests-teardown.sql");
//      require_once("sql/AllTests-setup.sql");
//
//      $GLOBALS['facebook_config']['debug'] = true;
//   }
    
   public function testComments() {

      global $socialApiKey;
      global $socialSecretKey;
      
      $fbClient = $this->fbClient;

      $authToken = $fbClient->auth_createToken();
      $res = $fbClient->auth_approveToken( GET_TC_USER_00 );
      $this->assertEquals("1", $res["result"]);
      $fbClient->auth_getSession($authToken);

      $xid = 'rest-' . time();
      for ( $i = 0 ; $i < 20; $i++ ) {
         $result = $fbClient->comments_add( $xid , "" . $i, GET_TC_AID );
         $this->assertTrue( $result == 1, "Comment not added " . var_export( $result, true )  );
      }

      $result = $fbClient->comments_count( $xid, GET_TC_AID );
      $this->assertEquals( $result, 20, "Get does not return expected amount ($result)" );
      
      $result =  $fbClient->comments_get( $xid, null, null, GET_TC_AID );
      $this->assertEquals( count($result), 10, "Number of comments return not matching ()" );

      // loop through retrieved and delete them.
      
   }

}
?>
