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

/**
 * Enter description here...
 *
 */
class RingsideApiConfig
{
    public static $db_username = '';
    public static $db_password = '';
    public static $db_server = '';
    public static $db_name = 'ringfb';
    public static $db_type = 'mysql';

    /**
     * Indicates whether an already-authenticated Facebook session should be used to authenticate an out-of-session Ringside connection.
     * Support is currently very limited. A Facebook client request must already have been made to Facebook in order to establish the session.
     */
    public static $use_facebook_trust = false;

    /**
     * Cache information used for persisting photo's, pic's and application images.
     */
    public static $upload_rootUrl = '';
    public static $upload_rootDir = '';
}

class RingsideApiServer {
    private static $LOAD_PATH = array('ringside/api', 'ringside');

    static function autoload($className) {
        if ( class_exists($className, false) || interface_exists($className, false)) {
            return false;
        }

        $parts = split('_', $className);

        $fileName = str_replace('_', DIRECTORY_SEPARATOR, $className).'.php';

        if ( count($parts) > 1 ) {
            $pkg_parts = array_slice($parts, 0, count($parts) - 1, true);
            // Ringside uses lower-case for package directory names and camel case for class names.
            $pkg_parts = strtolower(join(DIRECTORY_SEPARATOR, $pkg_parts));
            $fileName = $pkg_parts.DIRECTORY_SEPARATOR.$parts[count($parts)-1].'.php';
        }

        foreach ( self::$LOAD_PATH as $loadDirectory ) {
            $tryFile = ($loadDirectory?$loadDirectory.DIRECTORY_SEPARATOR:'').$fileName;
            if ( @include($tryFile) ) {
                if ( class_exists($className) ) {
//                    error_log("Successfully loaded $className from $tryFile");
                    return true;
                }

                error_log("Warning: Loaded file $fileName, but no class $className was defined.");
            }
        }

        return false;
    }
}

{
    if((include 'LocalSettings.php') === false)
    {
        error_log("Warning: No LocalSettings.php in include_path; default settings will be used for Ringside API container");
    }else
    {
        RingsideApiConfig::$db_username = $db_username;
        RingsideApiConfig::$db_password = $db_password;
        RingsideApiConfig::$db_server = $db_server;
        RingsideApiConfig::$db_name = $db_name;
        RingsideApiConfig::$db_type = $db_type;
        RingsideApiConfig::$use_facebook_trust = $trust_facebook;
        RingsideApiConfig::$upload_rootDir = $uploadRootDir;
        RingsideApiConfig::$upload_rootUrl = $uploadRootUrl;
    }

    ### Setup your doctrine connection
    require_once ('Doctrine/lib/Doctrine.php');
    spl_autoload_register(array('Doctrine', 'autoload'));
    //Doctrine::loadModels('ringside/api/dao/records/');
    $dsn = "$db_type://$db_username:$db_password@$db_server/$db_name";

    $manager = Doctrine_Manager::getInstance();

    // Set up validation FIXME: this really breaks out stuff, not good
    //$manager->setAttribute(Doctrine::ATTR_VALIDATE, Doctrine::VALIDATE_ALL);

    // Set up caching
    if(isset($useDbCache) && $useDbCache === true)
    {
        $sqlite_conn = Doctrine_Manager::connection(new PDO('sqlite:memory'));
        $cacheDriver = new Doctrine_Cache_Db(array('connection' => $sqlite_conn, 'tableName' => 'ringside_api'));

        try
        {
            $cacheDriver->createTable();
        }catch(Doctrine_Connection_Exception $e)
        {
            if($e->getPortableCode() !== Doctrine::ERR_ALREADY_EXISTS)
            {
                $cacheDriver = null;
            }
        }

        if(null !== $cacheDriver)
        {
            $manager->setAttribute(Doctrine::ATTR_QUERY_CACHE, $cacheDriver);
            $manager->setAttribute(Doctrine::ATTR_RESULT_CACHE, $cacheDriver);
            // Result cache set for 5 minutes - FIXME: this causes php to crash.  Riddle me that!
            //$manager->setAttribute(Doctrine::ATTR_RESULT_CACHE_LIFESPAN, 300);
            //Doctrine::ATTR_RESULT_CACHE_LIFESPAN;
            //Doctrine::ATTR_QUERY_CACHE_LIFESPAN
        }
    }

    $conn = Doctrine_Manager::connection($dsn);

    require_once ('ringside/m3/util/Settings.php');
    if(M3_Util_Settings::getDbProfilerEnable())
    {
        require_once ('ringside/m3/metric/DoctrineProfiler.php');
        $profiler = new M3_Metric_DoctrineProfiler();
        $conn->setListener($profiler);
    }

    $conn->setAttribute('portability', Doctrine::PORTABILITY_ALL);
    
    spl_autoload_register(array('RingsideApiServer', 'autoload'));
    
}
?>