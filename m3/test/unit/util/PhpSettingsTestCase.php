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
require_once ('ringside/m3/util/PhpSettings.php');

class PhpSettingsTestCase extends PHPUnit_Framework_TestCase
{
    public function testDefaultLocation()
    {
        $_php = new M3_Util_PhpSettings();
        $this->assertEquals(1, preg_match( '/.*LocalSettings\.php$/', $_php->getConfigurationPathName()), 'Wrong default php file name');
    }

    public function testCreate()
    {
        $_php = new M3_Util_PhpSettings('PhpSettingsTestCase1.php');
        $_data = array( M3_Util_PhpSettings::UNPARSED . '0' => "<?php\n",
                       'g1' => 'gone',
                       'g2' => 'gtwo',
                       'str1' => '"string here"',
                       'str2' => '\'another string\'',
                       M3_Util_PhpSettings::UNPARSED . '1' => "?>\n");
        $_php->writePhpFile($_data);
        $_php->readPhpFile($_readData);
        $this->assertEquals(6, count($_readData)); // includes the two UNPARSED lines
        $this->assertSame($_data, $_readData);

        unlink($_php->getConfigurationPathName());
    }

    public function testPreserveComments()
    {
        $_php = new M3_Util_PhpSettings('PhpSettingsTestCase2.php');

        $_data = array( M3_Util_PhpSettings::UNPARSED . '0' => "<?php\n",
                        'g1' => 'gone',
                        'g2' => 'gtwo',
                        M3_Util_PhpSettings::UNPARSED . '1' => "#\$var=ignored;\n",
                        'str1' => '"string here"',
                        'str2' => '\'another string\'',
                        M3_Util_PhpSettings::UNPARSED . '2' => "?>\n");
        
        $_php->writePhpFile($_data);
        $_contents = file_get_contents($_php->getConfigurationPathName());
        $this->assertEquals(1, preg_match('/.*\\n#\$var=ignored;\\n/', $_contents)); // wrote comments
        $_php->readPhpFile($_readData);
        $this->assertArrayNotHasKey("var", $_readData); // sanity check - the $var definition is commented out
        $this->assertArrayHasKey(M3_Util_PhpSettings::UNPARSED . '1', $_readData);
        $this->assertEquals("#\$var=ignored;\n", $_readData[M3_Util_PhpSettings::UNPARSED . '1']);
        $_php->writePhpFile($_readData); // this should preserve our comment
        $_contents = file_get_contents($_php->getConfigurationPathName());
        $this->assertEquals(1, preg_match('/.*\\n#\$var=ignored;\\n/', $_contents)); // wrote comments

        unset($_readData);
        $_php->readPhpFile($_readData, true);
        $this->assertEquals(4, count($_readData)); // does not include the three UNPARSED lines
        $this->assertArrayHasKey('g1', $_readData);
        $this->assertArrayHasKey('g2', $_readData);
        $this->assertArrayHasKey('str1', $_readData);
        $this->assertArrayHasKey('str2', $_readData);
        $this->assertArrayNotHasKey(M3_Util_PhpSettings::UNPARSED.'0', $_readData);
        $this->assertArrayNotHasKey(M3_Util_PhpSettings::UNPARSED.'1', $_readData);
        $this->assertArrayNotHasKey(M3_Util_PhpSettings::UNPARSED.'2', $_readData);
        unlink($_php->getConfigurationPathName());
    }

    public function testReadSetRuntimeValues()
    {
        $_php = new M3_Util_PhpSettings('PhpSettingsTestCase3.php');
        $_data = array( M3_Util_PhpSettings::UNPARSED . '0' => "<?php\n",
                       'GLOBALS[\'g1\']' => 'gone',
                       'GLOBALS[\'g2\']' => 'gtwo',
                       'GLOBALS[\'str1\']' => '"string here"',
                       'GLOBALS[\'str2\']' => '\'another string\'',
                       M3_Util_PhpSettings::UNPARSED . '1' => "?>\n");
        $_php->writePhpFile($_data);
        $_php->readPhpFileSetRuntimeValues($_readData, true);
        $this->assertEquals(4, count($_readData)); // does not include the two UNPARSED lines
        $this->assertArrayHasKey('GLOBALS[\'g1\']', $_readData);
        $this->assertArrayHasKey('GLOBALS[\'g2\']', $_readData);
        $this->assertArrayHasKey('GLOBALS[\'str1\']', $_readData);
        $this->assertArrayHasKey('GLOBALS[\'str1\']', $_readData);
        $this->assertArrayNotHasKey(M3_Util_PhpSettings::UNPARSED.'0', $_readData);
        $this->assertArrayNotHasKey(M3_Util_PhpSettings::UNPARSED.'1', $_readData);
        $this->assertEquals($GLOBALS['g1'], "gone");
        $this->assertEquals($GLOBALS['g2'], "gtwo");
        $this->assertEquals($GLOBALS['str1'], "string here");
        $this->assertEquals($GLOBALS['str2'], "another string");

        unlink($_php->getConfigurationPathName());
    }

    public function testWriteWithRuntimeValues()
    {
        $_php = new M3_Util_PhpSettings('PhpSettingsTestCase4.php');

        $_data = array( M3_Util_PhpSettings::UNPARSED . '0' => "<?php\n",
                        'GLOBALS[\'g1\']' => 'gone',
                        'GLOBALS[\'g2\']' => 'gtwo',
                        'GLOBALS[\'str1\']' => '"string here"',
                        'GLOBALS[\'str2\']' => '\'another string\'',
                        M3_Util_PhpSettings::UNPARSED . '2' => "?>\n");
        
        $GLOBALS['g1'] = '"GONE REPLACEMENT"';
        $GLOBALS['str1'] = '"STR1 REPLACEMENT"';
        $_php->writePhpFileWithRuntimeValues($_data);

        $_contents = file_get_contents($_php->getConfigurationPathName());

        $this->assertEquals(1, preg_match('/\n\$GLOBALS\[\'g1\'\]="GONE REPLACEMENT";\n/', $_contents), "bad 1");
        $this->assertEquals(1, preg_match('/\n\$GLOBALS\[\'g2\'\]=;\n/', $_contents), "bad 2");
        $this->assertEquals(1, preg_match('/\n\$GLOBALS\[\'str1\'\]="STR1 REPLACEMENT";\n/', $_contents), "bad 3");
        $this->assertEquals(1, preg_match('/\n\$GLOBALS\[\'str2\'\]=;\n/', $_contents), "bad 4");
        $_php->readPhpFile($_readData);
        $this->assertArrayHasKey('GLOBALS[\'g1\']', $_readData);
        $this->assertArrayHasKey('GLOBALS[\'g2\']', $_readData);
        $this->assertArrayHasKey('GLOBALS[\'str1\']', $_readData);
        $this->assertArrayHasKey('GLOBALS[\'str2\']', $_readData);
        
        // remove test file
        unlink($_php->getConfigurationPathName());
    }
}

?>