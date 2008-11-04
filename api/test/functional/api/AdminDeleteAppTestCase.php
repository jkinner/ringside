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
require_once( "ringside/rest/AdminDeleteApp.php" );
require_once( "ringside/rest/AdminCreateApp.php" );
require_once( "ringside/api/facebook/AdminGetAppProperties.php");

class AdminDeleteAppTestCase extends BaseAPITestCase
{
   public function testExecute()
   {
   	//create app
   	$appId = 17100;
   	$uid = 17001;
   	
   	$params = array('name' => 'my new application 22');   	  	
   	$apiCall = $this->initRest( new AdminCreateApp(), $params, $uid , $appId );
   
   	$resp = $apiCall->execute();
	  	$apiKey = $resp["app"]["api_key"];
   	
   	$this->assertTrue(isset($resp['app']));
   	
   	$params = array( 'properties'=>json_encode(array('application_name','icon_url')), 'app_api_key'=>$apiKey);
   	$apiCall = $this->initRest( new AdminGetAppProperties(), $params, $uid , $appId );
      $response = $apiCall->execute();
      $actual = json_decode( $response['result'], true );
      $this->assertEquals('my new application 22', $actual['application_name']);
      
      //delete app
      $params = array('app_api_key' => $apiKey);
      $apiCall = $this->initRest( new AdminDeleteApp(), $params, $uid , $appId );
      $resp = $apiCall->execute();
      $this->assertTrue($resp);
      
      //verify
      $params = array( 'properties'=>json_encode(array('application_name','icon_url')), 'app_api_key'=>$apiKey);
      $apiCall = $this->initRest( new AdminGetAppProperties(), $params, $uid , $appId );
   	$failed = false;
   	try {
   		$response = $apiCall->execute();	
   	} catch (Exception $e) {
   		$failed = true;
   	}
      if (!$failed) $this->fail("Request for app properties should have failed.");      
   }

}



?>
