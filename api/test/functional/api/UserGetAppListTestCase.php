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
require_once( "ringside/rest/UsersGetAppList.php" );

class UserGetAppListTestCase extends BaseAPITestCase
{ 
    public function testExecute()
    {     
        try {
            $params = array();
            $method = $this->initRest( new UsersGetAppList(), $params, 17001 );
            $this->assertTrue( $method != null );
            
            $resp = $method->execute();
            
            $app = $resp["apps"][0];
            $this->assertEquals(17100, $app["app_id"]);
            $this->assertEquals(17001, $app["user_id"]);
            $this->assertEquals(1, $app["enabled"]);
            $this->assertEquals("test app", $app["name"]);
            $this->assertEquals("theCanvasUrl", $app["canvas_url"]);
            $this->assertEquals("theSideNavUrl", $app["sidenav_url"]);
//            $this->assertEquals("test_case_key-17100", $app["api_key"]);
            $this->assertEquals("theCallbackUrl", $app["callback_url"]);
            $this->assertEquals(1, $app["canvas_type"]);
            $this->assertEquals(1, $app["application_type"]);
            $this->assertEquals("Client test app", $app["description"]);
            $this->assertEquals("http://url.com/postadd", $app["postadd_url"]);
            $this->assertEquals("http://url.com/postremove", $app["postremove_url"]);
            $this->assertEquals("http://url.com/about", $app["about_url"]);
            $this->assertEquals("John Q", $app["author"]);
            $this->assertEquals("http://www.jonq.tv", $app["author_url"]);
            $this->assertEquals("John Q Industries", $app["author_description"]);
            
        } catch ( OpenFBAPIException $exception ) {
            $this->fail( "No exception expected " . $exception->getCode() );
        }
    }
}
?>
