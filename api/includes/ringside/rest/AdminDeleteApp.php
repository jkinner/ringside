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
require_once ('ringside/api/DefaultRest.php');
require_once ('ringside/api/ServiceFactory.php');

class AdminDeleteApp extends Api_DefaultRest
{
    protected $m_appApiKey;
    protected $m_aid;
    protected $m_canvasName;

    public function validateRequest()
    {
        //make sure calling application is a default application
        $this->checkDefaultApp();

        $this->checkOneOfRequiredParams(array('app_api_key', 'aid', 'canvas_url'));

        //check to make sure name is set
        $this->m_appApiKey = $this->getApiParam('app_api_key');
        $this->m_aid = $this->getApiParam('aid');
        $this->m_canvasName = $this->getApiParam('canvas_url');
        $this->m_appApiKey = $this->getRequiredApiParam('app_api_key');
    }

    public function execute()
    {
    	$appId = NULL;
    	$appService = Api_ServiceFactory::create('AppService');
		
		//check to make sure name isn't already taken
		
        if ( null != $this->m_aid )
        {
            $appId = $this->m_aid;
        }
        elseif ( null != $this->m_appApiKey )
        {
            $appId = $appService->getNativeIdByApiKey($this->m_appApiKey);
        }
        elseif ( null != $this->m_canvasName )
        {
        	$ids = $appService->getNativeIdByProperty('canvas_url', $this->m_canvasName);
        	if (count($ids) > 0) {
        		$appId = $ids[0];
        	}            
        }
        $appService->deleteApp($appId);
        
        return true;
    }
}

?>
