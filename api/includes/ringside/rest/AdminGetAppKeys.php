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

require_once ("ringside/api/DefaultRest.php");
require_once ("ringside/api/bo/App.php");
require_once 'ringside/api/ServiceFactory.php';

/**
 * Gets API and secret keys for non-Ringside networks associated
 * with a given app and app developer.
 *
 * @author Mike Schachter
 * @apiName AdminGetAppKeys
 * @apiRequired app_api_key The Ringside API key of the application.
 * @callMethod ringside.users.getAppKeys
 * @return Array an array of associative arrays, one for each network, with
 * 		  the following key-value pairs:
 * 		  network_id - the ID of the network
 * 		  api_key - API key of network
 * 		  secret - secret of network
 */
class AdminGetAppKeys extends Api_DefaultRest
{
    protected $m_appApiKey;
    protected $m_aid;
    protected $m_nid;
    protected $m_canvasName;
     
    public function validateRequest()
    {

        $this->checkOneOfRequiredParams(array('app_api_key', 'aid', 'canvas_url'));
        $this->m_appApiKey = $this->getApiParam('app_api_key');
        $this->m_aid = $this->getApiParam('aid');
        $this->m_canvasName = $this->getApiParam('canvas_url');
        $this->m_nid = $this->getApiParam('nid', $this->getNetworkId());
    }

    public function execute()
    {
    	$appService = Api_ServiceFactory::create('AppService');
    	$domainService = Api_ServiceFactory::create('DomainService');
        $aid = false;
        if ( null != $this->m_aid )
        {
            $aid = $this->m_aid;
        }
        elseif ( null != $this->m_appApiKey )
        {
            $aid = $appService->getNativeIdByApiKey($this->m_appApiKey);
        }
        elseif ( null != $this->m_canvasName)
        {
            $aid = $appService->getNativeIdByProperty('canvas_url', $this->m_canvasName);
        }
         
        if (false !== $aid)
        {
            // TODO: Is this the right way to check for admin users?
            if($this->getUserId() > 0 && ! Api_Bo_App::checkUserOwnsApp($this->getUserId(), $aid))
            {
                throw new Exception('Cannot query for app keys: permission denied');
            }

            $keyService = Api_ServiceFactory::create('KeyService');
            $keysets = $keyService->getAllKeysets($aid);
            
            $rkeys = array();
            foreach ($keysets as $ks) {
            	$arr = array();
            	$dkeys = $keyService->getKeyset($ks['domain_id'], $ks['domain_id']);
            	$domainKey = $dkeys['api_key'];            	
            	
            	$arr['network_id'] = $domainKey;
            	$arr['api_key'] = $ks['api_key'];
            	$arr['secret'] = $ks['secret'];
            	
            	$rkeys[] = $arr;
            }
            
            $response = array('resp' => $rkeys);
            
            return $response;
        }
        
        throw new Exception("Could not find app with api key '{$this->m_appApiKey}' on network '{$this->m_nid}'");
    }
}

?>
