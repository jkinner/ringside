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
require_once( "ringside/api/DefaultRest.php" );
require_once( "ringside/api/bo/Items.php" );

/**
 * @author Mark Lugert mlugert@ringsidenetworks.com
 */
class ItemsSetInfo extends Api_DefaultRest 
{
      private $iid; 
      private $datatype; 
      private $url; 
      private $refurl;
      private $aid;
   
    /**
     * Validate request.
     */
    public function validateRequest( ) {
        
		$this->iid = $this->getRequiredApiParam( 'iid' );
		$this->url = $this->getRequiredApiParam('url');
		$this->refurl = $this->getRequiredApiParam('refurl');
		$this->datatype = $this->getApiParam( 'datatype', 0 );	
		$this->aid = $this->getAppId();
		
		try
    	{
    	   // RXF - this logic was in here, but not sure of intent
    	   $this->checkDefaultApp();
    	   $this->app_id = $this->getApiParam( 'aid', $this->app_id );
    	   
    	}catch(Exception $e)
    	{
    		// do nothing
    	}
    }

    /**
    * Sets the definition of an item.
    *
    * @param string $iid the identifier of the item
    * @param string $url the URL that refers directly to the item
    * @param string $refurl the URL of the page where the user can get information on the item
    * @param int $datatype optional - the type of the item, otherwise the default type
    */
    public function execute() 
    {
        $ret = Api_Bo_Items::setInfo($this->aid, $this->iid, $this->url, $this->refurl, $this->datatype);
        
        $response = array();
        $response [ 'result' ] = $ret?'1':'0';

        return $response;
    }

}
?>
