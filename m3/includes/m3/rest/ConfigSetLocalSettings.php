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
require_once 'ringside/m3/util/PhpSettings.php';

/**
 * M3 API that allows the caller to change the configuration settings for the local server.
 *
 * Special note to clients that want to call this method:
 * Due to the nature of PHP and the way it interprets POSTs data and stores it in $_POST,
 * you must ensure any keys to any parameters that have left brackets in them must have
 * their left brackets converted to the string "_m3lsqb_".
 * 
 * @author John Mazzitelli
 */
class ConfigSetLocalSettings extends M3_AbstractRest
{
    private $newSettings;

    public function validateRequest()
    {
        $_rawParams = $this->getApiParams();

        $_unescapedLeftBrackets = array();
        foreach ( $_rawParams as $_k => &$_v)
        {
            $_unescapedLeftBrackets[str_replace("_m3lsqb_", "[", $_k)] = $_v;
        }

        $this->newSettings = $_unescapedLeftBrackets;
    }
    
    /**
     * Puts the new settings in the local server's configuration.
     * Returns true on success; an exception is thrown on error.
     */
    public function execute()
    {
        $_settings = new M3_Util_PhpSettings();
        $_results = array();
        if ($_settings->readPhpFile($_results, false))
        {
            // overlay new settings that exist in the current configuration
            foreach (array_intersect_key($_results, $this->newSettings) as $_existingKey => $_existingValue)
            {
                $_results[$_existingKey] = $this->newSettings[$_existingKey];
            }
            
            // add new settings that don't yet exist in the current config (the existing variable might have been commented out)
            foreach (array_diff_key($this->newSettings, $_results) as $_newKey => $_newValue)
            {
                $_results[$_newKey] = $_newValue;
            }
            
            // remove existing settings that are no longer being set (we unset them by setting them to null)
            foreach (array_diff_key($_results, $this->newSettings) as $_oldKey => $_oldValue)
            {
                if (!($this->beginsWith($_oldKey, M3_Util_PhpSettings::UNPARSED)
                      || $this->beginsWith($_oldKey, M3_Util_PhpSettings::NEWLINE)))
                {
                   $_results[$_oldKey] = "null";
                }
            }

            $_writeResults = $_settings->writePhpFile($_results);
            if ($_writeResults)
            {
                return true;
            }
            else
            {
                throw new OpenFBAPIException("Failed to write the new settings", -2);                
            }
        }
        else
        {
            throw new OpenFBAPIException("Failed to read the settings file", -1);
        }
    }

    private function beginsWith( $str, $sub )
    {
        return substr($str, 0, strlen($sub)) === $sub;
    }    
}
?>