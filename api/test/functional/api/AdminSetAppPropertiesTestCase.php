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
require_once( "ringside/api/facebook/AdminSetAppProperties.php" );

class AdminSetAppPropertiesTestCase extends BaseAPITestCase
{
   public function testExecute()
   {
   	$uid = 11101;
   	$aid = 11000;

   	$getParams = array("properties"=>json_encode(array("secret_key","canvas_url","author","description")),
   						    "app_api_key"=>"application-1200");

   	$api = $this->initRest( new AdminGetAppProperties(), $getParams, $uid, $aid );
      
      $resp = $api->execute();
      $app = json_decode($resp["result"], true);
      
      $oldCanvasUrl = $app["canvas_url"];
      $oldAuthor = $app["author"];
      $oldDescription = $app["description"];
      
      $newCanvasUrl = "newcanvas";
      $newAuthor = "newauthor";
      $newDescription = "newdescription";
      
      $setParams = array("properties"=>json_encode(array("canvas_url"=>$newCanvasUrl,
      													"author"=>$newAuthor,
      													"description"=>$newDescription)),
   						    "app_api_key"=>"application-1200");
      
   	$api = $this->initRest( new AdminSetAppProperties(), $setParams, $uid, $aid );
      
     	$resp = $api->execute();
     	$this->assertTrue($resp);
     	
   	$api = $this->initRest( new AdminGetAppProperties(), $getParams, $uid, $aid );
      
      $resp = $api->execute();
      $app = json_decode($resp["result"], true);
      
      $this->assertEquals($newCanvasUrl, $app["canvas_url"]);
      $this->assertEquals($newAuthor, $app["author"]);
      $this->assertEquals($newDescription, $app["description"]);
      
      
      $setParams = array("properties"=>json_encode(array("canvas_url"=>$oldCanvasUrl,
      													"author"=>$oldAuthor,
      													"description"=>$oldDescription)),
   						    "app_api_key"=>"application-1200");
      
   	$api = $this->initRest( new AdminSetAppProperties(), $setParams, $uid, $aid );
      
     	$resp = $api->execute();
     	$this->assertTrue($resp);
     	
   	$api = $this->initRest( new AdminGetAppProperties(), $getParams, $uid, $aid );
      
      $resp = $api->execute();
      $app = json_decode($resp["result"], true);
            
      $this->assertEquals($oldCanvasUrl, $app["canvas_url"]);
      $this->assertEquals($oldAuthor, $app["author"]);
      $this->assertEquals($oldDescription, $app["description"]);
   }   
}
?>
