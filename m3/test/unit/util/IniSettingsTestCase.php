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
require_once ('ringside/m3/util/IniSettings.php');
require_once ('ringside/m3/util/Settings.php');

class IniSettingsTestCase extends PHPUnit_Framework_TestCase
{
    public function testDefaultLocation()
    {
        $ini = new M3_Util_IniSettings();
        $this->assertEquals( 1, preg_match( '/.*ringside\.ini$/', $ini->getConfigurationPathName()), 'Wrong default ini file name');
    }

    public function testDefineGlobals()
    {
        $ini = new M3_Util_IniSettings('IniSettingsTestCaseGlobals.ini');

        $section1Data = array('one' => '111', 'two' => '{$GLOBALS[\'one\']} 222');
        $section2Data = array('foo' => 'bar {$GLOBALS[two]}');
        $writeData = array('globals' => $section1Data, 'section2' => $section2Data);

        // sanity check - these should not be defined yet
        $this->assertFalse(isset($GLOBALS['one']));
        $this->assertFalse(isset($GLOBALS['two']));
        $this->assertFalse(isset($GLOBALS['foo']));

        $ini->writeIniFile($writeData);
        $ini->defineGlobals(); // by default, loads in the 'globals' section

        // only the 'globals' section was made global
        $this->assertEquals('111', $GLOBALS['one']);
        $this->assertEquals('111 222', $GLOBALS['two']);
        $this->assertFalse(isset($GLOBALS['foo']));

        // test that we can make globals from any section
        $ini->defineGlobals('section2');
        $this->assertEquals('111', $GLOBALS['one']);
        $this->assertEquals('111 222', $GLOBALS['two']);
        $this->assertEquals('bar 111 222', $GLOBALS['foo']);
        
        // make sure the test data is what we think it is
        $ini->readIniFile($readData);
        $this->assertEquals(2, count($readData['globals']));
        $this->assertEquals(1, count($readData['section2']));
        $this->assertTrue($section1Data === $readData['globals']);
        $this->assertTrue($section2Data === $readData['section2']);

        unlink($ini->getConfigurationPathName());
    }

    public function testCreate()
    {
        $ini = new M3_Util_IniSettings('IniSettingsTestCase1.ini');

        $globalData = array('g1' => 'gone', 'g2' => 'gtwo');
        $section1Data = array('s1a' => 'section one a');
        $section2Data = array('s2y' => 'section two y', 's2z' => 'section two z');
        $writeData = array('' => $globalData, 'section1' => $section1Data, 'section2' => $section2Data);
        $ini->writeIniFile($writeData);
        $ini->readIniFile($readData);
        $this->assertEquals(3, count($readData));
        $this->assertEquals(2, count($readData['']));
        $this->assertEquals(1, count($readData['section1']));
        $this->assertEquals(2, count($readData['section2']));
        $this->assertTrue($globalData === $readData['']);
        $this->assertTrue($section1Data === $readData['section1']);
        $this->assertTrue($section2Data === $readData['section2']);

        unlink($ini->getConfigurationPathName());
    }

    public function testPreserveComments()
    {
        $ini = new M3_Util_IniSettings('IniSettingsTestCase2.ini');

        $globalData = array('g1' => 'gone', 'g2' => 'gtwo', '__Comment__0' => "# start s1a");
        $section1Data = array('s1a' => 'section one a');
        $writeData = array('' => $globalData, 'section1' => $section1Data);
        $ini->writeIniFile($writeData);
        $contents = file_get_contents($ini->getConfigurationPathName());
        $this->assertEquals(1, preg_match('/\n# start s1a\n/', $contents)); // wrote comments
        $ini->readIniFile($readData);
        $ini->writeIniFile($readData); // this should preserve our comment
        $contents = file_get_contents($ini->getConfigurationPathName());
        $this->assertEquals(1, preg_match('/\n# start s1a\n/', $contents));
        unlink($ini->getConfigurationPathName());
    }
    
    public function testPhpIniFile()
    {
        $_phpLocation = M3_Util_Settings::getPhpIniFileLocation();
        $this->assertTrue(!empty($_phpLocation));
        $_php = new M3_Util_IniSettings( $_phpLocation, false);
        $_php->readIniFile($_phpSettings, true);
        $this->assertArrayHasKey('PHP', $_phpSettings);
        $this->assertArrayHasKey('engine', $_phpSettings['PHP']);
        $this->assertEquals('on', strtolower($_phpSettings['PHP']['engine']));
    }
}

?>