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

require_once 'ringside/m3/util/PhpSettings.php';

class M3PhpClientConfigTestCase extends BaseM3PhpClientTestCase
{
    public function testGetLocalSettings()
    {
        $_c = $this->getClient();
        $_results = $_c->configGetLocalSettings();
        $this->assertTrue(is_array($_results));
        $this->assertArrayHasKey('name', $_results[0]);
        $this->assertArrayHasKey('value', $_results[0]);
        $this->assertGreaterThan(0, count($_results));
        $this->assertTrue($this->hasName($_results, 'webUrl'));
        $this->assertFalse($this->hasName($_results, M3_Util_PhpSettings::UNPARSED.'0'));
        $this->assertFalse($this->hasName($_results, M3_Util_PhpSettings::NEWLINE.'0'));
    }

    public function testSetLocalSettings()
    {
        // get the config
        $_c = $this->getClient();
        $_results = $_c->configGetLocalSettingsFlattened();
        $this->assertTrue(is_array($_results));

        // pick a setting to change - make sure its numeric because we add +1 to it later
        $_settingNameToChange = 'm3MaxSizeAllowedOperationGetFileContent';
        $this->assertTrue(isset($_results[$_settingNameToChange]));   
        $_originalValue = $_results[$_settingNameToChange];
        
        // we now have the current settings, and the current, $_originalValue of one of them
        $_results[$_settingNameToChange] = $_originalValue + 1;
        $_results = $_c->configSetLocalSettings($_results);
        $this->assertTrue($_results == true);

        // get the config again and make sure it changed
        $_results = $_c->configGetLocalSettingsFlattened();
        $this->assertTrue(is_array($_results));
        $this->assertTrue(isset($_results[$_settingNameToChange]));   
        $_newValue = $_results[$_settingNameToChange];
        $this->assertTrue($_newValue == $_originalValue + 1);
        
        // put it back to the way it was
        $_results[$_settingNameToChange] = $_originalValue;
        $_results = $_c->configSetLocalSettings($_results);
        $this->assertTrue($_results == true);
    }

    public function testGetPhpIniSettings()
    {
        $_c = $this->getClient();
        $_results = $_c->configGetPhpIniSettings();
        $this->assertTrue(is_array($_results));
        $this->assertArrayHasKey('name', $_results[0]);
        $this->assertArrayHasKey('value', $_results[0]);
        $this->assertGreaterThan(0, count($_results));
        $this->assertTrue($this->hasName($_results, 'log_errors'));
        $this->assertFalse($this->hasName($_results, M3_Util_PhpSettings::UNPARSED.'0'));
        $this->assertFalse($this->hasName($_results, M3_Util_PhpSettings::NEWLINE.'0'));
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
}

?>