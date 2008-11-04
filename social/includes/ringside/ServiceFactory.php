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
 * Document this file.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
abstract class ServiceFactory
{
	/** The configuration for creating services, expected in parse_ini_file format with one section per service. */
	public static $config = array();
	
	private static $instances = array();
	
	public static function buildClassFileName($className)
	{
		$class_file = str_replace('_', DIRECTORY_SEPARATOR, $className);
		$end_of_dirname = strrpos($class_file, DIRECTORY_SEPARATOR);
		return strtolower(substr($class_file, 0, $end_of_dirname)).substr($class_file,$end_of_dirname).'.php';
	}

	public static function loadClass($className)
	{
		if ( ! class_exists($className) )
		{
			// Try it the "Ringside" way first...
			$class_file = self::buildClassFileName($className);
			$ringside_class_file_name = 'ringside'.DIRECTORY_SEPARATOR.$class_file;
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
	 * @param string $mapperClass the class to instantiate; overrides the current configuration.
	 * @return object the service instance
	 */
	public static function create($serviceInterfaceName, $serviceDefaultImplName, $implClass = null)
	{
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
				error_log("Storing instance for $serviceInterfaceName");
				self::$instances[$serviceInterfaceName] = $instance;
			}
		}
			
		return $instance;
	}	
}
?>