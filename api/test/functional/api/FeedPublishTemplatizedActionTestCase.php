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
require_once( "ringside/api/facebook/FeedPublishTemplatizedAction.php" );

define( 'FEED_TMPLT_USER' , '16001' );
define( 'FEED_TMPLT_USER_BAD', 0 );
define( 'FEED_TMPLT_ACTOR_NOT_FRIEND' , '16002' );
define( 'FEED_TMPLT_ACTOR_IS_FRIEND_NO_APP' , '16003' );
define( 'FEED_TMPLT_ACTOR_IS_FRIEND_WITH_APP' , '16004' );
define( 'FEED_TMPLT_TARGET_NOT_FRIEND1', '16005');
define( 'FEED_TMPLT_TARGET_NOT_FRIEND2', '16006');
define( 'FEED_TMPLT_TARGET_FRIEND1', '16007');
define( 'FEED_TMPLT_TARGET_FRIEND2', '16008');

define( 'FEED_TMPLT_APPID', 16100 );

/**
 * Test class.
 *
 * @author Richard Friedman
 */
class FeedPublishTemplatizedActionTestCase extends BaseAPITestCase {

   private $appId = FEED_TMPLT_APPID;
   private $session = array( 'app_id'=>FEED_TMPLT_APPID, 'test'=>2 );
   
   public static function providerTestConstructor() {

      return array(
      array( FEED_TMPLT_USER, array(), false ),
      array( FEED_TMPLT_USER, array("title_template"=>"testTitle"), true ),
      array( FEED_TMPLT_USER, array("title_template"=>"testTitleSubject", "title_data"=>"yaboo doobo" ), true ),
      array( FEED_TMPLT_USER, array("title_template"=>"testTitleSubject", "body_template"=>"test" ), true ),
      array( FEED_TMPLT_USER, array("title_template"=>"testTitleSubject", "body_template"=>"test", "body_data"=>"hunko monko", "body_general"=>"I am general" ), true ),
      array( FEED_TMPLT_USER, array("title_template"=>"testWithImage", "body_template"=>"test", "image_1"=>"my image", "image_1_link"=>"my link" ), true ),
      array( FEED_TMPLT_USER, array("title_template"=>"testWithImages", "body_template"=>"test", "image_1"=>"my image", "image_1_link"=>"my link", "image_2"=>"pretty2.jpg", "image_3"=>"3image.gif","image_3_link"=>"have a link?" ), true ),
      array( FEED_TMPLT_USER, array("title_template"=>"testTitle", "page_actor_id"=>33), true ),
      array( FEED_TMPLT_USER, array("title_template"=>"testTitle", "target_ids"=>"3,4,5"), true )
      );
   }

   /**
    * @dataProvider providerTestConstructor
    */
   public function testConstructor( $uid, $params, $pass )
   {
      try {
         $notif =  $this->initRest( new FeedPublishTemplatizedAction(), $params, $uid, $this->appId );
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
      $tests = array();
      
      $titleBad = array();
      $titleBad['title_template'] = 'hi i am a bad title';
      $tests[] = array( FEED_TMPLT_USER, $titleBad,  FB_ERROR_CODE_FEED_TITLE_PARAMS );
      
      $titleGood = array();
      $titleGood['title_template'] = 'I am just an {actor} who lives here';
      $tests[] = array( FEED_TMPLT_USER, $titleGood,  0 );
      
      $actorNotFriend = array();
      $actorNotFriend['title_template'] = 'You and {actor} are not friends';
      $actorNotFriend['page_actor_id'] = FEED_TMPLT_ACTOR_NOT_FRIEND;
      $tests[] = array( FEED_TMPLT_USER, $actorNotFriend,  FB_ERROR_CODE_REQUIRES_PERMISSION, FB_ERROR_MSG_ACTOR_USER_NOT_FRIENDS );
      
      $actorIsFriendNotOnApp = array();
      $actorIsFriendNotOnApp['title_template'] = 'You and {actor} are friends without apps';
      $actorIsFriendNotOnApp['page_actor_id'] = FEED_TMPLT_ACTOR_IS_FRIEND_NO_APP;
      $tests[] = array( FEED_TMPLT_USER, $actorIsFriendNotOnApp,  FB_ERROR_CODE_REQUIRES_PERMISSION, FB_ERROR_MSG_ACTOR_USER_DONT_SHAREAPPS );
      
      $actorIsFriendWithApp = array();
      $actorIsFriendWithApp['title_template'] = 'You and {actor} are good friends';
      $actorIsFriendWithApp['page_actor_id'] = FEED_TMPLT_ACTOR_IS_FRIEND_WITH_APP;
      $tests[] = array( FEED_TMPLT_USER, $actorIsFriendWithApp,  0 );

      $targetsNotFriends = array();
      $targetsNotFriends['title_template'] = 'Targets and {actor} not share friends';
      $targetsNotFriends['target_ids'] = implode( ",", array(FEED_TMPLT_TARGET_NOT_FRIEND1,FEED_TMPLT_TARGET_NOT_FRIEND2));
      $tests[] = array( FEED_TMPLT_USER, $targetsNotFriends,  FB_ERROR_CODE_REQUIRES_PERMISSION, FB_ERROR_MSG_TARGETS_NOT_FRIENDS );
      
      $targetsFriends = array();
      $targetsFriends['title_template'] = 'Targets and {actor} have lots of friends';
      $targetsFriends['target_ids'] = implode( ",", array(FEED_TMPLT_TARGET_FRIEND1,FEED_TMPLT_TARGET_FRIEND2));
      $tests[] = array( FEED_TMPLT_USER, $targetsFriends,  0 );
      
      $titleWithKeysNoData = array();
      $titleWithKeysNoData['title_template'] = 'I am just an {actor} who lives {here}';
      $tests[] = array( FEED_TMPLT_USER, $titleWithKeysNoData,  FB_ERROR_CODE_FEED_TITLE_JSON, FB_ERROR_MSG_FEED_TITLE_JSON_EMPTY);
      
      $titleWithKeysAndDataGood = array();
      $titleWithKeysAndDataGood['title_template'] = 'I am just an {actor} who lives {here} and has {data}';
      $titleWithKeysAndDataGood['title_data'] = json_encode( array('here'=>'brooklyn','data'=>'10010101') );
      $tests[] = array( FEED_TMPLT_USER, $titleWithKeysAndDataGood,  0 );
      
      $titleWithKeysAndDataMissingOne = array();
      $titleWithKeysAndDataMissingOne['title_template'] = 'I am just an {actor} who lives {here} and has {data} is gone';
      $titleWithKeysAndDataMissingOne['title_data'] = json_encode( array('here'=>'brooklyn') );
      $tests[] = array( FEED_TMPLT_USER, $titleWithKeysAndDataMissingOne,  FB_ERROR_CODE_FEED_TITLE_PARAMS,  FB_ERROR_MSG_FEED_MISSING_PARAMS );
            
      $titleWithKeysAndDataHasActor = array();
      $titleWithKeysAndDataHasActor['title_template'] = 'I am just an {actor} who lives {here} and has {data} but includes actor';
      $titleWithKeysAndDataHasActor['title_data'] = json_encode( array('here'=>'brooklyn','data'=>'10010101', 'actor'=>'yikes') );
      $tests[] = array( FEED_TMPLT_USER, $titleWithKeysAndDataHasActor,  FB_ERROR_CODE_FEED_TITLE_JSON, FB_ERROR_MSG_FEED_TITLE_JSON_INVALID );
      
      $titleWithKeysAndDataHasTargets = array();
      $titleWithKeysAndDataHasTargets['title_template'] = 'I am just an {actor} who lives {here} and has {data} and includes target';
      $titleWithKeysAndDataHasTargets['title_data'] = json_encode( array('here'=>'brooklyn','data'=>'10010101', 'target'=>'yikes') );
      $tests[] = array( FEED_TMPLT_USER, $titleWithKeysAndDataHasTargets,  FB_ERROR_CODE_FEED_TITLE_JSON, FB_ERROR_MSG_FEED_TITLE_JSON_INVALID );
      
      $bodyTagWrong = array();
      $bodyTagWrong['title_template'] = '{actor} testing bad body tag';
      $bodyTagWrong['body'] = "What a body.";
      $tests[] = array( FEED_TMPLT_USER, $bodyTagWrong,  FB_ERROR_CODE_INCORRECT_SIGNATURE );
      
      $bodyNoArgs = array();
      $bodyNoArgs['title_template'] = '{actor} testing body tag simple ';
      $bodyNoArgs['body_template'] = "What a body.";
      $tests[] = array( FEED_TMPLT_USER, $bodyNoArgs,  0 );
      
      $bodyJustActor = array();
      $bodyJustActor['title_template'] = '{actor} testing body with {actor}';
      $bodyJustActor['body_template'] = "Hey {actor} check that body.";
      $tests[] = array( FEED_TMPLT_USER, $bodyJustActor,  0 );
            
      $bodyJustTargetsNoTargets = array();
      $bodyJustTargetsNoTargets['title_template'] = '{actor} testing body with no targets';
      $bodyJustTargetsNoTargets['body_template'] = "Hey {target} check that body.";
      $tests[] = array( FEED_TMPLT_USER, $bodyJustTargetsNoTargets,  FB_ERROR_CODE_FEED_BODY_PARAMS,  FB_ERROR_MSG_FEED_BODY_MISSING_TARGETS );
      
      $bodyJustTargets = array();
      $bodyJustTargets['title_template'] = '{actor} testing body with targets ';
      $bodyJustTargets['body_template'] = "Hey {target} check that body.";
      $bodyJustTargets['target_ids'] = implode( ",", array(FEED_TMPLT_TARGET_FRIEND1,FEED_TMPLT_TARGET_FRIEND2));
      $tests[] = array( FEED_TMPLT_USER, $bodyJustTargets, 0 );
      
      $bodyWithStubNoData = array();
      $bodyWithStubNoData['title_template'] = '{actor} testing body with tags no data ';
      $bodyWithStubNoData['body_template'] = "Hey {actor} check {that} body {out}.";
      $tests[] = array( FEED_TMPLT_USER, $bodyWithStubNoData,  FB_ERROR_CODE_FEED_BODY_JSON, FB_ERROR_MSG_FEED_BODY_JSON_EMPTY );

      $bodyWithDataAndActor = array();
      $bodyWithDataAndActor['title_template'] = '{actor} testing body with tags and data ';
      $bodyWithDataAndActor['body_template'] = "Hey {actor} check {that} body {out}.";
      $bodyWithDataAndActor['body_data'] = json_encode( array('that'=>'brooklyn','out'=>'10010101', 'actor'=>'yikes') );
      $tests[] = array( FEED_TMPLT_USER, $bodyWithDataAndActor,  FB_ERROR_CODE_FEED_BODY_JSON, FB_ERROR_MSG_FEED_BODY_JSON_INVALID );
      
      $bodyWithDataAndTarget = array();
      $bodyWithDataAndTarget['title_template'] = '{actor} testing body with tags and data ';
      $bodyWithDataAndTarget['body_template'] = "Hey {actor} check {that} body {out}.";
      $bodyWithDataAndTarget['body_data'] = json_encode( array('that'=>'brooklyn','out'=>'10010101', 'target'=>'yikes') );
      $tests[] = array( FEED_TMPLT_USER, $bodyWithDataAndTarget,  FB_ERROR_CODE_FEED_BODY_JSON, FB_ERROR_MSG_FEED_BODY_JSON_INVALID );
      
      $bodyWithDataMissOne = array();
      $bodyWithDataMissOne['title_template'] = '{actor} testing body with tags and data ';
      $bodyWithDataMissOne['body_template'] = "Hey {actor} check {that} body {out}.";
      $bodyWithDataMissOne['body_data'] = json_encode( array('out'=>'10010101', 'unknown'=>'yikes') );
      $tests[] = array( FEED_TMPLT_USER, $bodyWithDataMissOne,  FB_ERROR_CODE_FEED_BODY_PARAMS, FB_ERROR_MSG_FEED_BODY_MISSING_PARAMS );
      
      $bodyWithStubAndData = array();
      $bodyWithStubAndData['title_template'] = '{actor} testing body with tags and data ';
      $bodyWithStubAndData['body_template'] = "Hey {actor} check {that} body {out}.";
      $bodyWithStubAndData['body_data'] = json_encode( array('that'=>'brooklyn','out'=>'10010101', 'unknown'=>'yikes') );
      $tests[] = array( FEED_TMPLT_USER, $bodyWithStubAndData,  0 );
      
      $bodyWithAll = array();
      $bodyWithAll['title_template'] = '{actor} testing body with tags and data ';
      $bodyWithAll['title_data'] = json_encode( array('here'=>'brooklyn','data'=>'10010101') );
      $bodyWithAll['body_template'] = "Hey {actor} check {that} body {out} my {target}.";
      $bodyWithAll['body_data'] = json_encode( array('that'=>'brooklyn','out'=>'10010101') );
      $bodyWithAll['body_general'] = "Generally speaking";
      $bodyWithAll['target_ids'] = implode( ",", array(FEED_TMPLT_TARGET_FRIEND1,FEED_TMPLT_TARGET_FRIEND2));
      $bodyWithAll['page_actor_id'] = FEED_TMPLT_ACTOR_IS_FRIEND_WITH_APP;
      $tests[] = array( FEED_TMPLT_USER, $bodyWithAll,  0 );
      
         return $tests;

   }

   /**
    * @dataProvider providerTestExecute
    */
   public function testExecute( $uid , $params, $pass, $except = null ) {

      try {
         // get sending user pre-information
         $method =  $this->initRest( new FeedPublishTemplatizedAction(), $params, $uid, $this->appId );
         $method->setSessionValue( 'test', 1 );
         $method->validateRequest();
         
         $response = $method->execute();
         $this->assertTrue( !$pass , "This test should have failed with code ($pass)");
         $this->assertEquals( $response['result'], '1', "The result should have been positive!" );
      } catch ( Exception $exception ) {
         $this->assertEquals( $pass, $exception->getCode(), "Exception should have not be thrown " . $exception );
         if ( $except != null ) { 
            $this->assertSame( $except, $exception->getMessage() );
         }
      }
   }
//
//   public function testExecuteBad( ) {
//         try {
//
//         // get sending user pre-information
//         $method = new FeedPublishActionToUser( FEED_TMPLT_USER_BAD, array("title"=>"testTitleSubject", "subject"=>"test" ) );
//         $response = $method->execute();
//         $this->fail( "Exception should have been thrown " );
//
//      } catch ( Exception $exception ) {
//         $this->assertTrue( $exception instanceof Exception );
//      }      
//   }
   
}

?>
