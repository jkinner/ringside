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
require_once('BaseAPITestCase.php');
require_once('ringside/api/OpenFBAPIException.php');
require_once('ringside/rest/AdminCreateNetwork.php');
require_once('ringside/rest/AdminDeleteNetwork.php');
require_once('ringside/rest/AdminGetNetworkProperties.php');

class AdminCreateDeleteNetworkPropertiesTestCase extends BaseAPITestCase
{
	public function testExecute() 
   {
   	$uid = 100000;  
   	$appId = 17100;
     	
   	$params = array('name' => 'somenewnetwork', 'auth_url' => 'someurl',
							 'login_url' => 'someurl2', 'canvas_url' => 'someurl3',
							 'web_url' => 'someurl4');
      $method = $this->initRest( new AdminCreateNetwork(), $params, $uid , $appId );
   	$resp = $method->execute();
   	
   	$this->assertTrue(array_key_exists('name', $resp['network']));
   	$this->assertTrue(array_key_exists('key', $resp['network']));
   	$this->assertEquals('somenewnetwork', $resp['network']['name']);
   	
   	$netKey = $resp['network']['key'];
   	
   	$props = implode(',', array('key', 'name'));
   	$params = array('nids' => $netKey, 'properties' => $props);
   	$method = $this->initRest( new AdminGetNetworkProperties(), $params, $uid , $appId );
      $resp = $method->execute();
      
      $this->assertEquals($netKey, $resp['networks'][0]['key']);
		$this->assertEquals('somenewnetwork', $resp['networks'][0]['name']);
	
   	$params = array('nid' => $netKey);
   	$method = $this->initRest( new AdminDeleteNetwork(), $params, $uid , $appId );
   	$resp = $method->execute();
	
	}
}
?>
