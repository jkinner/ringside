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
require_once('m3/rest/OperationGetFileContent.php');
require_once('ringside/m3/client/gzdecode.php');

class OperationGetFileContentTestCase extends BaseM3ApiTestCase
{
    public function testErrors()
    {
        $_params = array('pathname' => '../LocalSettings.php');
        try
        {
            $_apiCall = $this->initRest( new OperationGetFileContent(), $_params);        
            $this->fail("Should have failed, not allowed to get .. content");
        }
        catch (OpenFBAPIException $expected)
        {
        }

        $_params = array('pathname' => '/does/not/exist');
        try
        {
            $_apiCall = $this->initRest( new OperationGetFileContent(), $_params);        
            $this->fail("Should have failed, not allowed to get .. content");
        }
        catch (OpenFBAPIException $expected)
        {
        }
    }

    public function testGetContent()
    {
        $_params = array('pathname' => 'LocalSettings.php');
        $_apiCall = $this->initRest( new OperationGetFileContent(), $_params);
        $_results = M3_Client_gzdecode::gzdecode(base64_decode($_apiCall->execute()));
        $this->assertFalse(empty($_results), 'should have found local settings');
        $this->assertGreaterThan(0, strpos($_results, 'm3SecretKey'), 'should have true local settings content');

        $_params = array('pathname' => 'LocalSettings.php', 'useIncludePath' => 'true');
        $_apiCall = $this->initRest( new OperationGetFileContent(), $_params);
        $_results = M3_Client_gzdecode::gzdecode(base64_decode($_apiCall->execute()));
        $this->assertFalse(empty($_results), 'Should have found local settings');
        $this->assertGreaterThan(0, strpos($_results, 'm3SecretKey'), 'Should have true local settings content');

        $_params = array('pathname' => 'LocalSettings.php', 'useIncludePath' => true);
        $_apiCall = $this->initRest( new OperationGetFileContent(), $_params);
        $_results = M3_Client_gzdecode::gzdecode(base64_decode($_apiCall->execute()));
        $this->assertFalse(empty($_results), 'Should have found local settings.');
        $this->assertGreaterThan(0, strpos($_results, 'm3SecretKey'), 'Should have true local settings content.');

        $_params = array('pathname' => 'LocalSettings.php', 'useIncludePath' => false);
        $_apiCall = $this->initRest( new OperationGetFileContent(), $_params);
        $_results = M3_Client_gzdecode::gzdecode(base64_decode($_apiCall->execute()));
        $this->assertFalse(empty($_results), 'Should have found local settings from install dir.');
        $this->assertGreaterThan(0, strpos($_results, 'm3SecretKey'), 'Should have true local settings content from install dir.');
    }
}
?>