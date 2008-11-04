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
require_once('ringside/api/ServiceFactory.php');

abstract class Api_Bo_DomainService
{
    const DEFAULT_DOMAIN_SERVICE = 'Api_Bo_DomainServiceImpl';
    	
    /**
     * Creates an instance of the domain service
     *
     * @param string $implClass optional - implementation class name to instantiate.
     * @return Api_Bo_DomainService
     */
    public static function create($implClass = null)
    {
        return Api_ServiceFactory::create('Api_Bo_DomainService', self::DEFAULT_DOMAIN_SERVICE, $implClass);
    }
    
	public abstract function getNativeIdByApiKey($apiKey);
  
	public abstract function getNativeIdByName($name);
  
	public abstract function createDomain($name, $url, $apiKey, $secret);
	
	public abstract function deleteDomain($domainId);

	public abstract function getDomain($domainId);

	public abstract function getAllDomains();
	
	public abstract function getAppsByApiKey($apiKey);
}



?>