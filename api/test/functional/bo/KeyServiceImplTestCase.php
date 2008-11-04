<?php

require_once 'ringside/api/bo/App.php';
require_once 'ringside/api/ServiceFactory.php';

class KeyServiceImplTestCase extends PHPUnit_Framework_TestCase
{	
	public function testAppKeysets()
	{
		$keyService = Api_ServiceFactory::create('KeyService');	
		$this->assertTrue($keyService != NULL);
		
		//ficticious ids
		$appId = 1001;
		
		$domainId1 = 1024;
		$domainId2 = 12875;
		$domainId3 = 666; 	//mark of the beast
		
		$apiKey1 = '3883275772dxxxx';
		$secret1 = '8235uiuasd23nnn';
		$apiKey2 = '3983hhnn3nnn2';
		$secret2 = 'x98275majf93';
		$apiKey3 = 'qht278nnf9a0';
		$secret3 = '00382xxnnah';
		
		//test createKeyset
		$this->assertTrue($keyService->createKeyset($appId, $domainId1, $apiKey1, $secret1));
		$this->assertTrue($keyService->createKeyset($appId, $domainId2, $apiKey2, $secret2));
		$this->assertTrue($keyService->createKeyset($appId, $domainId3, $apiKey3, $secret3));
		
		//test uniqueness
		$this->assertFalse($keyService->isUnique($apiKey1, $secret1));
		$this->assertFalse($keyService->isUnique($apiKey2, $secret2));
		$this->assertFalse($keyService->isUnique($apiKey3, $secret3));
		$this->assertTrue($keyService->isUnique('goats goats goats', 'asdf123'));
		
		//test getKeyset
		$kset = $keyService->getKeyset($appId, $domainId1);
		$this->assertEquals($appId, $kset['entity_id']);
		$this->assertEquals($domainId1, $kset['domain_id']);
		$this->assertEquals($apiKey1, $kset['api_key']);
		$this->assertEquals($secret1, $kset['secret']);
		
		$kset = $keyService->getKeyset($appId, $domainId2);
		$this->assertEquals($appId, $kset['entity_id']);
		$this->assertEquals($domainId2, $kset['domain_id']);
		$this->assertEquals($apiKey2, $kset['api_key']);
		$this->assertEquals($secret2, $kset['secret']);
		
		$kset = $keyService->getKeyset($appId, $domainId3);
		$this->assertEquals($appId, $kset['entity_id']);
		$this->assertEquals($domainId3, $kset['domain_id']);
		$this->assertEquals($apiKey3, $kset['api_key']);
		$this->assertEquals($secret3, $kset['secret']);
				
		//test getIds
		$ids = $keyService->getIds($apiKey1);
		$this->assertEquals($appId, $ids['entity_id']);
		$this->assertEquals($domainId1, $ids['domain_id']);
		
		//test getAllKeysets
		$ksets = $keyService->getAllKeysets($appId);
		$this->assertNotNull($ksets);
		$this->assertEquals(3, count($ksets));
		
		//test deleteKeyset
		$keyService->deleteKeyset($appId, $domainId2);
		$ksets = $keyService->getAllKeysets($appId);
		$this->assertNotNull($ksets);
		$this->assertEquals(2, count($ksets));
		
		$keyService->deleteAllKeysets($appId);
		$ksets = $keyService->getAllKeysets($appId);
		$this->assertNull($ksets);
	}	
}
?>