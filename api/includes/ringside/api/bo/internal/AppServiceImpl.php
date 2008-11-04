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
include_once 'ringside/api/dao/App.php';
include_once 'ringside/api/bo/AppService.php';

class Api_Bo_AppServiceImpl extends Api_Bo_AppService
{
	protected static $defaultProperties = array('callback_url' => '', 'canvas_url' => '', 'isdefault' => 0, 
											    'sidenav_url' => '', 'icon_url' => '', 'canvas_type' => 0,
											    'desktop' => 0, 'developer_mode' => 0, 'author' => '',
											    'author_url' => '', 'author_description' => '',
											    'support_email' => '', 'application_type' => '',
											    'mobile' => 0, 'deployed' => 0, 'description' => '',
											    'default_fbml' => '', 'tos_url' => '', 'postadd_url' => '',
											    'postremove_url' => '', 'privacy_url' => '', 'ip_list' => '',
											    'about_url' => '', 'logo_url' => '', 'edit_url' => '',
											    'default_column' => 1, 'attachment_action' => '',
											    'attachment_callback_url' => '', 'name' => '');

	public function getNativeIdByApiKey($apiKey)
	{
		$keyService = Api_ServiceFactory::create('KeyService');
		$ids = $keyService->getIds($apiKey);
		if ($ids != null) {
			return $ids['entity_id'];
		}
		return null;
	}
	

	public function appExists($appId)
	{
		$q = new Doctrine_Query();
		$q->select('id');
		$q->from('RingsideApp');
		$q->where("id=$appId");
		
		$res = $q->execute();
		if (count($res) > 0) {
			return true;
		}
		return false;
	}
	
	public function getNativeIdsByProperty($propertyName, $propertyValue)
	{
		if (!array_key_exists($propertyName, self::$defaultProperties)) {
			throw new Exception("[AppServiceImpl] no such app property '$propertyName'");
		}
		
		$q = new Doctrine_Query();
		$q->select('id');
		$q->from('RingsideApp');
		$q->where("$propertyName='$propertyValue'");
		
		$res = $q->execute();
		$ids = null;
		if (count($res) > 0) {
			$ids = array();
			foreach ($res as $app) {
				$ids[] = $app->id;
			}
		}
		return $ids;
	}
	
	public function createApp($name, $apiKey, $secret, $appProperties, $nativeId = null, $domainKey = null)
	{
		$props = array();
		foreach (self::$defaultProperties as $pname => $defaultVal) {			
			$props[$pname] = isset($appProperties[$pname]) ? $appProperties[$pname] : $defaultVal;
		}
	
		$callback_url = $props['callback_url'];
		$canvas_url = $props['canvas_url'];		
		$sidenav_url = $props['sidenav_url'];
		$isdefault = $props['isdefault'];
		$icon_url = $props['icon_url'];
		$canvas_type = $props['canvas_type'];
		$desktop = $props['desktop'];
		$developer_mode = $props['developer_mode'];
		$author = $props['author'];
		$author_url = $props['author_url'];
		$author_description = $props['author_description'];
		$support_email = $props['support_email'];
		$application_type = $props['application_type'];
		$mobile = $props['mobile'];
		$deployed = $props['deployed'];
		$description = $props['description'];
		$default_fbml = $props['default_fbml'];
		$tos_url = $props['tos_url'];
		$postadd_url = $props['postadd_url'];
		$postremove_url = $props['postremove_url'];
		$privacy_url = $props['privacy_url'];
		$ip_list = $props['ip_list'];
		$about_url = $props['about_url'];
		$logo_url = $props['logo_url'];
		$edit_url = $props['edit_url'];
		$default_column = $props['default_column'];
		$attachment_action = $props['attachment_action'];
		$attachment_callback_url = $props['attachment_callback_url'];
	
		$api_key = null; //not used, but eliminates Notices
		$secret_key = null; //not used, but eliminates Notices
		
		$c = Doctrine_Manager::getInstance()->getConnectionForComponent('RingsideApp');
        $c->beginTransaction();
        try {
            $appId = Api_Dao_App::createApp($api_key, $callback_url, $canvas_url, $name, $isdefault,
									    	$secret_key, $sidenav_url, $icon_url, $canvas_type,
									    	$desktop, $developer_mode, $author, $author_url,
									    	$author_description, $support_email, $application_type,
									    	$mobile, $deployed, $description, $default_fbml, $tos_url,
									    	$postadd_url, $postremove_url, $privacy_url, $ip_list,
							   				$about_url, $logo_url, $edit_url, $default_column,
							   				$attachment_action, $attachment_callback_url, $nativeId);
	        if ($appId === false) {
				throw new Exception("[AppServiceImpl] could not create app '$name' with api_key=$apiKey on domain $domainId");
			}
			
			//create app keys for domain
			$keyService = Api_ServiceFactory::create('KeyService');
			$domainService = Api_ServiceFactory::create('DomainService');
			
			if($domainKey == null)
			{
			    $localDomainId = $domainService->getNativeIdByName('Ringside');
		    }
		    else
		    {
		        $localDomainId = $domainService->getNativeIdByApiKey($domainKey);
		    }
			
			if ($localDomainId == null) {
				throw new Exception("[AppServiceImpl] no local domain named 'Ringside' found!");	
			}
			
			if (!$keyService->createKeyset($appId, $localDomainId, $apiKey, $secret)) {
				throw new Exception("[AppServiceImpl] could not create keyset for app '$name' with api_key=$apiKey on domain $domainId");	
			}
            
            $c->commit();
        }
        catch (Exception $e) {
            $c->rollback();
            throw $e;
        }
		
		return $appId;
	}
	
	// must specify the domain_id to get API keys to return with this lookup
	public function getApp($appId, $domain_id=null)
	{

		$q = new Doctrine_Query();
		
		if(isset($domain_id)) {
		    $q->select('a.*,k.api_key');
		    $q->from('RingsideApp a');
    		$q->leftJoin("a.RingsideKeyring k");
		    $q->where("a.id=$appId AND k.domain_id=$domain_id");
	    }
	    else {
	        $q->select('a.*');
    		$q->from('RingsideApp a');
    		$q->where("a.id=$appId");
	    }
		

		$res = $q->execute();

		if (count($res) > 0) {
		    $app_element = $res[0]->toArray();
            if($domain_id) {
                $app_element['api_key'] = $app_element["RingsideKeyring"][0]['api_key'];
                unset($app_element['RingsideKeyring']);
            }
			return $app_element;
		}
		return null;
	}
	
	public function getAllApps()
	{
		$q = Doctrine_Query::create();
		$q->from('RingsideApp a');
		$q->leftJoin("a.RingsideKeyring k");
		$res = $q->execute();
		if (count($res) > 0) {
			return $res->toArray();
		}
	
		return null;
	}
	
	public function updateApp($appId, $newProps)
	{		
		$props = array();
		
		foreach ($newProps as $pname => $pval) {
			$propName = $pname;			
			if (strpos($pname, '.') === false) {
				$fullName = "RingsideApp.$pname"; 
			} else {
				$fullName = $pname;
				$tarr = explode('.', $pname);
				$propName = $tarr[1];
			}
			if (!array_key_exists($propName, self::$defaultProperties)) {
				throw new Exception("[AppServiceImpl] unknown property '$pname'.");
			}			
			
			$props[$fullName]= $pval; 
		}
		Api_Dao_App::updateAppProperties($appId, $props, null);	
	}
	
	public function deleteApp($appId)
	{
		$keyService = Api_ServiceFactory::create('KeyService');
		$keyService->deleteAllKeysets($appId);
	
		$q = new Doctrine_Query();
		$q->select('*');
		$q->from('RingsideApp');
		$q->where("id=$appId");
		
		$res = $q->execute();
		if (count($res) > 0) {
			if (!$res[0]->delete()) throw new Exception("[AppServiceImpl] could not delete app with id=$appId");
		}
	}
	
}

?>