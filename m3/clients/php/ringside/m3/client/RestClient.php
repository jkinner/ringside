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

require_once 'ringside/m3/client/AbstractRestClient.php';

/**
 * A REST client that can be used to access M3 APIs hosted within a Ringside server.
 *
 * @author John Mazzitelli
 */
class M3_Client_RestClient extends M3_Client_AbstractRestClient
{
    /**
     * Builds the client, with an optional array of configuration settings.
     *
     * @param $serverAddr the endpoint of the server that this client will talk to
     * @param $secret the secret key used to sign the requests
     * @param $configArray array of configuration settings that control the behavior of the client
     */
    public function __construct($serverAddr, $secret, $configArray = null)
    {
        parent::__construct($serverAddr, $secret, $configArray);
    }

    // ----- INVENTORY -------------------------------------------------------

    /**
     * Returns '1' very fast as a way to ping the server to ensure it is
     * running.  This is normally not a useful API for clients local
     * inside the Ringside Server.  Remote management clients will normally
     * be the users of this API.
     *
     * @return string '1'
     */
    public function inventoryPing()
    {
        $_results = parent::callRestMethod('m3.inventory.ping');
        return $_results;
    }

    /**
     * Returns a multi-dimensional associative array containing version information
     * on Ringside components.
     *
     * @return array all version info
     */
    public function inventoryGetVersionInfo()
    {
        $_results = parent::callRestMethod('m3.inventory.getVersionInfo');
        return $_results;
    }

    /**
     * Returns an array containing the full dot-notation names of all deployed APIs
     * and their file names.
     * The API data are grouped by their namespaces. The returned array is associative
     * with the keys being the namespaces and values being list arrays. The list arrays
     * contain associative arrays consisting of api_name and pathname keys.
     *
     * @return array all APIs deployed in the server
     */
    public function inventoryGetApis()
    {
        $_response = parent::callRestMethod('m3.inventory.getApis');
        $_result = array();

        foreach ($_response as $_apis)
        {
                $_apiName = $_apis['api_name'];
                $_pathname = $_apis['pathname'];
    
                $_apiNamespaceAndTheRest = explode('.', $_apiName, 2);
                $_apiNamespace = $_apiNamespaceAndTheRest[0];
                if (!isset($_result[$_apiNamespace]))
                {
                    $_result[$_apiNamespace] = array();
                }
                $_result[$_apiNamespace][] = array('api_name' => $_apiName, 'pathname' => $_pathname);            
        }

        return $_result;
    }

    /**
     * Returns an array containing information on a deployed API.
     *
     * @param $apiName the dot-notation name of the API whose information is to be returned
     *
     * @return array a list of information on a deployed API
     */
    public function inventoryGetApi($apiName)
    {
        $_params = array('apiName' => $apiName);
        $_apps = parent::callRestMethod('m3.inventory.getApi', $_params);
        return $_apps;
    }

    /**
     * Returns an array containing information on all deployed applications.
     *
     * @return array a list of information on all deployed applications
     */
    public function inventoryGetApplications()
    {
        $_apps = parent::callRestMethod('m3.inventory.getApplications');
        return $_apps;
    }

    /**
     * Returns an array containing information on a deployed application.
     *
     * @param $appId the app ID whose information is to be returned
     *
     * @return array a list of information on a deployed application
     */
    public function inventoryGetApplication($appId)
    {
        $_params = array('appId' => $appId);
        $_apps = parent::callRestMethod('m3.inventory.getApplication', $_params);
        return $_apps;
    }

    /**
     * Returns an array containing information on all known networks.
     *
     * @return array a list of information on all known networks
     */
    public function inventoryGetNetworks()
    {
        $_networks = parent::callRestMethod('m3.inventory.getNetworks');
        return $_networks;
    }

    /**
     * Returns an array containing information on a known network.
     *
     * @param $networkId the network ID of the network whose information is to be returned
     *
     * @return array a list of information on a known network
     */
    public function inventoryGetNetwork($networkId)
    {
        $_params = array('networkId' => $networkId);
        $_networks = parent::callRestMethod('m3.inventory.getNetwork', $_params);
        return $_networks;
    }


    /**
     * Returns an array containing information on all known tags.
     *
     * @return array a list of information on all known tags
     */
    public function inventoryGetTags()
    {
        $_tags = parent::callRestMethod('m3.inventory.getTags');
        return $_tags;
    }

    /**
     * Returns an array containing information on a known tag.
     *
     * @param $tagNamespace the namespace of the named tag whose information is to be returned
     * @param $tagName the name of the tag whose information is to be returned
     *
     * @return array a list of information on a known network
     */
    public function inventoryGetTag($tagNamespace, $tagName)
    {
        $_params = array('tagNamespace' => $tagNamespace, 'tagName' => $tagName);
        $_tags = parent::callRestMethod('m3.inventory.getTag', $_params);
        return $_tags;
    }

    // ----- METRICS ---------------------------------------------------------

    /**
     * Returns user statistics.
     *
     * @return array aggregated user stats
     */
    public function metricsGetAggregatedUserStatistics()
    {
        $_callResponse = parent::callRestMethod('m3.metrics.getAggregatedUserStatistics');
        return $_callResponse;
    }

    /**
     * Returns a multi-dimenational array containing the APIs that we have metrics for
     * whose values are arrays consisting of count/min/max/average data for each API.
     *
     * @return array all APIs deployed in the server
     */
    public function metricsGetApiDurations()
    {
        $_callResponse = parent::callRestMethod('m3.metrics.getApiDurations');

        // I do not want a dependency on M3 core classes. I could have called:
        //   $_results = M3_Util_Stats::unflattenArray($_results);
        // but instead I will do it myself

        $_results = array();
        
        foreach ($_callResponse as $_stats)
        {
            $_results[$_stats['key']] = array('count' => &$_stats['count'],
                                              'min'   => &$_stats['min'],
                                              'max'   => &$_stats['max'],
                                              'avg'   => &$_stats['avg']);
        }
        
        return $_results;
    }

    /**
     * Purges the API duration data stored in the server. After this completes,
     * the
     *
     * @return true if the deletion was successful; false otherwise
     */
    public function metricsPurgeApiDurations()
    {
        $_results = parent::callRestMethod('m3.metrics.purgeApiDurations');
        return $_results;
    }

    // ----- CONFIG ----------------------------------------------------------

    /**
     * Obtains the configuration settings for the local server.
     * The list returned is an array of name/value pair arrays.
     * If you want a flat associative array where the keys are the
     * names of the config settings and the values are the config
     * setting values, use {@link configGetLocalSettingsFlattened}.
     * 
     * @return array associative array of config settings
     */
    public function configGetLocalSettings()
    {
        $_results = parent::callRestMethod('m3.config.getLocalSettings');
        return $_results;
    }

    /**
     * Obtains the configuration settings for the local server
     * as a flat associative array. The difference between this
     * and {@link configGetLocalSettings} is the layout of the
     * returned array.  This method returns a flat associative
     * array where the keys are the config setting names and their
     * values are the values of the config settings.
     *
     * @return array associative array of config settings
     */
    public function configGetLocalSettingsFlattened()
    {
        $_results = $this->configGetLocalSettings();
        $_flattened = array();

        foreach ($_results as $_item => $_nvPair)
        {
            $_flattened[$_nvPair['name']] = $_nvPair['value'];
        }
        
        return $_flattened;
    }

    /**
     * Sets the configuration settings for the local server.
     * You must pass in the full set of configuration items, whether or
     * not you are changing the values from their currently existing ones.
     * This means you should get the full, flattened config array from
     * {@link configGetLocalSettingsFlattened()} first, then change the
     * values you want to change in that array and pass the
     * flattened array to this method. It will be assumed that any
     * settings that do not exist in $newSettings that exist in the current
     * configuration are to be set to null (i.e "unset").
     *  
     * $param $newSettings the new configuration for the local server
     *
     * @return returns true on success, an exception is thrown on error
     */
    public function configSetLocalSettings(array $newSettings)
    {
         // NOTE: bad things happen if we have a setting that is the same as one of our
         //       protocol params (like "v" or "sig"). Right now we don't, but we need
         //       to make sure we keep it that way.
        $_params = $newSettings;
        
        // its possible some config settings are arrays (e.g. GLOBAL['facebook']['debug']=0).
        // That would convert to a real array in $_POST on the server, which we do not want.
        // Do get this to be sent as-is, convert all [ chars to _m3lsqb_
        // (as per phpdoc of the Ringside REST API implementation class)
        $_escapedLeftBrackets = array();
        foreach ($_params as $_k => &$_v)
        {
            $_escapedLeftBrackets[str_replace("[", "_m3lsqb_", $_k)] = $_v;
        }
                
        $_results = parent::callRestMethod('m3.config.setLocalSettings', $_escapedLeftBrackets);
        return $_results;
    }

    /**
     * Obtains the configuration settings found in php.ini.
     * The list returned is an array of name/value pair arrays.
     *
     * @return array associative array of php.ini settings, or false on error
     */
    public function configGetPhpIniSettings()
    {
        $_results = parent::callRestMethod('m3.config.getPhpIniSettings');
        return $_results;
    }

    // ----- OPERATIONS ------------------------------------------------------

    /**
     * Given arbitrary PHP code, evaluates that code and returns its results.
     * WARNING! This is a very powerful and potentially dangerous method.
     * Use with care.
     * 
     * @param $phpCode the code that is to be evaluated
     * 
     * @return string the results of the evaluation
     */
    public function operationEvaluatePhpCode($phpCode)
    {
        $_params = array('phpCode' => $phpCode);
        $_results = parent::callRestMethod('m3.operation.evaluatePhpCode', $_params);
        return $_results;        
    }

    /**
     * Returns the actual content of the given file.  The pathname is
     * relative to the install directory unless the '$useIncludePath'
     * parameter is set to 'true'.
     * 
     * @param $pathname the file whose content is to be returned
     * @param $useIncludePath if 'true', the file should be found in include path
     * 
     * @return string the file's content
     */
    public function operationGetFileContent($pathname, $useIncludePath = null)
    {
        $_params = array('pathname' => $pathname, 'useIncludePath' => $useIncludePath);
        $_results = parent::callRestMethod('m3.operation.getFileContent', $_params);
        $_results = base64_decode($_results);
        require_once 'ringside/m3/client/gzdecode.php';
        $_results = M3_Client_gzdecode::gzdecode($_results);
        
        return $_results;        
    }

}

?>
