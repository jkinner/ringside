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
require_once( 'ringside/api/dao/App.php');
require_once( "ringside/rest/AdminGetAppKeys.php" );

class AdminGetAppKeysTestCase extends BaseAPITestCase
{
	
	public function testExecute()
	{	
		$uid = 17001;		
		$name = 'superapp5372';		
		$apiKey = 'test_case_key-81753';
		$secret = 'nnn283f238h';
		$appProps = array();
		
		$appService = Api_ServiceFactory::create('AppService');
		$appId = $appService->createApp($name, $apiKey, $secret, $appProps);
		Api_Dao_App::setAppOwner($uid, $appId);
		
		$params = array('app_api_key' => $apiKey);
		
		$api = $this->initRest( new AdminGetAppKeys(), $params, $uid, $appId );
		
		$resp = $api->execute();
		
		$this->assertEquals(1, count($resp['resp']));
		$this->assertEquals($apiKey, $resp['resp'][0]['api_key']);
		$this->assertEquals($secret, $resp['resp'][0]['secret']);
		
		$appService->deleteApp($appId);
		Api_Dao_App::removeAppOwner($uid, $appId);
	}

}
?>
