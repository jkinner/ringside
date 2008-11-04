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

require_once ('ringside/api/config/RingsideApiConfig.php');
require_once ('ringside/api/cache/CacheProviderInterface.php');
require_once ('ringside/api/cache/CacheUtils.php');

/**
 * A cache provider which uses storage and retrieval through
 * the current executing server.  This is meant for local
 * installation process only.  
 *
 */
class HtdocsProvider implements CacheProviderInterface
{
	
	private $m_dirKey;
	private $m_rootUrl;
	private $m_rootDir;
	
	/**
	 * Construct a Htdocs cache provider which writes out application
	 * Specific information to different locations.
	 */
	public function __construct()
	{
		
		$this->m_dirKey = "something unique?";
		
		// We need the directory to store files
		// and the url which exposes this
		$this->m_rootUrl = RingsideApiConfig::$upload_rootUrl;
		$this->m_rootDir = RingsideApiConfig::$upload_rootDir;
		
		$this->m_rootDir = str_replace ( '/', DIRECTORY_SEPARATOR, $this->m_rootDir );
		$this->m_rootDir = str_replace ( '\\', DIRECTORY_SEPARATOR, $this->m_rootDir );
	
	}
	
	public function isCached($scope_id, $key)
	{
		return file_exists ( $this->getFilename ( $scope_id, $key ) );
	}
	
	public function setByContent($scope_id, $key, $value)
	{
		$ext = substr ( $key, strrpos ( $key, '.' ), strlen ( $key ) - 1 ); // Get the extension from the filename.
		$result = $this->mkdir_recursive ( $this->getPathname ( $scope_id ) );
		if ($result === false)
		{
			error_log ( "Failed to create directory structure ($scope_id)" );
			return false;
		}
		return file_put_contents ( $this->getFilename ( $scope_id, $key, $ext ), $value );
	}
	
	public function setByUpload($upload, $scope_id, $key)
	{
		$ext = substr ( $key, strrpos ( $key, '.' ), strlen ( $key ) - 1 ); // Get the extension from the filename.
		$result = $this->mkdir_recursive ( $this->getPathname ( $scope_id ) );
		if ($result === false)
		{
			error_log ( "Failed to create directory structure ($scope_id)" );
			return false;
		}
		return copy ( $upload, $this->getFilename ( $scope_id, $key, $ext ) );
	}
	
	public function setByReference($scope_id, $reference, $type = null)
	{
		
		// TODO make sure valid content
		try
		{
			$contents = CacheUtils::fetch ( $reference );
			
			if ($contents === false)
			{
				return false;
			} else if ($contents ['http_code'] != '200')
			{
				return false;
			} else if ($type != null && ! in_array ( $contents ['content_type'], $type ))
			{
				return false;
			} else
			{
				$result = file_put_contents ( $this->getFilename ( $scope_id, $key, ".unknown" ), $contents );
				return $result;
			}
		} catch ( Exception $exception )
		{
			return false;
		}
	}
	
	public function getReference($scope_id, $key)
	{
		$ext = substr ( $key, strrpos ( $key, '.' ), strlen ( $key ) - 1 ); // Get the extension from the filename.
		return $this->m_rootUrl . "/" . $this->makeKey ( $scope_id, $this->m_dirKey ) . "/" . $this->makeKey ( $scope_id, $key ) . $ext;
	
	}
	
	public function getContent($scope_id, $key)
	{
		try
		{
			$contents = file_get_contents ( $this->getFilename ( $scope_id, $key ) );
			
			if ($contents === false)
			{
				return false;
			} else
			{
				return $contents;
			}
		} catch ( Exception $exception )
		{
			return false;
		}
	}
	
	/**
	 * Delete a content resources from the system.
	 *
	 * @param unknown_type $scope_id
	 * @param unknown_type $key
	 * @return unknown
	 */
	public function clear($scope_id, $key)
	{
		$result = unlink ( $this->getFilename ( $scope_id, $key ) );
		if ($result === false)
		{
			error_log ( "DELETE FAILED ($scope_id) ($key) " );
		}
		
		return $result;
	}
	
	/**
	 * Key maker
	 * TODO add some salt.
	 * 
	 * @param string $scope_id
	 * @param string $key
	 * @return some hash
	 */
	private function makeKey($scope_id, $key)
	{
		return md5 ( $scope_id . '-' . $key );
	}
	
	/**
	 * Get the path dir for this scope.
	 *
	 * @param unknown_type $scope_id
	 * @return unknown
	 */
	private function getPathname($scope_id)
	{
		$keyDir = $this->makeKey ( $scope_id, $this->m_dirKey );
		return $this->m_rootDir . DIRECTORY_SEPARATOR . $keyDir;
	}
	
	/**
	 * Get full path/file name.
	 * TODO add some salt.
	 *
	 * @param unknown_type $scope_id
	 * @param unknown_type $key
	 * @return unknown
	 */
	private function getFilename($scope_id, $key, $ext)
	{
		$keyFile = $this->makeKey ( $scope_id, $key );
		$file = $this->getPathname ( $scope_id ) . DIRECTORY_SEPARATOR . $keyFile;
		$file .= $ext;
		return $file;
	}
	
	/**
	 * Make the directory tree required.
	 *
	 * @param string $pathname
	 * @return boolean true/false
	 */
	private function mkdir_recursive($pathname)
	{
		is_dir ( dirname ( $pathname ) ) || $this->mkdir_recursive ( dirname ( $pathname ) );
		return is_dir ( $pathname ) || @mkdir ( $pathname );
	}
}

?>
