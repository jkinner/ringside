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
require_once( 'Cache/Lite.php' );

define ( 'CACHELITE_DEFAULT_TEMPDIR', './cache-lite-' );
define ( 'CACHELITE_DEFAULT_LIFETIME', '7200' );
define ( 'CACHELITE_DEFAULT_MEMORY_ONLY', true );
/**
 * 
 * PHP Cache Provider uses memcache!
 *
 * This uses the PEAR::CacheLite, please install from pear.
 * pear install CacheLite
 * 
 */
class CacheLiteProvider implements CacheProviderInterface {

   private $cache;
   
    public function __construct( ) {

    		$tempDirectory = self::getTempDir();
    		
         $options = array(
             'lifeTime' => CACHELITE_DEFAULT_LIFETIME,
             'onlyMemoryCaching' => CACHELITE_DEFAULT_MEMORY_ONLY,
         	 'pearErrorMode' => CACHE_LITE_ERROR_DIE
         );

         if ( $tempDirectory ) {
         	$options['cacheDir'] = $tempDirectory .'/';
         }
         
//         error_log("Cache-Lite is using ".$tempDirectory);
         $this->cache = new Cache_Lite($options);        
    }
    
    protected static function getTempDir()
    {
    	$tmpdir = false;
    	if ( function_exists('sys_get_temp_dir') ) {
       	$temp_dir = sys_get_temp_dir();
       	if ( self::_checkTmpDir($temp_dir) ) {
       		$tmpdir = $temp_dir;
       	}
    	}
    	
        if ( ! $tmpdir && !empty($_ENV['TMP']) ) {
            	$temp_dir = realpath( $_ENV['TMP'] );
            	if ( self::_checkTmpDir($temp_dir) ) {
            		$tmpdir = $temp_dir; 
            	}
        }
        
        if ( ! $tmpdir && ! empty($_ENV['TMPDIR']) ) {
                $temp_dir = realpath( $_ENV['TMPDIR'] );
            	if ( self::_checkTmpDir($temp_dir) ) {
            		$tmpdir = $temp_dir; 
            	}
        }

        if ( ! $tmpdir && !empty($_ENV['TEMP']) ) {
                $temp_idr = realpath( $_ENV['TEMP'] );
            	if ( self::_checkTmpDir($temp_dir) ) {
            		$tmpdir = $temp_dir; 
            	}
        }

        if ( ! $tmpdir ) {
        			$temp_file = tempnam( md5(uniqid(rand(), TRUE)), '' );
                if ( $temp_file ) {
                    $temp_dir = realpath( dirname($temp_file) );
                    unlink( $temp_file );
                    $tmpdir = $temp_dir;
                }
        }
    
        return $tmpdir;
    }

    private static function _checkTmpDir($tmpdir) {
    	$result = true;
    	  if ( ( $ctfile = fopen("$tmpdir/cache-test", 'w') ) === false ) {
//        		error_log("Cache_Lite configuration: Cannot write to $tmpdir");
        		$result = false;
        }
        		
        if ( $ctfile ) {
       		fclose($ctfile);
       		@unlink($ctfile);
        }
        
        return $result;
    }
    
   public function isCached( $scope_id, $key ) {
       return ( $this->cache->get( $this->makeKey( $scope_id, $key ) ) == false ) ? false : true ; 
   }
      
   public function setByContent( $scope_id, $key, $value ) {
      return $this->cache->save( $value, $this->makeKey( $scope_id, $key ) );  
   }

   public function setByReference( $scope_id, $reference, $type = null ) {

      // TODO make sure valid content
      try {
         $contents = CacheUtils::fetch( $reference );
         
         if ($contents === false) {
            return false;      
         } else if ( $contents['http_code'] != '200'  ) {
            return false; 
         } else if ( $type != null && !in_array( $contents['content_type'], $type ) ) {
            return false;
         } else { 
            $result = $this->cache->save( $contents['body'], $this->makeKey( $scope_id, $reference ) );            
            return $result;
         }  
      } catch ( Exception $exception) {
         return false;
      }
   }

   public function getReference( $scope_id, $key ) {
      return "/cache/CacheLiteProvider/$scope_id/$key";      
   }
   
   public function getContent( $scope_id, $key ) {
      return $this->cache->get( $this->makeKey( $scope_id, $key ) );      
   }
   
   public function clear( $scope_id, $key ) {
      return $this->cache->remove( $this->makeKey( $scope_id, $key ) );
   }
   
   private function makeKey( $scope_id, $key ) {
      return md5( $scope_id . '-' . $key );
   }
      
}

?>
