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

require_once( 'ringside/api/cache/CacheProviderInterface.php' );
require_once( 'ringside/api/cache/CacheUtils.php' );

define ( 'MEMCACHEPROVIDER_DEFAULT_EXPIRES' , 120 );
define ( 'MEMCACHEPROVIDER_DEFAULT_HOST', 'localhost' );
define ( 'MEMCACHEPROVIDER_DEFAULT_PORT', 11211 );

/**
 *
 * PHP Cache Provider use's memcache!
 *
 * Memcache must be installed for this to work.
 * This can be installed from PECL on *nix or Win32.
 *
 */
class MemcacheCacheProvider implements CacheProviderInterface {

   private $memcache;
   public $expires = MEMCACHEPROVIDER_DEFAULT_EXPIRES;
   public $host = MEMCACHEPROVIDER_DEFAULT_SERVER;
   public $port = MEMCACHEPROVIDER_DEFAULT_PORT;
    
   public function __construct( ) {

      $this->memcache = new Memcache();
      if ( !$this->memcache->connect( $this->host, $this->port) ) {
         throw new OpenFBAPIException( "Error creating cache", FB_ERROR_CODE_UNKNOWN_ERROR );
      }

   }

   public function setByContent( $scope_id, $key, $value ) {
      global $cacheProviderExpires;

      return $this->memcache->set( $this->makeKey( $scope_id, $key ), $value, MEMCACHE_COMPRESSED, $this->expires );
   }

   public function setByReference( $scope_id, $reference, $type = null ) {
      $contents = CacheUtils::fetch( $reference );
       
      if ( !$contents ) {
         return false;
      } else {
         if ( $type != null && !array_key_exists( $contents['content_type'], $type ) ) {
            return false;
         }

         return $this->memcache->set( $this->makeKey( $scope_id, $key ), $contents, MEMCACHE_COMPRESSED, $this->expires );
      }
   }

   public function getReference( $scope_id, $key ) {
      return "/$scope_id/$key";

   }
    
   public function getContent( $scope_id, $key ) {
      return $this->memcache->get( $this->makeKey( $scope_id, $key ) );
   }
    
   public function clear( $scope_id, $key ) {
      return $this->memcache->delete( $this->makeKey( $scope_id, $key ) );
   }
    
   private function makeKey( $scope_id, $key ) {
      return $scope_id . '-' . $key ;
   }
    
    
   public function isCached( $scope_id, $key ) {
      return (  $this->memcache->get( $this->makeKey( $scope_id, $key ) ) == false ) ? false : true ;
   }

}

?>
