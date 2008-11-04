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
require_once( "ringside/rest/AdminGetAppKeys.php" );
require_once( "ringside/rest/AdminSetAppKeys.php" );
require_once( "ringside/api/dao/UsersApp.php");

class AdminSetAppKeysTestCase extends BaseAPITestCase
{	
	public function testExecute()
	{
		$uid = 17001;		
		$name = 'superapp5372';		
		$oldApiKey = 'test_case_key-81753';
		$oldSecret = 'nnn283f238h';
		$appProps = array();
		
		$keyService = Api_ServiceFactory::create('KeyService');
		$domainService = Api_ServiceFactory::create('DomainService');
		$localDomainId = $domainService->getNativeIdByName('Ringside');
		$ids = $keyService->getKeyset($localDomainId, $localDomainId);
		$domainKey = $ids['api_key'];
		$this->assertNotNull($domainKey);
		
		$appService = Api_ServiceFactory::create('AppService');
		$appId = $appService->createApp($name, $oldApiKey, $oldSecret, $appProps);
		Api_Dao_App::setAppOwner($uid, $appId);
		
		$params = array('app_api_key' => $oldApiKey);
				
		$newApiKey = '237825y235nn';
		$newSecret = '328752hannafds';
		
		$kprops = array();
		$kprops[] = array('network_id' => $domainKey, 'api_key' => $newApiKey, 'secret' => $newSecret);				
		$params = array('app_id' => $appId, 'keys' => json_encode($kprops));
		
		$api = $this->initRest( new AdminSetAppKeys(), $params, $uid, $appId );
		$this->assertTrue($api->execute());
		
		$params = array('app_api_key' => $newApiKey);
		
		$api = $this->initRest( new AdminGetAppKeys(), $params, $uid, $appId );		
		$resp = $api->execute();
		
		$this->assertEquals(1, count($resp['resp']));
		$this->assertEquals($newApiKey, $resp['resp'][0]['api_key']);
		$this->assertEquals($newSecret, $resp['resp'][0]['secret']);
		
		$appService->deleteApp($appId);
		Api_Dao_App::removeAppOwner($uid, $appId);
	}
}
?>
