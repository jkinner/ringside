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

require_once( "ringside/rest/CommentsGet.php" );
require_once( "ringside/rest/CommentsAdd.php" );
require_once( "ringside/rest/CommentsDelete.php" );
require_once( "ringside/rest/CommentsCount.php" );

/**
 * @author Richard Friedman rfriedman@ringsidenetworks.com
 */

define ( 'GET_TC_USER_00', 12000 );
define ( 'GET_TC_USER_01', 12001 ); 
define ( 'GET_TC_USER_02', 12002 );
define ( 'GET_TC_COMMENT_AID', 34000 );
define ( 'GET_TC_AID', 34001 );
define( 'GET_TC_TEXT', "CommentsGetTestCase : " );

/**
 * RatingsGet supports
 * XID (param)
 * FIRST (param)
 * COUNT (param)
 * AID (formal or context)
 * UID (formal)
 *
 * Response array of comments.
 */
class CommentsAllTestCase extends BaseAPITestCase 
{

   public static function providerConstructor() {

      return array(
      array( GET_TC_USER_00, array(), GET_TC_COMMENT_AID , true ),
      array( GET_TC_USER_00, array("xid"=>"xid1"), GET_TC_COMMENT_AID , false ),
      array( GET_TC_USER_00, array("xid"=>"xid2", "aid"=>""), GET_TC_COMMENT_AID , false),
      array( GET_TC_USER_00, array("xid"=>"xid3", "aid"=>GET_TC_AID), GET_TC_COMMENT_AID , false ),
      array( GET_TC_USER_00, array("xid"=>"xid4", "aid"=>"1231241"), GET_TC_COMMENT_AID, false ),
      array( GET_TC_USER_00, array("xid"=>"xid5", "aid"=>"asdkwo"), GET_TC_COMMENT_AID, false ),
      array( GET_TC_USER_00, array("first"=>"23"), GET_TC_COMMENT_AID , true ),
      array( GET_TC_USER_00, array("xid"=>"xid5", "first"=>"0"), GET_TC_COMMENT_AID , false ),
      array( GET_TC_USER_00, array("xid"=>"xid6", "first"=>"100", "count"=>"10"), GET_TC_COMMENT_AID , false ),
      array( GET_TC_USER_00, array("xid"=>"xid7", "first"=>"100", "count"=>"10"), GET_TC_COMMENT_AID , false ),
      );
   }


   /**
    * @dataProvider providerConstructor
    */
   public function testConstructor( $uid, $params, $appId,  $expected )
   {
      try {
	      $comment = $this->initRest( new CommentsGet(), $params, $uid , $appId );
         $this->assertFalse ( $expected, "Should have thrown exception " . var_export( $params ,true ) );
      } catch ( OpenFBAPIException $e ) {
         $this->assertTrue( $expected, "Should NOT have thrown exception " . var_export( $params ,true ) );
         $this->assertEquals( FB_ERROR_CODE_PARAMETER_MISSING, $e->getCode() );
      }

   }


   public static function providerExecute() {

      $x = 0;
      return array(
      array( GET_TC_USER_00, GET_TC_TEXT . $x++ , array("xid"=>"exec1", "aid"=>GET_TC_AID), GET_TC_COMMENT_AID , 12, 12, 0  ),
      array( GET_TC_USER_00, GET_TC_TEXT . $x++ , array("xid"=>"exec2", "first"=>"0", "aid"=>GET_TC_AID), GET_TC_COMMENT_AID , 22, 10, 0 ),
      array( GET_TC_USER_00, GET_TC_TEXT . $x++ , array("xid"=>"exec3", "first"=>"20", "count"=>"8", "aid"=>GET_TC_AID), GET_TC_COMMENT_AID , 30, 8, 20 ),
      array( GET_TC_USER_00, GET_TC_TEXT . $x++ , array("xid"=>"exec4", "first"=>"15", "count"=>"10", "aid"=>GET_TC_AID), GET_TC_COMMENT_AID , 20, 5, 15 ),
      );
   }


   /**
    * @dataProvider providerExecute
    */
   public function testExecute( $uid, $msg, $params, $appId,  $total, $count, $first )
   {
      $total = (integer) $total;
      $count = (integer) $count;
      $first = (integer) $first;

      try {
         $expectedCount = min ( $total - $first, $count, 10 );
         $j = $expectedCount;
         
         for ( $i = 0; $i<$total; $i++ ) {
            $addParams = array();
            $addParams['xid'] = $params['xid'];
            $addParams['aid'] = $params['aid'];
            if ( $i >= $total - $first - $expectedCount && $i < $total - $first ) {
               $addParams['text'] = "Find me " . $j--;
            } else { 
               $addParams['text'] = $msg;
            }
	        $api = $this->initRest( new CommentsAdd(), $addParams, $uid , $appId );
            $result = $api->execute();
            $this->assertTrue( $result['result'] == 1 , "Adding comment failed." );
         }

         $countParams = array();
         $countParams['xid'] = $params['xid'];
         $countParams['aid'] = $params['aid'];
	     $api = $this->initRest( new CommentsCount(), $countParams, $uid , $appId );
         $result = $api->execute();
         $this->assertTrue( $result['result'] == $total , "total inserted does not mach count ( {$result['result']}!= $total)");
          
	     $api = $this->initRest( new CommentsGet(), $params, $uid , $appId );
         $result = $api->execute();
         $this->assertTrue( is_array($result), "Results is not an array!" . $comments );
         $this->assertArrayHasKey( 'comments', $result, "Comments not returned for " . $params['xid'] );
         $comments = $result['comments'];
         $this->assertTrue( is_array($comments), "comments is not an array!" . $comments );

         foreach ( $comments as $i=>$value ) {
            $this->assertEquals( "Find me " . ($i +1) , $value['text'] );
            
            $delParams = array();
            $delParams['xid'] = $params['xid'];
            $delParams['cid'] = $value['cid'];
            $delParams['aid'] = $params['aid'];
            
            $api = $this->initRest( new CommentsDelete(), $delParams, $uid , $appId );
            $result = $api->execute();
            $this->assertTrue( $result['result'] == 1 , "Deleting comment failed." );
         }

         $api = $this->initRest( new CommentsCount(), $countParams, $uid , $appId );
         $result = $api->execute();
         $this->assertTrue( $result['result'] == ($total - $expectedCount) , "total deleted does not mach count (xid:{$params['xid']}) (total:$total) (removed:$expectedCount) {$result['result']}!= 0)");
         
      } catch ( OpenFBAPIException $e ) {
         $this->fail( "Exception not expected " . $e->getTraceAsString() );
      }

   }
   
}

?>
