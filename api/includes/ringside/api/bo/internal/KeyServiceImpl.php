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

include_once 'ringside/api/dao/records/RingsideKeyring.php';
include_once 'ringside/api/bo/KeyService.php';

class Api_Bo_KeyServiceImpl extends Api_Bo_KeyService
{
	public function validate($entityId, $domainId, $apiKey, $secret)
	{
		$q = new Doctrine_Query();
		$q->select('*');
		$q->from('RingsideKeyring');
		$q->where("entity_id=$entityId AND domain_id=$domainId AND api_key='$apiKey' AND secret='$secret'");
			
		$res = $q->execute();
		if (count($res) > 0) {
			return true;
		}
		return false;	
	}
	
	public function getIds($apiKey)
	{
		$q = new Doctrine_Query();
		$q->select('entity_id,domain_id');
		$q->from('RingsideKeyring');
		$q->where("api_key='$apiKey'");
			
		$res = $q->execute();
		if (count($res) > 0) {
			$ids = array();
			$ids['entity_id'] = $res[0]->entity_id;
			$ids['domain_id'] = $res[0]->domain_id;
			return $ids;
		}
		return false;
	}
	
	public function isUnique($apiKey, $secret)
	{
		$q = new Doctrine_Query();
		$q->select('id');
		$q->from('RingsideKeyring');
		$q->where("api_key='$apiKey' AND secret='$secret'");
			
		$res = $q->execute();
		if (count($res) > 0) {
			return false;
		}
		return true;	
	}

	public function createKeyset($entityId, $domainId, $apiKey, $secret)
	{
		if (! $this->isUnique($apiKey, $secret)) {
			throw new Exception("[KeyServiceImpl] cannot create app with non-unique api_key/secret '$apiKey/$secret");
		}
		$keyring = new RingsideKeyring();
		$keyring->entity_id = $entityId;
		$keyring->domain_id = $domainId;
		$keyring->api_key = $apiKey;
		$keyring->secret = $secret;
		if (!$keyring->trySave()) {
			throw new Exception("[KeyServiceImpl] could not create new key with api_key=$apiKey, secret=$secret");
		}
		
		return true;	
	}
	
	public function updateKeyset($entityId, $domainId, $newApiKey, $newSecret)
	{
		$q = new Doctrine_Query();
		$q->select('*');
		$q->from('RingsideKeyring');
		$q->where("entity_id=$entityId AND domain_id=$domainId");
		
		$res = $q->execute();
		if (count($res) > 0) {
			$keyring = $res[0];
			$keyring->api_key = $newApiKey;
			$keyring->secret = $newSecret;
			return $keyring->trySave();
		}	
		return false;
	}
	
	
	public function deleteKeyset($entityId, $domainId)
	{
		$q = new Doctrine_Query();
		$q->select('*');
		$q->from('RingsideKeyring');
		$q->where("entity_id=$entityId AND domain_id=$domainId");
			
		$res = $q->execute();
		if (count($res) > 0) {
			if (!$res[0]->delete()) throw new Exception("[KeyServiceImpl] could not delete keyset entityId=$entityId, domainId=$domainId");
		}	
	}
	
	public function deleteAllKeysets($entityId)
	{
		$q = new Doctrine_Query();
		$q->select('*');
		$q->from('RingsideKeyring');
		$q->where("entity_id=$entityId");
			
		$res = $q->execute();
		if (count($res) > 0) {
			foreach ($res as $ks) {
				$did = $ks->domain_id;
				if (!$ks->delete()) throw new Exception("[KeyServiceImpl] could not delete keyset entityId=$entityId, domainId=$did");
			}
		}	
	}
	
	public function getKeyset($entityId, $domainId)
	{
		$q = new Doctrine_Query();
		$q->select('*');
		$q->from('RingsideKeyring');
		$q->where("entity_id=$entityId AND domain_id=$domainId");

		$res = $q->execute();
		if (count($res) > 0) {
			$rarr = $res->toArray();
			return $rarr[0];
		}
		return null;
	}
	
	public function getAllKeysets($entityId)
	{	
		$q = new Doctrine_Query();		
		$q->select('*');
		$q->from('RingsideKeyring');
		$q->where("entity_id=$entityId");
				
		$res = $q->execute();
		if (count($res) > 0) {
			return $res->toArray();
		}
	
		return null;
	}
	
	public function getAllKeysetsByDomainId($domainId)
	{
	    $q = new Doctrine_Query();		
		$q->select('*');
		$q->from('RingsideKeyring');
		$q->where("domain_id='$domainId' AND entity_id!='$domainId'");
				
		$res = $q->execute();
		if (count($res) > 0) {
			return $res->toArray();
		}
	
		return null;
	}
	
}

?>