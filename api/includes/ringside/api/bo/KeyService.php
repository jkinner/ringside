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

require_once 'ringside/api/ServiceFactory.php';

abstract class Api_Bo_KeyService
{	
    /**
     * Enter description here...
     *
     * @param string $implClass
     * @return Api_Bo_KeyService
     */
   public static function create($implClass = null)
   {
       return Api_ServiceFactory::create('Api_Bo_KeyService', 'Api_Bo_KeyServiceImpl', $implClass);
   }
   
	public abstract function validate($entityId, $domainId, $apiKey, $secret);

	/**
	 * Returns an associative array $ids with two elements:
	 * $ids['entity_id'] = entity id associated with $apiKey
	 * $ids['domain_id'] = domain id associated with $apiKey
	 */
	public abstract function getIds($apiKey);
	
	public abstract function isUnique($apiKey, $secret);
	
	public abstract function createKeyset($entityId, $domainId, $apiKey, $secret);
	
	public abstract function getKeyset($entityId, $domainId);
	
	public abstract function getAllKeysets($entityId);
	
	public abstract function getAllKeysetsByDomainId($domainId);
	
	public abstract function deleteKeyset($entityId, $domainId);

	public abstract function deleteAllKeysets($entityId);
	
	public abstract function updateKeyset($entityId, $domainId, $newApiKey, $newSecret);
}


?>