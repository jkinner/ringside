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

require_once('BaseM3ApiTestCase.php');
require_once('m3/rest/ConfigSetLocalSettings.php');
require_once('ringside/m3/util/PhpSettings.php');

class ConfigSetLocalSettingsTestCase extends BaseM3ApiTestCase
{
    public function testSetLocalSettings()
    {
        // get the config
        $_apiCall = $this->initRest( new ConfigGetLocalSettings(), null);
        $_results = $_apiCall->execute();
        $this->assertTrue(is_array($_results));
        $this->assertTrue(is_array($_results['local_setting']));
        $_results = $_results['local_setting'];

        // pick a setting to change - make sure its numeric because we add +1 to it later
        $_settingNameToChange = 'm3MaxSizeAllowedOperationGetFileContent';
        $this->assertTrue($this->hasName($_results, $_settingNameToChange));   
        $_originalValue = $this->getNamedValue($_results, $_settingNameToChange);
        $_flattened = $this->flattenArray($_results);
        
        // we now have $_flattened the current settings, and the current, $_originalValue of one of them
        $_flattened[$_settingNameToChange] = $_originalValue + 1;
        $_apiCall = $this->initRest( new ConfigSetLocalSettings(), $_flattened);
        $_results = $_apiCall->execute();
        $this->assertTrue($_results == true);

        // get the config again and make sure it changed
        $_apiCall = $this->initRest( new ConfigGetLocalSettings(), null);
        $_results = $_apiCall->execute();
        $this->assertTrue(is_array($_results));
        $this->assertTrue(is_array($_results['local_setting']));
        $_results = $_results['local_setting'];
        $this->assertTrue($this->hasName($_results, $_settingNameToChange));
        $_newValue = $this->getNamedValue($_results, $_settingNameToChange);
        $this->assertTrue($_newValue == $_originalValue + 1);
        
        // put it back to the way it was
        $_flattened[$_settingNameToChange] = $_originalValue;
        $_apiCall = $this->initRest( new ConfigSetLocalSettings(), $_flattened);
        $_results = $_apiCall->execute();
        $this->assertTrue($_results == true);
    }
    
    private function hasName($haystack, $needle)
    {
        foreach ($haystack as $_nv)
        {
        	if ($_nv['name'] === $needle)
        	{
        	    return true;
        	}
        }
        
        return false;
    }

    private function getNamedValue($haystack, $needle)
    {
        foreach ($haystack as $_nv)
        {
        	if ($_nv['name'] === $needle)
        	{
        	    return $_nv['value'];
        	}
        }
        
        return null;
    }
    
    private function flattenArray($arr)
    {
        $_flattened = array();
        foreach ($arr as $_nv)
        {
        	$_flattened[$_nv['name']] = $_nv['value'];
        }
        return $_flattened;
    }
}
?>