<?php

include_once 'ringside/api/ServiceFactory.php';

class AllBoTestFixtures
{
	public static function create()
	{
		$domainService = Api_ServiceFactory::create('DomainService');
		$localDomainId = $domainService->getNativeIdByName('Ringside');
		if ($localDomainId != NULL)  $domainService->deleteDomain($localDomainId);
		
		$domainService->createDomain('Ringside', 'http://localhost', 'test-api-key', 'test-secret');
	}
	
	public static function destroy()
	{
		$domainService = Api_ServiceFactory::create('DomainService');
		$localDomainId = $domainService->getNativeIdByName('Ringside');
		if ($localDomainId != NULL)  $domainService->deleteDomain($localDomainId);
	}
}




?>