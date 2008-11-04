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
require_once('m3/rest/InventoryGetVersionInfo.php');

class InventoryGetVersionInfoTestCase extends BaseM3ApiTestCase
{
    public function testGetVersionInfo()
    {
        $_apiCall = $this->initRest( new InventoryGetVersionInfo(), null);
        $_results = $_apiCall->execute();
        $this->assertTrue(is_array($_results));
        $this->assertArrayHasKey('ringside', $_results);
        $this->assertArrayHasKey('version', $_results['ringside']);
        $this->assertArrayHasKey('build_date', $_results['ringside']);
        $this->assertArrayHasKey('build_number', $_results['ringside']);
        $this->assertArrayHasKey('svn_revision', $_results['ringside']);
        $this->assertArrayHasKey('install_dir', $_results['ringside']);
        $this->assertTrue(strlen($_results['ringside']['install_dir']) > 0);
        $this->assertArrayHasKey('php', $_results);
        $this->assertArrayHasKey('version', $_results['php']);
        $this->assertArrayHasKey('doctrine_version', $_results['php']);
        $this->assertArrayHasKey('php_ini_file', $_results['php']);
        $this->assertArrayHasKey('extensions', $_results);
        $this->assertGreaterThan(2, count($_results['extensions']['extension'])); // need at least curl,mcrypt,pdo
        $this->assertArrayHasKey('name', $_results['extensions']['extension'][0]);
        $this->assertArrayHasKey('version', $_results['extensions']['extension'][0]);
        $this->assertArrayHasKey('name', $_results['extensions']['extension'][1]);
        $this->assertArrayHasKey('version', $_results['extensions']['extension'][1]);
    }
}
?>