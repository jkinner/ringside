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
include_once 'ringside/api/ServiceFactory.php';
include_once 'ringside/api/bo/DomainService.php';
include_once 'ringside/api/dao/records/RingsideDomain.php';

class Api_Bo_DomainServiceImpl extends Api_Bo_DomainService
{
	public function createDomain($name,$url,$apiKey, $secret)
	{
		$domain = new RingsideDomain();
		$domain->name = $name;
		$domain->url = $url;
		if (!$domain->trySave()) {
			throw new Exception("[DomainServiceImpl] could not create domain with name=$name, url=$url");
		}
	    $keyService = Api_ServiceFactory::create('KeyService');
	    $keyService->createKeyset($domain["id"],$domain["id"],$apiKey, $secret);
		return $domain;
	}
	
	public function deleteDomain($domainId)
	{
		if(!$domainId)
		{
		    return false;
	    }
		
		$keyService = Api_ServiceFactory::create('KeyService');
		$keyService->deleteAllKeysets($domainId);
		
		$d = Doctrine::getTable('RingsideDomain')->find($domainId);
		if($d !==false) {
			return $d->delete();
		} else {
			return false;
		}
	}
	
	public function getNativeIdByApiKey($apiKey)
	{
		$keyService = Api_ServiceFactory::create('KeyService');
	    if($array_of_domain_ids = $keyService->getIds($apiKey))
	  	{
	    	return $array_of_domain_ids["domain_id"];
	    } else {
	      	return null;
	    }
	}
	
	public function getAppsByApiKey($apiKey)
	{
	    if($apiKey == null)
	    {
	        error_log("No ApiKey was sent to the getAppsByApiKey method");
	        return null;
        }
	    $domain_id = $this->getNativeIdByApiKey($apiKey);
	    $keyService = Api_ServiceFactory::create('KeyService');
		$keys = $keyService->getAllKeysetsByDomainId($domain_id);
		
		$appService = Api_ServiceFactory::create('AppService');
		
		if(count($keys>0))
		{
    	    foreach($keys as $key)
    	    {
    	        $apps[] = $appService->getApp($key['entity_id'],$key['domain_id']);
    	    }
    	    if(count($apps)>0)
    	    {
    	        return $apps;
    	    }
        }
        else
        {
            return null;
        }
	}
	
	public function getNativeIdByName($name)
	{
		$domainTable = Doctrine::getTable('RingsideDomain');
	  	if($domain = $domainTable->findOneByName($name))
	    {
	        return $domain->id;
	    } else {
	      	return null;
      	}
	}

	public function getDomain($domainId)
	{	
		$domain_table = Doctrine::getTable('RingsideDomain');
	    $domain = $domain_table->find($domainId);
	    if ($domain !== false)
	    {
	        return $domain->toArray();
        } else {
	        return null;
        }
	}

	public function getAllDomains()
	{        
        $domains = Doctrine::getTable('RingsideDomain')->findAll();
        if ($domains !==false)
        {
            return $domains;
        } else {
            return null;
        }
	}

	
}

?>