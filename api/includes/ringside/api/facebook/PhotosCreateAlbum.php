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
require_once ('ringside/api/bo/Photos.php');

/**
 * Photos.createAlbum API
 */
class PhotosCreateAlbum extends Api_DefaultRest {
    /** The album name. */
    private $m_name;
    
    /** The album location. */
    private $m_location;
    
    /** The album description. */
    private $m_description;

    public function validateRequest( ) {

        $this->m_name = $this->getRequiredApiParam( 'name' );
        $this->m_location = $this->getApiParam( 'location' , '' );
        $this->m_description = $this->getApiParam( 'description' , '' );
    }

    /**
     * Execute the Photos.createAlbum method
     *
     * @return The information about the created album.  This is returned in an associative aray:
     *  array( 'aid'=>12345,
     *         'cover_pid'=>0
     *         'owner'=>23212
     *         'name'=>My photo album name
     *         'created'=>1042652187
     *         'modified'=>1042652200
     *         'description'=>my photo album description
     *         'location'=>London, England
     *         'link'=>http://www.ringside.com/album.php?aid=12345&id=23212
     *         'size'=>0 )
     */
    public function execute() 
    {
    	$time = time();
        $aid = Api_Bo_Photos::createAlbum(0, $this->getDescription(), $this->getLocation(), $this->getName(), $this->getUserId());

        if($aid)
        {
	        $link = Api_Bo_Photos::createAlbumLink( $aid, $this->getUserId() );
	        
	        $retVal = array();
	        $retVal[ FB_PHOTOS_AID ] = $aid;        
	        $retVal[ FB_PHOTOS_COVER_PID ] = 0;
	        $retVal[ FB_PHOTOS_CREATED ] = $time;
	        $retVal[ FB_PHOTOS_DESCRIPTION ] = $this->getDescription();
	        $retVal[ FB_PHOTOS_LINK ] = $link;
	        $retVal[ FB_PHOTOS_LOCATION ] = $this->getLocation();
	        $retVal[ FB_PHOTOS_MODIFIED ] = $time;
	        $retVal[ FB_PHOTOS_NAME ] = $this->getName();
	        $retVal[ FB_PHOTOS_OWNER ] = $this->getUserId();
	        $retVal[ FB_PHOTOS_SIZE ] = 0;
        }
        return $retVal;
    }

    /**
     * Get the album name.
     *
     * @return unknown The album name.
     */
    public function getName() {
        return $this->m_name; 
    }

    /**
     * Get the album location.
     *
     * @return unknown The album location.
     */
    public function getLocation() {
        return $this->m_location; 
    }

    /**
     * Get the album description.
     *
     * @return unknown The album description.
     */
    public function getDescription() {
        return $this->m_description; 
    }
} 
?>
