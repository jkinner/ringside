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
require_once( "ringside/api/facebook/ProfileSetFBML.php" );
require_once( "ringside/api/facebook/ProfileGetFBML.php" );

      define( 'SETFBML_APPID', '7101' );
      define( 'SETFBML_USER_WITH_APP_FBML', '7011' );
      define( 'SETFBML_USER_WITH_APP_EMPTY_FBML', '7012' );
      define( 'SETFBML_USER_WITH_APP_NO_FBML', '7013' );
      define( 'SETFBML_USER_NOAPP', '7014' );
      define( 'SETFBML_NOSUCHUSER', '7099' );
      define( 'SETFBML_HELLOFBML', 'helloworld');
      define( 'SETFBML_EXPECTEDFBML', 'SetMeSomeFBMLPlease');
      define( 'SETFBML_RETURNEDFBML', '<![CDATA[SetMeSomeFBMLPlease]]>');
      define( 'SETFBML_EMPTYFBML', '' );

class ProfileSetFBMLTestCase extends BaseAPITestCase {

    private $session_without_fbml = array( 'app_id'=>SETFBML_APPID );
    private $session_with_fbml = array( 'app_id'=>SETFBML_APPID, 'fbml'=>'SpeedyCachedFBML' );
 
    public function testConstructor()
    {     

       // uid in session, markup and 
        try {
           $params = array ( 'markup'=>SETFBML_EXPECTEDFBML );
           $method = $this->initRest( new ProfileSetFBML(), $params, SETFBML_USER_WITH_APP_FBML, null, null, $this->session_without_fbml );
           $this->assertTrue( $method != null );
        } catch ( OpenFBAPIException $exception ) {
            $this->assertEquals( FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode() );
        }

        // uid in array 
        try {
           $params = array ( 'markup'=>SETFBML_EXPECTEDFBML, 'uid'=>SETFBML_USER_WITH_APP_FBML );
           $method = $this->initRest( new ProfileSetFBML(), $params, null, null, null, $this->session_without_fbml );
            $this->assertTrue( $method != null );
        } catch ( OpenFBAPIException $exception ) {
            $this->fail( "Should not have thrown exception " . $exception->getCode() );
        }
        
        // uid in array 
        try {
           $params = array ( 'markup'=>SETFBML_EXPECTEDFBML, 'uid'=>SETFBML_USER_WITH_APP_FBML );
           $method = $this->initRest( new ProfileSetFBML(), $params, null, null, null, $this->session_with_fbml );
           $this->assertTrue( $method != null );
        } catch ( OpenFBAPIException $exception ) {
            $this->fail( "Should not have thrown exception " . $exception->getCode() );
        }

        // uid some junk 
        try {
           $params = array ( 'markup'=>SETFBML_EXPECTEDFBML );
           $method = $this->initRest( new ProfileSetFBML(), $params, SETFBML_NOSUCHUSER, null, null, $this->session_with_fbml );
            $this->assertTrue( $method != null );
        } catch ( OpenFBAPIException $exception ) {
            $this->fail( "Should not have thrown exception " . $exception->getCode() );
        }
    }

    public function testProfileSetFBMLUserHasAppAndFbml() 
    {
       
       // session indicates users has FBML
        try {
            $params = array ( 'markup'=>SETFBML_EXPECTEDFBML, 'uid'=>SETFBML_USER_WITH_APP_FBML );
            $method = $this->initRest( new ProfileSetFBML(), $params, null, null, null, $this->session_with_fbml );
            $result = $method->execute();
            $this->assertArrayHasKey( 'result', $result, $result );
            $this->assertTrue( '1' == $result['result']);

            $params = array ();
            $method = $this->initRest( new ProfileGetFBML(), $params, SETFBML_USER_WITH_APP_FBML, null, null, $this->session_with_fbml );
            $result = $method->execute();
            $this->assertArrayHasKey( 'result', $result, $result );
            $this->assertSame( SETFBML_RETURNEDFBML, $result['result']);
                    
        } catch ( OpenFBAPIException $exception ) {
            $this->fail( "Should not have thrown exception " . $exception );
        }

       // session doe not indicate users has FBML
        try {
            $params = array ( 'markup'=>SETFBML_EXPECTEDFBML . 't2', 'uid'=>SETFBML_USER_WITH_APP_FBML );
            $method = $this->initRest( new ProfileSetFBML(), $params, null, null, null, $this->session_without_fbml );
            $result = $method->execute();
            $this->assertArrayHasKey( 'result', $result, $result );
            $this->assertTrue( '1' == $result['result']);

            $params = array ();
            $method = $this->initRest( new ProfileGetFBML(), $params, SETFBML_USER_WITH_APP_FBML, null, null, $this->session_without_fbml );
            $result = $method->execute();
            $this->assertArrayHasKey( 'result', $result, $result );
            $this->assertSame( '<![CDATA['.SETFBML_EXPECTEDFBML . 't2'.']]>', $result['result']);
                    
        } catch ( OpenFBAPIException $exception ) {
            $this->fail( "Should not have thrown exception " . $exception );
        }
        
        // clear the FBML
        try {
            $params = array ( 'markup'=>SETFBML_EMPTYFBML , 'uid'=>SETFBML_USER_WITH_APP_FBML );
            $method = $this->initRest( new ProfileSetFBML(), $params, null, null, null, $this->session_without_fbml );
            $result = $method->execute();
            $this->assertArrayHasKey( 'result', $result, $result );
            $this->assertTrue( '1' == $result['result']);

            $params = array ();
            $method = $this->initRest( new ProfileGetFBML(), $params, SETFBML_USER_WITH_APP_FBML, null, null, $this->session_without_fbml );
            $result = $method->execute();
            $this->assertArrayHasKey( 'result', $result, $result );
            $this->assertSame( SETFBML_EMPTYFBML , $result['result']);
                    
        } catch ( OpenFBAPIException $exception ) {
            $this->fail( "Should not have thrown exception " . $exception );
        }
    
    }

    public function testProfileSetFBMLUserHasAppAndEmptyFBML() 
    {
        try {
            $params = array ( 'markup'=>SETFBML_EXPECTEDFBML );
            $method = $this->initRest( new ProfileSetFBML(), $params, SETFBML_USER_WITH_APP_EMPTY_FBML, null, null, $this->session_without_fbml );
            $result = $method->execute();
            $this->assertArrayHasKey( 'result', $result, $result );
            $this->assertTrue( '1' == $result['result']);

            $params = array ();
            $method = $this->initRest( new ProfileGetFBML(), $params, SETFBML_USER_WITH_APP_EMPTY_FBML, null, null, $this->session_without_fbml );
            $result = $method->execute();
            $this->assertArrayHasKey( 'result', $result, $result );
            $this->assertSame( SETFBML_RETURNEDFBML, $result['result']);
                    
        } catch ( OpenFBAPIException $exception ) {
            $this->fail( "Should not have thrown exception " . $exception );
        }

    }
    
    public function testProfileSetFBMLUserHasAppAndnullFBML() 
    {
        try {
            $params = array ( 'markup'=>SETFBML_EXPECTEDFBML );
            $method = $this->initRest( new ProfileSetFBML(), $params, SETFBML_USER_WITH_APP_NO_FBML, null, null, $this->session_without_fbml );
            $result = $method->execute();
            $this->assertArrayHasKey( 'result', $result, $result );
            $this->assertTrue( '1' == $result['result']);

            $params = array ( );
            $method = $this->initRest( new ProfileGetFBML(), $params, SETFBML_USER_WITH_APP_NO_FBML, null, null, $this->session_without_fbml );
            $result = $method->execute();
            $this->assertArrayHasKey( 'result', $result, $result );
            $this->assertSame( SETFBML_RETURNEDFBML, $result['result']);
                    
        } catch ( OpenFBAPIException $exception ) {
            $this->fail( "Should not have thrown exception " . $exception );
        }
    }

    public function testProfileSetFBMLUserDoesNotHaveApp() 
    {
        try {
            $params = array ( 'markup'=>SETFBML_EXPECTEDFBML );
            $method = $this->initRest( new ProfileSetFBML(), $params, SETFBML_USER_NOAPP, null, null, $this->session_without_fbml );
            $result = $method->execute();
            $this->assertArrayHasKey( 'result', $result, $result );
            $this->assertTrue( '1' == $result['result'], $result['result'] );

            $params = array ();
            $method = $this->initRest( new ProfileGetFBML(), $params, SETFBML_USER_NOAPP, null, null, $this->session_without_fbml );
            $result = $method->execute();
            $this->assertArrayHasKey( 'result', $result, $result );
            $this->assertSame( SETFBML_RETURNEDFBML, $result['result']);
                    
        } catch ( OpenFBAPIException $exception ) {
            $this->fail( "Should not have thrown exception " . $exception );
        }
       
        try {
            $params = array ( 'markup'=>SETFBML_EXPECTEDFBML );
            $method = $this->initRest( new ProfileSetFBML(), $params, SETFBML_USER_NOAPP, null, null, $this->session_without_fbml );
            $result = $method->execute();
            $this->assertArrayHasKey( 'result', $result, $result );
            $this->assertTrue( '1' == $result['result']);

            $params = array ( );
            $method = $this->initRest( new ProfileGetFBML(), $params, SETFBML_USER_NOAPP, null, null, $this->session_without_fbml );
            $result = $method->execute();
            $this->assertArrayHasKey( 'result', $result, $result );
            $this->assertSame( SETFBML_RETURNEDFBML, $result['result']);
                    
        } catch ( OpenFBAPIException $exception ) {
            $this->fail( "Should not have thrown exception " . $exception );
        }
        
        
    }    
}
?>
