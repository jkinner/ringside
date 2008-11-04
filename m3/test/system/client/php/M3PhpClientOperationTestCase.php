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

class M3PhpClientOperationTestCase extends BaseM3PhpClientTestCase
{
    public function testEval()
    {
        $_c = $this->getClient();

        try
        {
            @$_c->operationEvaluatePhpCode();
            $this->fail("Should have thrown an exception - we didn't pass in the params");
        }
        catch (Exception $expected)
        {
        }

        $_params = '$w = "wot"; $g = "gorilla?"; return $w . " " . $g;';
        $_results = $_c->operationEvaluatePhpCode(array('phpCode' => $_params));
        $this->assertEquals("wot gorilla?", $_results);
    }
    
    public function testGetContent()
    {
        $_c = $this->getClient();

        try
        {
            @$_c->operationGetFileContent();
            $this->fail("Should have thrown an exception - we didn't pass in the required param");
        }
        catch (Exception $expected)
        {
        }

        $_results = $_c->operationGetFileContent('LocalSettings.php');
        $this->assertFalse(empty($_results), 'should have found local settings');
        $this->assertGreaterThan(0, strpos($_results, 'm3SecretKey'), 'should have true local settings content');

        $_results = $_c->operationGetFileContent('LocalSettings.php', 'true');
        $this->assertFalse(empty($_results), 'Should have found local settings');
        $this->assertGreaterThan(0, strpos($_results, 'm3SecretKey'), 'Should have true local settings content');

        $_results = $_c->operationGetFileContent('LocalSettings.php', true);
        $this->assertFalse(empty($_results), 'Should have found local settings.');
        $this->assertGreaterThan(0, strpos($_results, 'm3SecretKey'), 'Should have true local settings content.');

        $_results = $_c->operationGetFileContent('LocalSettings.php', false);
        $this->assertFalse(empty($_results), 'Should have found local settings from install dir.');
        $this->assertGreaterThan(0, strpos($_results, 'm3SecretKey'), 'Should have true local settings content from install dir.');
    }
}

?>