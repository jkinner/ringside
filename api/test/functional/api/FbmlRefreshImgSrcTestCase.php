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
require_once( "ringside/api/facebook/FbmlRefreshImgSrc.php" );

define( 'FBML_IMGREFRESH_APPID', '15101' );
define( 'FBML_IMGREFRESH_NOSUCHUSER', '15099' );

class FbmlRefreshImgSrcTestCase extends BaseAPITestCase {

    private $appId = FBML_IMGREFRESH_APPID;
   
    public function setUp() {

    }
    
    public function tearDown() {
     
    }
 
    public function testConstructor()
    {     
        try {
            $params = array();
            $method = $this->initRest( new FbmlRefreshImgSrc(), $params, FBML_IMGREFRESH_NOSUCHUSER, $this->appId );
            $this->fail("Object creation should have thrown an exception.");
        } catch ( OpenFBAPIException $exception ) {
            $this->assertEquals( FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode() );
        }

        // uid some junk 
        try {
            $params = array ( 'url'=>"http://localhost");
            $method = $this->initRest( new FbmlRefreshImgSrc(), $params, FBML_IMGREFRESH_NOSUCHUSER, $this->appId );
            $this->assertTrue( $method != null );
        } catch ( OpenFBAPIException $exception ) {
            $this->fail( "Should not have thrown exception " . $exception->getCode() );
        }
    }

 
    public static function providerTestSetRefreshImgSrc()
    {
        return array(            
           array(array('url'=>'http://www.osadvisors.com/images/test.gif')), 
           array(array('url'=>'http://www.osadvisors.com/images/test.jpg')), 
           array(array('url'=>'http://www.osadvisors.com/images/test.png')), 
           array(array('url'=>'http://www.osadvisors.com/images/test.bmp')), 
           array(array('url'=>'http://www.osadvisors.com/images/test.tif')), 
        );
    }    
    
	/**
     * @dataProvider providerTestSetRefreshImgSrc
     */
    public function testSetRefreshImgSrc( $params ) 
    {
       
        try {
            $method = $this->initRest( new FbmlRefreshImgSrc(), $params, FBML_IMGREFRESH_NOSUCHUSER, $this->appId );
            $method->provider->setByContent( $this->session['app_id'], $params['url'], "fill" );
            $result = $method->execute();            
            $this->assertArrayHasKey( 'result', $result, $result );
            $this->assertTrue( '1' == $result['result'], "Error result ({$result['result']}) incorrect" );
            $contents = file_get_contents( $params['url'] );
            $this->assertSame( $contents, $method->provider->getContent(FBML_IMGREFRESH_APPID, $params['url'] ) );
            
            $method->provider->clear( FBML_IMGREFRESH_APPID, $params['url'] );
            $this->assertFalse( $method->provider->getContent(FBML_IMGREFRESH_APPID, $params['url'] ) );
        } catch ( OpenFBAPIException $exception ) {
            $this->fail( "Should not have thrown exception " . $exception );
        }

    }

    public static function providerTestSetRefreshImgSrcBad()
    {
        return array(            
           array(array('url'=>'http://localhost:82375235/images/does/not/exist')), 
           array(array('url'=>'http://localhost:8931/doesnotexist')),
           array(array('url'=>'http://localhost:85723/images/images.wmf')) 
        );
    }    
    
	/**
     * @dataProvider providerTestSetRefreshImgSrcBad
     */
    public function testSetRefreshImgSrcBadUrl( $params ) 
    {
       
        try {
            $method = $this->initRest( new FbmlRefreshImgSrc(), $params, FBML_IMGREFRESH_NOSUCHUSER, $this->appId );
            $method->provider->setByContent( $this->session['app_id'], $params['url'], "fill" );
            $result = $method->execute();
            $this->assertArrayHasKey( 'result', $result, $result);
            $this->assertEquals(0, $result['result']);
        } catch ( OpenFBAPIException $exception ) {
            $this->fail( "Should not have thrown exception " . $exception );
        }

    }
}
?>
