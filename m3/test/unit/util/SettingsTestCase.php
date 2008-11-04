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

require_once ('PHPUnit/Framework.php');
require_once ('ringside/m3/util/Settings.php');

class SettingsTestCase extends PHPUnit_Framework_TestCase
{
    public function testLocalSettings()
    {
        // this is really to make sure the dev local settings doesn't alter include path over and over
        $this->assertEquals(1, include 'LocalSettings.php');
        $_includePath1 = get_include_path();
        $this->assertEquals(1, include 'LocalSettings.php');
        $_includePath2 = get_include_path();
        $this->assertSame($_includePath1, $_includePath2, "local settings should not have altered include path twice");
    }

    public function testSettings()
    {
        $_allowEval = M3_Util_Settings::getAllowOperationEvaluatePhpCode();
        $this->assertType('boolean', $_allowEval);

        $_maxSizeAllowedFileContent = M3_Util_Settings::getMaxSizeAllowedOperationGetFileContent();
        $this->assertGreaterThanOrEqual(0, $_maxSizeAllowedFileContent);

        $_dataDir = M3_Util_Settings::getDataDirectory();
        $this->assertFalse(empty($_dataDir));

        $_dataStoreEnum = M3_Util_Settings::getDatastore();
        $this->assertFalse(empty($_dataStoreEnum));
        $this->assertType(M3_Util_DatastoreEnum, $_dataStoreEnum);

        $_dirs = M3_Util_Settings::getDeployedApiLocations();
        $this->assertEquals(3, count($_dirs));
        $this->assertContains('/ringside/rest', $_dirs);
        $this->assertContains('/m3/rest', $_dirs);
        $this->assertContains('/ringside/api/facebook', $_dirs);
        
        $_php = M3_Util_Settings::getPhpIniFileLocation();
        $this->assertGreaterThan(strlen("php.ini"), strlen($_php), "should be a full path to the php.ini file");
        $_phpContent = M3_Util_Settings::getPhpIniSettings();
        $this->assertTrue(!empty($_phpContent));
        
        $_dbProfileEnable = M3_Util_Settings::getDbProfilerEnable();
        $this->assertNotNull($_dbProfileEnable);
        $this->assertTrue($_dbProfileEnable === true || $_dbProfileEnable === false, "Should have been a boolean");
    }
    
    public function testGetErrorLogs()
    {
        $_phpErrorLog = M3_Util_Settings::getPhpErrorLogPathName();
        $_webInstallDir = M3_Util_Settings::getWebServerInstallationDirectory();
        $_webErrorLog = M3_Util_Settings::getWebServerErrorLogPathName();
    }
}

?>