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

require_once 'ringside/m3/AbstractRest.php';
require_once 'ringside/m3/util/Settings.php';

/**
 * M3 API that returns an inventory of all known APIs deployed inside the Ringside server.
 *
 * @author John Mazzitelli
 */
class InventoryGetApis extends M3_AbstractRest
{
    private $restLocations;

    /**
     * Returns an array containing a flat list of dot-notation names of all deployed APIs.
     *
     * @return array all APIs deployed in the server. The returned array is a list of
     *         associative arrays with each associative array list item including information
     *         about each deployed API
     */
    public function execute()
    {
        // re-organize the array into a flat list of name/filename pairs. we do this because the
        // grouped array would produce an XML response that isn't easily defined via XML Schema
        $_all = $this->getAllApiNames();
        
        $_list = array();
        foreach ($_all as $_namespace => $_namespaceApis)
        {
           foreach ($_namespaceApis as $_apiName => $_file)
           {
               $_list[] = array("api_name" => $_apiName, "pathname" => $_file);
           }
        }
        
        return array('api' => $_list);
    }

    /**
     * Returns an array containing the full dot-notation names of all deployed APIs
     * and their php implementation filenames.
     * The API names are grouped by their namespaces in the returned multi-dimensional array.
     *
     * @return array all APIs deployed in the server. The returned array is associative with
     *         the key being the namespace name and value being another associative array
     *         keyed on API name whose value is the filename of the API implementation php file. 
     */
    private function getAllApiNames()
    {
        $_apiDirectories = M3_Util_Settings::getDeployedApiLocations();

        foreach ($_apiDirectories as $_apiDirectory)
        {
            $_apiNamespace = str_replace("/", ".", ltrim($_apiDirectory, '/'));
            if (substr($_apiNamespace, -5) == ".rest")
            {
                $_apiNamespace = substr($_apiNamespace, 0, -5);
            }
            else if ($_apiNamespace="ringside.api.facebook")
            {
                $_apiNamespace = "facebook";
            }
            
            $_apis[$_apiNamespace] = $this->getApiNamesInDirectory($_apiNamespace, $_apiDirectory);
        }

        return $_apis;
    }

    /**
     * Returns the APIs that are currently deployed in the given rest directory name.
     * The returned names will be the full REST API name that REST clients can use
     * to invoke the APIs.  This includes the three-parts to the REST API:
     * <namespace>.<package>.<class>; e.g. "m3.inventory.getApis"
     *
     * @param $apiNamespace the namespace of the APIs found in the given directory
     * @param $restDir the directory name where the API classes can be found
     *
     * @return array names of all APIs deployed in the server in the given directory
     *         the array is associative, where the key is the API name and the value
     *         is the php file that implements that API
     */
    private function getApiNamesInDirectory($apiNamespace, $restDir)
    {
        $_rootDirs = M3_Util_Settings::getRestLocations();

        $_names = array();

        foreach($_rootDirs as $_dir)
        {
            $_fullApiDirectory = $_dir . $restDir;

            if ( file_exists($_fullApiDirectory))
            {
                $_files = scandir( $_fullApiDirectory );
                foreach($_files as $_file)
                {
                    if(!is_dir($_file))
                    {
                        $_apiName = $this->determineApiName($_file);
                        if (!empty($_apiName))
                        {
                            $_name = $apiNamespace . '.' . $_apiName;
                            if (!$this->isIgnoredName($_name))
                            {
                                $_names[$_name] = $_fullApiDirectory . '/' . $_file;
                            }
                        }
                    }
                }
            }
        }

        return $_names;
    }

    /**
     * There are certain names that we know are deployed in the server but should not be
     * considered APIs. We hardcode those names in this method. This will return true
     * if the name is to be ignored, false if the name is not to be ignored and should be
     * considered an API name. 
     */
    private function isIgnoredName($fullApiName)
    {
        // TODO: we could make this list configurable in case we need this list to be user controlled, but no need for that now
        $_ignoreList = array('facebook.open.fBConstants'); // add more as we need
        return !(array_search($fullApiName, $_ignoreList) === false);
    }

    /**
     * Given a filename, this will return the name of the API call it implements
     * or null if the filename does not correspond to a REST API.
     * 
     * The REST API name that is returned will have two parts. The first
     * is the all-lowercase "package" and the second is the camel-cased "class"
     * (though these names probably aren't the proper things to call them).
     * For example, this API "InventoryGetApis" will have an API name of
     * "inventory.getApis". For a REST client to be able to invoke the API,
     * the namespace must be appended to the returned API name (for example,
     * "m3." must be appended to this API like this: "m3.inventory.getApis").
     *
     * @param string $filename the name of the file (must not have any path info!)
     * @return string the REST API name, or null
     */
    private function determineApiName($filename)
    {
        // API names are based on their PHP file names
        if (substr($filename, -4) == ".php")
        {
            $_name = substr($filename, 0, strlen($filename) - 4);
            $_split = $this->explodeCamelCase($_name);
            $_first = array_shift($_split);
            $_first = strtolower($_first);
            $_split[0][0] = strtolower($_split[0][0]);
            $_second = implode('', $_split);
            $_name = $_first . '.' . $_second;
            return $_name;
        }

        return null;
    }

    /**
     * Splits the given string according to CamelCase. Uppercase characters are treated as the separator
     * but they are returned in their appropriate array elements.
     * 
     * Examples:
     * 
     * "HelloWorld" returns array('Hello', 'World')
     * "XYZCorp" returns array ('XYZ', 'Corp')
     * 
     * @param string $string the camel case string
     * @return array the camel case segments
     */
    private function explodeCamelCase($string)
    {
      // Split up the string into an array according to the uppercase characters
      $array = preg_split('/([A-Z][^A-Z]+)/', $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);      
      return $array;
    }
}
?>