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

require_once( "ringside/api/OpenFBAPIException.php" );
require_once( "ringside/api/DefaultRest.php" );
require_once( "ringside/api/cache/CacheProviderInterface.php" );

   define ( 'DEFAULT_FBML_REFURL_CACHE', 'CacheLiteProvider' );
   
/**
 * fbml.setRefHandle
 * 
 * @author Richard Friedman
 */
class FbmlRefreshRefUrl extends Api_DefaultRest {

   public $cacheProvider = DEFAULT_FBML_REFURL_CACHE;
   public $provider = null;
   public $contentTypes = array( 'text/html', 'text/plain' );
   
    private $m_url;
    
    public function validateRequest( ) {

        $this->m_url = $this->getRequiredApiParam('url');
        
        require_once( "ringside/api/cache/" . $this->cacheProvider . '.php');
    	$this->provider = new $this->cacheProvider( );
        
    }

    /**
     * Execute the fbml.refreshRefUrl
     *  
     */
    public function execute() {
       
        $response = array();
        $result = $this->provider->setByReference( $this->getAppId() , $this->m_url, $this->contentTypes );
        
        $response['result'] = ($result === false) ? '0' : '1'; 

        return $response;
    }

}

?>
