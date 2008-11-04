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
require_once( "ringside/api/facebook/AdminGetAppProperties.php" );

define( 'ADMIN_NAME' , 'application_name' );
define( 'ADMIN_CANVAS_URL' , 'canvas_url' );
define( 'ADMIN_CALLBACK' , 'callback_url' );
define( 'ADMIN_POSTADD', 'post_install_url' );
define( 'ADMIN_EDIT' , 'edit_url' ); // This is currently not implemented.
define( 'ADMIN_DASHBOARD' ,'dashboard_url' ); // This is currently not implemented.
define( 'ADMIN_UNINSTALL' , 'uninstall_url' );
define( 'ADMIN_IPLIST' ,  'ip_list' );
define( 'ADMIN_EMAIL' , 'email' );
define( 'ADMIN_DESCRIPTION' , 'description' );
define( 'ADMIN_IFRAME' , 'use_iframe' );
define( 'ADMIN_DESKTOP' ,'desktop' );
define( 'ADMIN_ISMOBILE', 'is_mobile' );
define( 'ADMIN_DEFAULTFBML ' , 'default_fbml' );
define( 'ADMIN_DEFAULT_COLUMN' , 'default_column' ); // not supported
define( 'ADMIN_MESSAGE_URL' , 'message_url' ); // not supported
define( 'ADMIN_MESSAGE_ACTION' , 'message_action' ); // not supported
define( 'ADMIN_ABOUT_URL', 'about_url' );
define( 'ADMIN_PRIVATE_INSTALL' , 'private_install' ); // not supported
define( 'ADMIN_INSTALLABLE' , 'installable' );
define( 'ADMIN_PRIVACY_URL', 'privacy_url' );
define( 'ADMIN_HELP_URL', 'help_url' ); // not supported
define( 'ADMIN_SEE_ALL_URL', 'see_all_url' ); // not supported
define( 'ADMIN_TOS_URL', 'tos_url' );
define( 'ADMIN_DEV_MODE' , 'dev_mode' );
define( 'ADMIN_PRELOAD_FQL', 'preload_fql' ); //not supported
define( 'ADMIN_ICON_URL' , 'icon_url' );

define ( 'ADMIN_11000_ID', 11000 );
define ( 'ADMIN_11000_CALLBACK', 'http://url.com/callback' );
define ( 'ADMIN_11000_API_KEY', 'application-1200' );
define ( 'ADMIN_11000_SECRET_KEY', 'application-1200' );
define ( 'ADMIN_11000_NAME', 'Application 1200') ;
define ( 'ADMIN_11000_CANVAS_URL', 'app1200' );
define ( 'ADMIN_11000_SIDENAV_URL', 'app1200' );
define ( 'ADMIN_11000_EMAIL',  'jack@mail.com' );
define ( 'ADMIN_11000_POSTADD', 'http://url.com/postadd' );
define ( 'ADMIN_11000_UNINSTALL', 'http://url.com/postremove' );
define ( 'ADMIN_11000_IP_LIST', '12.34.56.78' );
define ( 'ADMIN_11000_DESCRIPTION', 'Greatest appplication ever!' );
define ( 'ADMIN_11000_USE_IFRAME', 1 );
define ( 'ADMIN_11000_DESKTOP', 0 );
define ( 'ADMIN_11000_ISMOBILE', 0 );
define ( 'ADMIN_11000_DEFAULT_FBML', '<fb:name />' );
define ( 'ADMIN_11000_ABOUT_URL', 'http://url.com/about' );
define ( 'ADMIN_11000_PRIVACY_URL', 'http://url.com/privacy' );
define ( 'ADMIN_11000_TOS_URL', '' );
define ( 'ADMIN_11000_DEVMODE', 1 );
define ( 'ADMIN_11000_INSTALLABLE', 1 );
define ( 'ADMIN_11000_ICON_URL', '' );
define ( 'ADMIN_11000_PRELOAD_FQL', '' );
define ( 'ADMIN_11000_SEE_ALL_URL', '' );
define ( 'ADMIN_11000_DEFAULT_COLUMN', '' );
define ( 'ADMIN_11000_MESSAGE_URL', '' );
define ( 'ADMIN_11000_MESSAGE_ACTION', '' );
define ( 'ADMIN_11000_PRIVATE_INSTALL', '' );
define ( 'ADMIN_11000_HELP_URL', '' );

define ( 'ADMIN_11001_ID', 11001 );
define ( 'ADMIN_11001_EMAIL', 'jeff@mail.com' );
define ( 'ADMIN_11001_NAME', 'Iam11001');

define ( 'ADMIN_USER_WITH_APPS', '11101' );
define ( 'ADMIN_USER_WITH_11000', '11102' );
define ( 'ADMIN_USER_WITH_NOAPPS', '11103' );

class AdminGetAppPropertiesTestCase extends BaseAPITestCase {
   
   public static function providerTestConstructor() {

      return array(
      array( ADMIN_USER_WITH_NOAPPS, array(), false ),
      array( ADMIN_USER_WITH_NOAPPS, array( "properties"=>ADMIN_ICON_URL), false ),
      array( ADMIN_USER_WITH_NOAPPS, array( "properties"=>json_encode(array(ADMIN_ICON_URL))), true ),
      array( ADMIN_USER_WITH_NOAPPS, array( "properties"=>json_encode( array( ADMIN_MESSAGE_ACTION,ADMIN_ABOUT_URL,ADMIN_PRIVATE_INSTALL) )) , true ),
      array( ADMIN_USER_WITH_NOAPPS, array( "properties"=>json_encode( array( ADMIN_ISMOBILE, ADMIN_IFRAME,ADMIN_EMAIL )), "aid"=>ADMIN_11000_ID), true ),
      );
   }
    
   /**
    * @dataProvider providerTestConstructor
    */
   public function testConstructor( $uid, $params, $pass )
   {
      try {
         
         $method = $this->initRest( new AdminGetAppProperties(), $params, $uid, ADMIN_11000_ID );
         $this->assertTrue( $pass, "This test should have failed with exception!" );
         $this->assertNotNull($method, "Object missing!");

      } catch ( OpenFBAPIException $exception ) {
         $this->assertFalse( $pass, "This test should have not thrown an exception! " . $exception );
         $this->assertEquals( FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode() );
      }
   }
    
   
   public static function providerTestExecute() {

   	$appId = 11000;
      $testResults = array();
      $testResults[ADMIN_ICON_URL]=ADMIN_11000_ICON_URL;
      $tests[]  = array( ADMIN_USER_WITH_APPS, array( "properties"=>json_encode(array(ADMIN_ICON_URL))), $testResults, $appId );

      $appId = 11000;
      $testResults = array();
      $testResults[ADMIN_ABOUT_URL]=ADMIN_11000_ABOUT_URL;
      $testResults[ADMIN_MESSAGE_ACTION]= ADMIN_11000_MESSAGE_ACTION;
      $testResults[ADMIN_PRIVATE_INSTALL]=ADMIN_11000_PRIVATE_INSTALL;
      $tests[]  = array( ADMIN_USER_WITH_APPS, array( "properties"=>json_encode( array( ADMIN_MESSAGE_ACTION,ADMIN_ABOUT_URL,ADMIN_PRIVATE_INSTALL))), $testResults, $appId  );
      
      $appId = 11000;
      $testResults = array();
      $testResults[ADMIN_EMAIL]= ADMIN_11000_EMAIL;
      $testResults[ADMIN_IFRAME]=ADMIN_11000_USE_IFRAME;
      $testResults[ADMIN_ISMOBILE]=ADMIN_11000_ISMOBILE;
      $tests[]  = array( ADMIN_USER_WITH_11000, array( "properties"=>json_encode( array( ADMIN_ISMOBILE, ADMIN_IFRAME,ADMIN_EMAIL )), "aid"=>ADMIN_11000_ID), $testResults, $appId  );
            
      $appId = 11001;
      $testResults = array();
      $testResults[ADMIN_EMAIL]= ADMIN_11001_EMAIL;
      $testResults[ADMIN_NAME]=ADMIN_11001_NAME;
      $tests[]  = array( ADMIN_USER_WITH_APPS, array( "properties"=>json_encode( array( ADMIN_EMAIL, ADMIN_NAME )), "aid"=>ADMIN_11001_ID), $testResults, $appId  );
      
      $appId = 11000;
      $testResults = array();
      $testResults[ADMIN_NAME]= ADMIN_11000_NAME;      
      $tests[]  = array( ADMIN_USER_WITH_11000, array( "properties"=>json_encode(array(ADMIN_NAME)), "app_api_key"=>ADMIN_11000_API_KEY), $testResults, $appId  );
      
      $appId = 11000;
      $testResults = array();
      $testResults[ADMIN_NAME]= ADMIN_11000_NAME;      
      $tests[]  = array( ADMIN_USER_WITH_11000, array( "properties"=>json_encode(array(ADMIN_NAME)), "canvas_url"=>ADMIN_11000_CANVAS_URL), $testResults, $appId  );
      
      return $tests;
   }
    
   /**
    * @dataProvider providerTestExecute
    */
    public function testExecute( $uid, $params, $expected, $appId ) 
    {
       
       // pass in uid
       try {
          $method = $this->initRest( new AdminGetAppProperties(), $params, $uid, $appId );
          $response = $method->execute();          
          $actual = json_decode( $response['result'], true );          
          $this->assertTrue( !empty($actual ), "There are no items in response (".$response['result'].")" );
          
          foreach ( $actual as $key=>$value ) {
             $this->assertArrayHasKey( $key, $expected, "actual has key not found in expected" );
             $this->assertEquals( $expected[$key], $value, "Returned value differs.");
          }
          foreach ( $expected as $key=>$value ) {
             $this->assertArrayHasKey( $key, $actual, "actual is missing key found in expected" );
          }
          
        } catch ( OpenFBAPIException $exception ) {
            $this->fail( "Should not have thrown exception " . $exception->getMessage() );
        }
                
    }
   
   public static function providerTestPermissions() {
      
      // The calling application is 11000
      // if a user attempts to get information on an application they do not own...

      $testResults = array();
      $testResults[ADMIN_ICON_URL]=ADMIN_11000_ICON_URL;
      $tests[]  = array( ADMIN_USER_WITH_11000, array( "properties"=>json_encode(array(ADMIN_ICON_URL)), "aid"=>ADMIN_11001_ID), FB_ERROR_CODE_GRAPH_EXCEPTION );

      return $tests;
   }
	 
  /**
   * @dataProvider providerTestPermissions
   */
   public function testPermissionError( $uid, $params, $eCode )
   {   
       // pass in uid
       try {
          $method = $this->initRest( new AdminGetAppProperties(), $params, $uid, ADMIN_11000_ID );
          $response = $method->execute();
          $this->fail( "Exception should have been thrown");
          
        } catch ( OpenFBAPIException $exception ) {
            $this->assertEquals( $eCode, $exception->getCode() );
        }
       
   }
}
?>
