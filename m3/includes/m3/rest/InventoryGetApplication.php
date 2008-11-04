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

require_once 'ringside/m3/AbstractRest.php';
require_once 'ringside/api/bo/App.php';
require_once 'ringside/api/ServiceFactory.php';

/**
 * M3 API that returns a inventory of an application deployed inside the Ringside server.
 *
 * @author John Mazzitelli
 */
class InventoryGetApplication extends M3_AbstractRest
{
    private $appId;

    public function validateRequest()
    {
        $this->appId = $this->getRequiredApiParam("appId");
    }

    /**
     * Returns an array containing the information on the given application
     * identified by its app ID.
     *
     * @return array list of information on the given application deployed in the server
     */
    public function execute()
    {
    	$appService = Api_ServiceFactory::create('AppService');
    	$_app = $appService->getApp($this->appId);
        
        return array('application' => $_app);
    }
}
?>