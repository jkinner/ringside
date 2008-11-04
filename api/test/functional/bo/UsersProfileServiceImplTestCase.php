<?php

require_once 'ringside/api/ServiceFactory.php';

class UsersProfileServiceImplTestCase extends PHPUnit_Framework_TestCase
{	
	public function testService()
	{
		$upService = Api_ServiceFactory::create('UserProfileService');	
		$this->assertTrue($upService != NULL);		
		$this->assertTrue($upService->isSupported('activities'));
		$this->assertFalse($upService->isSupported('goats'));
	}	
}
?>