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

class AdminGetNetworkPropertiesTestCase extends BaseAPITestCase
{
	public function testExecute() 
	{
	   $uid = 100000;
	   $appId = 17100;
     	   
     	$props = array('key', 'name', 'auth_url', 'login_url',
							'canvas_url', 'web_url', 'social_url',
							'auth_class', 'postmap_url');
   	$params = array('nids' => 'testdata_key1',
   						 'properties' => implode(',', $props));
        $method = $this->initRest( new AdminGetNetworkProperties(), $params, $uid, $appId );
      $resp = $method->execute();
      
      $this->assertEquals('testdata_key1', $resp['networks'][0]['key']);
		$this->assertEquals('testdata_name1', $resp['networks'][0]['name']);
		$this->assertEquals('testdata_auth1', $resp['networks'][0]['auth_url']);
		$this->assertEquals('testdata_login1', $resp['networks'][0]['login_url']);
		$this->assertEquals('testdata_canvas1', $resp['networks'][0]['canvas_url']);
		$this->assertEquals('testdata_web1', $resp['networks'][0]['web_url']);
		$this->assertEquals('testdata_social1', $resp['networks'][0]['social_url']);
		$this->assertEquals('testdata_authclass1', $resp['networks'][0]['auth_class']);
		$this->assertEquals('testdata_postmap1', $resp['networks'][0]['postmap_url']);
	}
}
?>
