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

interface CacheProviderInterface {

   /**
    * Set the content by passing in a blob of the content. 
    *
    * @param string $scope_id Scoping occurs at the application level, this should be the appid or appkey
    * @param string $key key name
    * @param string $value the text you would like to store
    * 
    * @return true/false on success
    */
   public function setByContent( $scope_id, $key, $value );

   /**
    * Set the content by passing in a reference to the content. 
    *
    * @param string $scope_id Scoping occurs at the application level, this should be the appid or appkey    
    * @param string $reference URL to some form of content.
    * @param array $type array list of types supported
    * 
    * @return true/false if saved.  Note this method can respond false because of a content type unsupported.   
    */
   public function setByReference( $scope_id, $reference, $type = null );

   /**
    * Get a reference to the content via a URL. 
    *
    * @param string $scope_id Scoping occurs at the application level, this should be the appid or appkey
    * @param string $key key name
    * 
    * @return URL to the content, null if unsupported.   
    */
   public function getReference( $scope_id, $key );
   
   /**
    * Get the actual content. 
    *
    * @param string $scope_id Scoping occurs at the application level, this should be the appid or appkey
    * @param string $key key name
    * 
    * @return the content as is in the object. null if content could not be retrieved.    
    */
   public function getContent( $scope_id, $key );
   
   /**
    * Let the cache know this content can be removed/cleared/cleaned up.  
    *
    * @param string $scope_id Scoping occurs at the application level, this should be the appid or appkey
    * @param string $key key name
    * 
    * @return true/false if it understands and can execute the request.    
    */
   public function clear( $scope_id, $key );
   
   /**
    * Test if an item is cached, preferably without retrieving it. 
    *
    * @param string $scope_id
    * @param string $key
    */
   public function isCached( $scope_id, $key );

}
?>
