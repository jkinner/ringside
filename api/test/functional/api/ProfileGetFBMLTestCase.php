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
require_once( "ringside/api/facebook/ProfileGetFBML.php" );

      define( 'GETFBML_APPID', '7100' );
      define( 'GETFBML_APPID_NOSUCH', '7105' );
      define( 'GETFBML_APPID_DEFAULT', '7102' );
      define( 'GETFBML_USER_WITH_APP_FBML', '7001' );
      define( 'GETFBML_USER_WITH_APP_EMPTY_FBML', '7002' );
      define( 'GETFBML_USER_WITH_APP_NO_FBML', '7003' );
      define( 'GETFBML_USER_NOAPP', '7004' );
      define( 'GETFBML_NOSUCHUSER', '7099' );
      define( 'GETFBML_EXPECTEDFBML', '<![CDATA[helloworld]]>');
      define( 'GETFBML_EMPTYFBML', '' );

class ProfileGetFBMLTestCase extends BaseAPITestCase {

   private $appId = GETFBML_APPID;
    
    public function testConstructor()
    {     

       // no parameters
        try {
           $params = array();
           $method = $this->initRest( new ProfileGetFBML(), $params, GETFBML_NOSUCHUSER, $this->appId );
            $this->assertTrue( $method != null );
        } catch ( OpenFBAPIException $exception ) {
            $this->assertEquals( FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode() );
        }

        // has parameters 
        try {
            $method = new ProfileGetFBML( GETFBML_NOSUCHUSER, array('uid'=>GETFBML_USER_NOAPP), $this->session );
            $this->assertTrue( $method != null );
        } catch ( OpenFBAPIException $exception ) {
            $this->fail( "Should not have thrown exception " . $exception->getCode() );
        }
    }

    public function testProfileGetFBMLUserHasAppAndFbml() 
    {
       
       // pass in uid
        try {
           $params = array("uid"=>GETFBML_USER_WITH_APP_FBML);
           $method = $this->initRest( new ProfileGetFBML(), $params, GETFBML_NOSUCHUSER, $this->appId );
           $result = $method->execute();
           $this->assertArrayHasKey( 'result', $result, $result );
           $this->assertSame( GETFBML_EXPECTEDFBML, $result['result']);
        } catch ( OpenFBAPIException $exception ) {
            $this->fail( "Should not have thrown exception " . $exception->getTraceAsString() );
        }

        // constructor null uid
        try {
           $params = array("uid"=>GETFBML_USER_WITH_APP_FBML);
           $method = $this->initRest( new ProfileGetFBML(), $params, null, $this->appId );
           $result = $method->execute();
           $this->assertArrayHasKey( 'result', $result, $result );
           $this->assertSame( GETFBML_EXPECTEDFBML, $result['result']);
        } catch ( OpenFBAPIException $exception ) {
            $this->fail( "Should not have thrown exception " . $exception->getCode() );
        }
                
        // construct with uid
        try {
           $params = array();
           $method = $this->initRest( new ProfileGetFBML(), $params, GETFBML_USER_WITH_APP_FBML, $this->appId );
           $result = $method->execute();
           $this->assertArrayHasKey( 'result', $result, $result );
           $this->assertSame( GETFBML_EXPECTEDFBML, $result['result']);
        } catch ( OpenFBAPIException $exception ) {
           $this->fail( "Should not have thrown exception " . $exception->getTraceAsString() );
        }

    }

    public function testProfileGetFBMLNoSuchUser() 
    {
        // construct with uid
        try {
            $params = array();
            $method = $this->initRest( new ProfileGetFBML(), $params, GETFBML_NOSUCHUSER, $this->appId );
            $result = $method->execute();
            $this->assertArrayHasKey( 'result', $result, $result );
            $this->assertSame( GETFBML_EMPTYFBML, $result['result']);
        } catch ( OpenFBAPIException $exception ) {
            $this->fail( "Should not have thrown exception " . $exception->getCode() );
        }
    }
    
    public function testProfileGetFBMLUserDoesNotHaveApp() 
    {
        try {
            $params = array();
            $method = $this->initRest( new ProfileGetFBML(), $params, GETFBML_USER_NOAPP, $this->appId );
            $result = $method->execute();
            $this->assertArrayHasKey( 'result', $result, $result );
            $this->assertSame( GETFBML_EMPTYFBML, $result['result']);
        } catch ( OpenFBAPIException $exception ) {
            $this->fail( "Should not have thrown exception " . $exception->getCode() );
        }
    }

    public function testProfileGetFBMLUserasAppEmptyFBML() 
    {
       try {
          $params = array();
          $method = $this->initRest( new ProfileGetFBML(), $params, GETFBML_USER_WITH_APP_EMPTY_FBML, $this->appId );
          $result = $method->execute();
          $this->assertArrayHasKey( 'result', $result, $result );
          $this->assertSame( GETFBML_EMPTYFBML, $result['result']);
       } catch ( OpenFBAPIException $exception ) {
          $this->fail( "Should not have thrown exception " . $exception->getCode() );
       }
    }

    public function testProfileGetFBMLUserasAppNoFBML() 
    {
       try {
          $params = array();
          $method = $this->initRest( new ProfileGetFBML(), $params, GETFBML_USER_WITH_APP_NO_FBML, $this->appId );
          $result = $method->execute();
          $this->assertArrayHasKey( 'result', $result, $result );
          $this->assertSame( GETFBML_EMPTYFBML, $result['result']);
       } catch ( OpenFBAPIException $exception ) {
          $this->fail( "Should not have thrown exception " . $exception->getCode() );
       }
    }

   public static function providerTestConstructor() {

      return array(
      // Case: Params: empty, Context: uid,aid
      array( null, GETFBML_USER_WITH_APP_FBML, GETFBML_APPID, GETFBML_EXPECTEDFBML, false ),
      // Case: Params: aid1.  Context: uid, aid1
      array( GETFBML_APPID, GETFBML_USER_WITH_APP_FBML, GETFBML_APPID, GETFBML_EXPECTEDFBML, false ),
      // Case: Params: ''.  Context: uid, aid1
      array( '', GETFBML_USER_WITH_APP_FBML, GETFBML_APPID, GETFBML_EXPECTEDFBML, false ),
      // Case: Params: aid1.  Context: uid, aid2 (DNE)
      array( GETFBML_APPID, GETFBML_USER_WITH_APP_FBML, GETFBML_APPID_NOSUCH, GETFBML_EXPECTEDFBML, true ),
      // Case: Params: aid2.  Context: uid, aid1
      array( GETFBML_APPID_NOSUCH, GETFBML_USER_WITH_APP_FBML, GETFBML_APPID, GETFBML_EXPECTEDFBML, true ),
      // Case: Params: aid1.  Context: uid, aid3 (DEFAULT)
      array( GETFBML_APPID, GETFBML_USER_WITH_APP_FBML, GETFBML_APPID_DEFAULT, GETFBML_EXPECTEDFBML, false ),
      
      );
   }

   /**
    * @dataProvider providerTestConstructor
    */    
    public function testProfileGetFBMLAids( $aid , $defaultUid, $defaultAid, $expectedFbml, $exception ) { 
       try {
          $params = array();
          if ( $aid != null ) 
          {
             $params['aid'] = $aid;
          }
          
          $method = $this->initRest( new ProfileGetFBML(), $params, $defaultUid, $defaultAid );
          $result = $method->execute();
          $this->assertFalse( $exception );
          $this->assertArrayHasKey( 'result', $result, $result );
          $this->assertSame( $expectedFbml, $result['result'], "ExpectedFBML not correct for ($aid,$defaultUid,$defaultAid) Expected($expectedFbml) " . var_export( $result, true ) );
       } catch ( OpenFBAPIException $e ) {
          $this->assertTrue( $exception, "Unknown exception " . $e );
       }
    }
}
?>
