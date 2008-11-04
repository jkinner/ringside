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
require_once( "ringside/api/facebook/FeedPublishStoryToUser.php" );

define( 'FEED_STORY_USER' , '57' );
define('FEED_STORY_USER_BAD', 0 );

class FeedPublishStoryToUserTestCase extends BaseAPITestCase {

   private $session = array( "test"=>1 );
   public static function providerTestConstructor() {

      return array(
      array( FEED_STORY_USER, array(), false ),
      array( FEED_STORY_USER, array("title"=>"testTitle"), true ),
      array( FEED_STORY_USER, array("title"=>"testTitleSubject", "body"=>"test" ), true ),
      array( FEED_STORY_USER, array("title"=>"testWithImage", "body"=>"test", "image_1"=>"my image", "image_1_link"=>"my link" ), true ),
      array( FEED_STORY_USER, array("title"=>"testWithImages", "body"=>"test", "image_1"=>"my image", "image_1_link"=>"my link", "image_2"=>"pretty2.jpg", "image_3"=>"3image.gif","image_3_link"=>"have a link?" ), true )      
      );
   }

   /**
    * @dataProvider providerTestConstructor
    */
   public function testConstructor( $uid, $params, $pass )
   {
      try {
         $notif =  $this->initRest( new FeedPublishStoryToUser(), $params, $uid );
         $notif->setSessionValue( 'test', 1 );
         $notif->validateRequest();
          
         $this->assertTrue( $pass, "This test should have failed with exception!" );
         $this->assertNotNull($notif, "Object missing!");

      } catch ( Exception $exception ) {
         $this->assertFalse( $pass, "This test should have not thrown an exception! " . $exception );
         $this->assertEquals( FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode() );
      }
   }

   public static function providerTestExecute() {

      return array(
      array( FEED_STORY_USER, array("title"=>"testTitle"), 1 ),
      array( FEED_STORY_USER, array("title"=>"testTitleSubject", "body"=>"test" ), 1 ),
      array( FEED_STORY_USER, array("title"=>"testWithImage", "body"=>"test", "image_1"=>"my image", "image_1_link"=>"my link" ), 1 ),
      array( FEED_STORY_USER, array("title"=>"testWithImages", "body"=>"test", "image_1"=>"my image", "image_1_link"=>"my link", "image_2"=>"pretty2.jpg", "image_3"=>"3image.gif","image_3_link"=>"have a link?" ), 1 )      
      );
   }

   /**
    * @dataProvider providerTestExecute
    */
   public function testExecute( $uid , $params, $pass ) {

      try {
         // get sending user pre-information
         $method =  $this->initRest( new FeedPublishStoryToUser(), $params, $uid );
         $method->setSessionValue( 'test', 1 );
         $method->validateRequest();

         $response = $method->execute();
         $this->assertEquals( $pass, $response['result'] );

      } catch ( Exception $exception ) {
         $this->fail( "Exception should have not be thrown " . $exception );
      }
   }
}

?>
