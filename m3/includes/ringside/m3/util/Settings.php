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

require_once ('ringside/m3/util/File.php');
require_once ('ringside/m3/util/DatastoreEnum.php');
require_once ('ringside/m3/util/IniSettings.php');

/**
 * This class defines methods used to determine how
 * M3 is setup and configured.  For example, use this to determine if M3
 * should use the filesystem or database as its backing data store.
 *
 * @author John Mazzitelli
 */
class M3_Util_Settings
{
    // cache these since directory access is expensive
    private static $restLocations;

    /**
     * Returns the flag to indicate if we should enable the Doctrine database profiler.
     * 
     * @return boolean true if the database profiler should be turned off; false if not
     */
    public static function getDbProfilerEnable()
    {
        include 'LocalSettings.php';
        if (!isset($m3DbProfilerEnable) || empty($m3DbProfilerEnable))
        {
            $m3DbProfilerEnable = false;
        }
        return ($m3DbProfilerEnable === true || strtolower($m3DbProfilerEnable) === "true");
    }

    /**
     * Returns the maximum size of file allowed to be served by m3.operation.getFileContent REST API.
     * If this returns 0, it effectively disables that REST API.
     * 
     * @return int maximum file size that can be served by the getFileContent M3 REST API
     */
    public static function getMaxSizeAllowedOperationGetFileContent()
    {
        include 'LocalSettings.php';
        if (!isset($m3MaxSizeAllowedOperationGetFileContent) || empty($m3MaxSizeAllowedOperationGetFileContent))
        {
            $m3MaxSizeAllowedOperationGetFileContent = 1000000;
        }
        return $m3MaxSizeAllowedOperationGetFileContent;
    }

    /**
     * Returns the flag that enables the m3.operation.evaluatePhpCode REST API.
     * 
     * @return boolean true if M3 allows for remote evals, false if its disallowed
     */
    public static function getAllowOperationEvaluatePhpCode()
    {
        include 'LocalSettings.php';
        if (!isset($m3AllowOperationEvaluatePhpCode) || empty($m3AllowOperationEvaluatePhpCode))
        {
            $m3AllowOperationEvaluatePhpCode = true;
        }
        return ($m3AllowOperationEvaluatePhpCode === true || strtolower($m3AllowOperationEvaluatePhpCode) === "true");    
    }

    /**
     * Returns the URL of the REST server. This URL can be used to access the
     * M3 REST APIs.
     * 
     * @return string URL endpoint of the REST server
     */
    public static function getRestServerUrl()
    {
        include 'LocalSettings.php';
        if (!isset($serverUrl) || empty($serverUrl))
        {
            $serverUrl = 'http://127.0.0.1/ringside/restserver.php';
        }
        return $serverUrl;
    }

    /**
     * Return the M3 secret key that all M3 requests must be signed with.
     *
     * @return string M3 secret key
     */
    public static function getM3SecretKey()
    {
        include 'LocalSettings.php';
        if (!isset($m3SecretKey) || empty($m3SecretKey))
        {
            // TODO: config did not set the M3 secret key - should we use the social key as the default?
            $m3SecretKey = 'r1ngs1d3';
        }
        return $m3SecretKey;
    }

    /**
     * Returns an array of directory locations where deployed REST APIs can be found.
     * These directories should be found under the REST locations found via
     * {@link getRestLocations()}.
     *
     * @return array directory names
     */
    public static function getDeployedApiLocations()
    {
        include 'LocalSettings.php';
        if (!isset($m3DeployedApiLocations) || empty($m3DeployedApiLocations))
        {
            // user didn't specify, our default is our known set of API locations 
            $m3DeployedApiLocations = '/ringside/rest,/m3/rest,/ringside/api/facebook';
        }

        $_dirs = explode(',', $m3DeployedApiLocations);
        return $_dirs;
    }

    /**
     * Return array of locations where REST APIs can be located.
     *
     * These are directories that deployed API locations can be rooted at.
     * In other words, get these rest locations first, then under these directories,
     * you should be able to find deployed REST APIs in the subdirectories
     * specified by {@link getDeployedApiLocations()}. 
     *
     * @return array of directory locations where REST APIs can be found
     */
    public static function getRestLocations()
    {
        if (!empty(self::$restLocations))
        {
            return self::$restLocations;
        }

        $_restLocations = array();

        $_includePaths = explode(PATH_SEPARATOR, get_include_path() );
        foreach ($_includePaths as $_includePath)
        {
            $_includePath = realpath(rtrim($_includePath, '\\/'));
            if ($_includePath)
            {
                $_restLocations[] = $_includePath;
            }
        }

        self::$restLocations = array_unique($_restLocations); // if include_path has any dups, remove them
        return self::$restLocations;
    }
    
    /**
     * Returns the datastore type that M3 should use which determines how and where
     * its data is persisted.
     *
     * @return M3_Util_DatastoreEnum datastore type
     */
    public static function getDatastore()
    {
        include 'LocalSettings.php';
        if (!isset($m3Datastore) || empty($m3Datastore))
        {
            // user didn't specify, our default is the DB
            $_ds = M3_Util_DatastoreEnum::DB();
        }
        else
        {
            $_ds = M3_Util_DatastoreEnum::create($m3Datastore);
        }
        return $_ds;
    }

    /**
     * If the datastore used by M3 is the filesystem, this will be the directory
     * where the data is stored.  M3 may also decide to use this directory to store
     * other information that it wants to persist, as it sees fit.
     *
     * @return String a path to the directory where M3 can store data on the file system.
     */
    public static function getDataDirectory()
    {
        include 'LocalSettings.php';
        $_dir = '';

        if (isset($m3DataDirectory)) 
        {
           $_dir = trim($m3DataDirectory);
        }
        
        if (empty($_dir))
        {
            $_dir = M3_Util_File::getTempDir();
        }

        return $_dir;
    }

    /**
     * Returns the full pathname to the PHP error log file, if it can be found.
     * If the PHP error log is not defined, then the web server error log
     * (if it can be found) will be returned, since it will be assumed that is
     * where the PHP error logs will also be written to.
     * 
     * @return string path to the PHP error log, or false if its location is unknown
     */
    public static function getPhpErrorLogPathName()
    {
        $_errorLogPath = ini_get('error_log');
        if (!$_errorLogPath)
        {
            return self::getWebServerErrorLogPathName();
        }

        return $_errorLogPath;
    }

    /**
     * Returns the full pathname to the web server's error log file, if it can be found.
     * The web server error log file will be assumed to be located at either
     * "logs/error.log" or "logs/error_log" under the web server install directory.
     * 
     * @return string path to the error log, or false if its location is unknown
     * 
     * @see getWebServerInstallationDirectory()
     */
    public static function getWebServerErrorLogPathName()
    {
        $_webServerDir = self::getWebServerInstallationDirectory();
        if ($_webServerDir)
        {
            $_possibilities = array("$_webServerDir/logs/error.log",
                                    "$_webServerDir/logs/error_log");
            foreach ($_possibilities as $_possible)
            {
                if (file_exists($_possible))
                {
                    return $_possible;
                }
            }            
        }

        return false;
    }

    /**
     * The configuration file can optionally define where the web server
     * is installed.  This is typically used when the web server is Apache
     * and you want to find the logs and configuratin files. If not defined,
     * the web server config and log files might not be found.
     *
     * @return string a path to the directory where the web server is installed,
     *                or false if not known
     */
    public static function getWebServerInstallationDirectory()
    {
        include 'LocalSettings.php';
        $_dir = false;

        if (isset($m3WebServerInstallDir)) 
        {
           $_dir = trim($m3WebServerInstallDir);
        }
        
        return $_dir;
    }

    /**
     * Returns the path to the PHP configuration file (php.ini).
     * 
     * @return pathname of the php.ini file, or false if it cannot be determined
     */
    public static function getPhpIniFileLocation()
    {
        if (function_exists("php_ini_loaded_file"))
        {
            $_cfgfile = php_ini_loaded_file();
            if (!$_cfgfile)
            {
                error_log ("php.ini file is not loaded!");
                return false;
            }
        }
        else
        {
            ob_start();
            phpinfo();
            $_phpinfoFull = ob_get_contents();
            ob_end_clean();
            
            // Remove all <> tags from $phpinfo
            $_phpinfo = preg_replace('/<[^>]*>/', '', $_phpinfoFull);
            
            // Find the php.ini location
            preg_match('/Loaded Configuration File[ \t]*(=>[ \t]*)?([^ \t\n]*)/', $_phpinfo, $_matches);
            $_cfgfile = $_matches[2];
            if (!$_cfgfile)
            {
                error_log ("Unable to determine which configuration (php.ini) file is used!");
                return false;
            }
        }
        
        return $_cfgfile;        
    }

    /**
     * Returns an array consisting of the contents of the php.ini file.
     * Keep in mind that PHP ignores section headers - all settings are considered
     * to be in one global unnamed section. Because of this, you can ask for the returned
     * array to be "flat" (i.e. one-dimensional, removing the separation of
     * settings by section). Otherwise, you can get the data returned as
     * a multi-dimensional array - first dimension is the section
     * names and the second is the name/value pairs found in the section.
     * 
     * @param $flatten if true (the default) all data comes back in a flat array list without
     *                 any section information; if false, the returned value is multi-dimensional
     *                 with data separated in sections.
     * @return multi-dimensional array of php.ini data, false if it can't get the data
     */
    public static function getPhpIniSettings($flatten = true)
    {
        $_phpIniLocation = self::getPhpIniFileLocation();
        if (!$_phpIniLocation)
        {
            return false;
        }
    
        $_phpIni = new M3_Util_IniSettings($_phpIniLocation, false);
        if ($_phpIni->readIniFile($_phpIniSettings, true))
        {
            if ($flatten)
            {
                $_flattenedData = array();
                foreach ($_phpIniSettings as $_section => $_nameValues)
                {
                	$_flattenedData = array_merge($_flattenedData, $_nameValues);
                }
                return $_flattenedData;
            }
            else
            {
                return $_phpIniSettings;
            }
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Returns the installation directory of the Ringside Server itself.
     * 
     * @return string the pathname of the install directory
     */
    public static function getRingsideServerInstallDir()
    {
        // when in production, build.info file should always exist at the root install dir;
        // use build.info as our primary search file since we know we'll never have more than one.
        // if that isn't found, we must be in dev mode; let's hunt for the LocalSettings.php
        // which should also be in the root install dir. There may be more than one in other
        // directories, but I think we usually only have one at the root dir.  Because I'm
        // not sure if this will change in the future, that's why I key on build.info first.
        $_file = M3_Util_File::findFileInIncludePath('build.info');
        if (!$_file)
        {
            $_file = M3_Util_File::findFileInIncludePath('LocalSettings.php');
            if (!$_file)
            {
                throw Exception("Missing both build.info and LocalSettings.php! Can't find install dir");            
            }
        }
        
        $_installDir = dirname(realpath($_file));
        return $_installDir;
    }
}
?>