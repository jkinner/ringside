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

   define ( 'DEFAULT_FBML_REFHANDLE_CACHE', 'CacheLiteProvider' );
   
/**
 * fbml.setRefHandle
 * 
 * @author Richard Friedman
 */
class FbmlSetRefHandle extends Api_DefaultRest {

   public $cacheProvider = DEFAULT_FBML_REFHANDLE_CACHE;
   public $provider = null;
   
    private $m_handle;
    private $m_fbml;
    
    public function validateRequest( ) {
        
        $this->m_handle = $this->getRequiredApiParam('handle');
        $this->m_fbml = $this->getRequiredApiParam('fbml');

         require_once( "ringside/api/cache/" . $this->cacheProvider . '.php' );
    	$this->provider = new $this->cacheProvider( );
        
    }

    public function execute() {
       
        $response = array();

        $result = $this->provider->setByContent( $this->getAppId() , $this->m_handle, $this->m_fbml );
    	  if (!isset($result) || empty($result)) $result = true;
        $response['result'] = ( $result == true ) ? 1 : 0; 

        return $response;
    }

}

?>
