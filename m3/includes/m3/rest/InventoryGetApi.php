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
require_once 'ringside/m3/util/File.php';

/**
 * M3 API that returns an inventory of a known API deployed inside the Ringside server.
 *
 * @author John Mazzitelli
 */
class InventoryGetApi extends M3_AbstractRest
{
    private $apiName;

    public function validateRequest()
    {
        $this->apiName = $this->getRequiredApiParam("apiName");
    }

    /**
     * Returns an array containing information on an API.
     *
     * @return array info on an API
     */
    public function execute()
    {

        $_apiNamePieces = explode('.', $this->apiName);
        $_apiNamespace = $_apiNamePieces[0];
        $_apiPkg = $_apiNamePieces[1];
        $_apiShortName = $_apiNamePieces[2];
        $_apiClassFile = ucfirst($_apiPkg) . ucfirst($_apiShortName) . '.php';

        // we need to scan the rest locations so we can get the full path info
        $_restLocations = M3_Util_Settings::getRestLocations();
        $_deployedApiLocations = M3_Util_Settings::getDeployedApiLocations();
        $_foundPathname = null;

        foreach ($_restLocations as $_restLocation)
        {
            foreach ($_deployedApiLocations as $_deployedApiLocation)
            {
                $_dir = M3_Util_File::buildPathName($_restLocation, $_deployedApiLocation);
                $_pathname = M3_Util_File::buildPathName($_dir, $_apiClassFile);
                if (file_exists($_pathname))
                {
                    $_foundPathname = $_pathname;
                    break;
                }
            }

            if (!is_null($_foundPathname))
            {
                break;
            }
        }

        $_info = array();
        
        if (!is_null($_foundPathname))
        {
            $_info['api_name'] = $this->apiName;
            $_info['pathname'] = $_foundPathname;
        }
        
        return array('api' => $_info);
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