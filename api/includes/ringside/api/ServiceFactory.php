<?php
 /*
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
  */

/**
 * This file is a duplicate of the one in the Social Module, which will
 * probably go away?
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
class Api_ServiceFactory
{
	/** The configuration for creating services, expected in parse_ini_file format with one section per service. */
	public static $config = array();
	
	private static $instances = array();
	
	public static function buildClassFileName($className)
	{
		if (strpos($className, '_') !== false) {
			$carr = explode('_', $className);
			$fname = '';
			for ($k = 0; $k < (count($carr)-1); $k++) {
				$fname .= strtolower($carr[$k]) . '/';
			}
			$fname .= 'internal/' . $carr[count($carr)-1] . '.php';
			return $fname;			
		} else {
			return "api/bo/internal/$className.php";
		}		
	}

	public static function loadClass($className)
	{
		if (!class_exists($className))
		{
			// Try it the "Ringside" way first...
			$class_file = self::buildClassFileName($className);
			$ringside_class_file_name = "ringside/$class_file";
			try 
			{
				if ( false === include_once($ringside_class_file_name) )
				{
					// If that doesn't work, try the raw class name way
					include_once($class_file);
				}
				
				if ( ! class_exists($className, false) )
				{
					error_log("File was loaded, but no class defined when loading service $className");
					throw new Exception("Failed to load class $className");
				}
			}
			catch ( Exception $e )
			{
				error_log("Exception when processing class definition for service $className");
				throw $e;
			}
		}
	}

	/**
	 * Creates the appropriate mapper service for the current environment. If no override class name is given,
	 * the result is stored and will be retrieved later when the same service is requested.
	 *
	 * @param string $implClass the class to instantiate; overrides the current configuration.
	 * @return object the service instance
	 */
	public static function create($serviceInterfaceName, $serviceDefaultImplName = null, $implClass = null)
	{
		if ($serviceDefaultImplName == null) $serviceDefaultImplName = "Api_Bo_{$serviceInterfaceName}Impl";
		$override = isset($implClass);
		$instance = isset(self::$instances[$serviceInterfaceName])?self::$instances[$serviceInterfaceName]:null;
		if ( ! isset($instance) || isset($implClass) )
		{
			if ( empty($implClass) ) {
				if ( isset(self::$config[$serviceInterfaceName]) )
				{
					$implClass = self::$config[$serviceInterfaceName]['impl'];
				}
				
				if ( empty($implClass) )
				{
					$implClass = $serviceDefaultImplName;
				}
			}
	
			self::loadClass($implClass);
			
			$instance = new $implClass();
			
			if ( false === $override ) {
				//error_log("Storing instance for $serviceInterfaceName");
				self::$instances[$serviceInterfaceName] = $instance;
			}
		}
			
		return $instance;
	}	
}
?>