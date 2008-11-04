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
require_once( "ringside/api/facebook/FbmlSetRefHandle.php" );

define( 'FBML_APPID', '15101' );
define( 'FBML_NOSUCHUSER', '15099' );

class FbmlSetRefHandleTestCase extends BaseAPITestCase {

    private $appId = FBML_APPID;
 
    public function testConstructor()
    {     
        try {
            $params = array();
            $method = $this->initRest( new FbmlSetRefHandle(), $params, FBML_NOSUCHUSER, $this->appId );
            $this->fail("Object creation should have thrown an exception.");
        } catch ( OpenFBAPIException $exception ) {
            $this->assertEquals( FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode() );
        }

        // uid in array 
        try {
            $params = array ( 'handle'=>"test");
            $method = $this->initRest( new FbmlSetRefHandle(), $params, FBML_NOSUCHUSER, $this->appId );
            $this->fail("Object creation should have thrown an exception.");
        } catch ( OpenFBAPIException $exception ) {
            $this->assertEquals( FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode() );
        }
        
        // uid in array 
        try {
            $params = array ( 'fbml'=>"test");
            $method = $this->initRest( new FbmlSetRefHandle(), $params, FBML_NOSUCHUSER, $this->appId );
            $this->fail("Object creation should have thrown an exception.");
        } catch ( OpenFBAPIException $exception ) {
            $this->assertEquals( FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode() );
        }

        // uid some junk 
        try {
            $params = array ( 'handle'=>"test",'fbml'=>"test");
            $method = $this->initRest( new FbmlSetRefHandle(), $params, FBML_NOSUCHUSER, $this->appId );
            $this->assertTrue( $method != null );
        } catch ( OpenFBAPIException $exception ) {
            $this->fail( "Should not have thrown exception " . $exception->getCode() );
        }
    }

 
    public static function providerTestSetRefHandle()
    {
        return array(
           array(array( 'handle'=>'create_1', 'fbml'=>'1234567890' )), 
           array(array('handle'=>'create_2', 'fbml'=>'And the cat jumped in the whole with the rabbit and the fox. OH NO.' )), 
           array(array('handle'=>'create_3', 'fbml'=>'What about I18N tests...'))        
        );
    }    
    
	/**
     * @dataProvider providerTestSetRefHandle
     */
    public function testSetRefHandle( $params ) 
    {
       
       // array ( 'handle'=>"test",'fbml'=>"test")
       // session indicates users has FBML
        try {
            $method = $this->initRest( new FbmlSetRefHandle(), $params, FBML_NOSUCHUSER, $this->appId );
            $result = $method->execute();
            $this->assertArrayHasKey( 'result', $result, $result );
            $this->assertTrue( '1' == $result['result']);
            $this->assertSame( $params['fbml'], $method->provider->getContent(FBML_APPID, $params['handle'] ) );
            
            $method->provider->clear( FBML_APPID, $params['handle'] );
            $this->assertFalse( $method->provider->getContent(FBML_APPID, $params['handle'] ) );
        } catch ( OpenFBAPIException $exception ) {
            $this->fail( "Should not have thrown exception " . $exception );
        }

    }
}
?>
