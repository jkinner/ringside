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
require_once( "ringside/api/DefaultRest.php" );
require_once ('ringside/api/bo/Photos.php');

class LocalOpenFBAPI extends Api_DefaultRest {
    public function validateRequest( ) {
    }

    public function execute() {
        
    }
    
}

class OpenFBAPITestCase extends BaseAPITestCase {
    
    public function testConstructor()
    {
        $uid = 123;
        $apiParams = array( 'one'=>1, 'two'=>2 );
   	
        $fb = $this->initRest( new LocalOpenFBAPI(), $apiParams, $uid );
        
        $this->assertEquals( $uid, $fb->getUserId() );
        $this->assertEquals( $apiParams, $fb->getApiParams() );
    }

    public function testvalidateRequiredParams()
    {
        $uid = 123;
        $apiParams = array( 'one'=>1, 'two'=>2 );
        $fb = $this->initRest( new LocalOpenFBAPI(), $apiParams, $uid );

        try {
            $params = array( 'api_key' );
            $fb->checkRequiredParams( $params );
            $this->fail( "Should have gotten an exception." );
        } catch ( OpenFBAPIException $exception ) {
            $this->assertEquals( FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode() );
        }
    }
    
    public function testcreateAlbumLink()
    {
        $aid = 4;
        $uid = 123;
        
        $this->assertEquals( "http://www.ringside.com/album.php?aid=4&id=123",
                             Api_Bo_Photos::createAlbumLink( $aid, $uid ) );
        
    }
}
?>
