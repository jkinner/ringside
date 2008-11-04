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
require_once( "ringside/rest/AdminGetServerInfo.php" );


class AdminGetServerInfoTestCase extends BaseAPITestCase
{
	public function testExecute()
	{
		$uid = 17001;
		$appId = 17100;
		$params = array();
		$session = array("app_id" => $appId);
		
		$_SERVER['SERVER_NAME'] = 'localhost';
		$_SERVER['SERVER_PORT'] = 8080;
		
        $api = $this->initRest( new AdminGetServerInfo(), $params, $uid, $appId );
		$resp = $api->execute();
		
		$this->assertTrue(array_key_exists('web_root', $resp['result']));	
		$this->assertTrue(array_key_exists('web_url', $resp['result']));
		$this->assertTrue(array_key_exists('demo_url', $resp['result']));
		$this->assertTrue(array_key_exists('server_url', $resp['result']));
		$this->assertTrue(array_key_exists('support_email', $resp['result']));
		$this->assertEquals('admin@localhost', $resp['result']['support_email']);		
	}

}



?>
