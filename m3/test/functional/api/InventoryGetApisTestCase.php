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
require_once('m3/rest/InventoryGetApis.php');

class InventoryGetApisTestCase extends BaseM3ApiTestCase
{
    public function testGetApis()
    {
        $_apiCall = $this->initRest( new InventoryGetApis(), null);
        $_results = $_apiCall->execute();
        $this->assertTrue(is_array($_results));
        $this->assertTrue(is_array($_results['api']));
        $this->assertEquals(1, count($_results));
        $this->assertArrayNotHasKey('m3', $_results['api'], "Should have been a flat array list, not grouped by namespace");
        $this->assertArrayNotHasKey('ringside', $_results['api'], "Should have been a flat array list, not grouped by namespace.");
        $this->assertArrayNotHasKey('facebook', $_results['api'], "Should have been a flat array list, not grouped by namespace!");
        $this->assertArrayHasKey("api_name", $_results['api'][0]);
        $this->assertArrayHasKey("pathname", $_results['api'][0]);
        
        $_foundit = false;
        foreach ($_results['api'] as $_api)
        {
            if ($_api['api_name'] === 'm3.inventory.getApis')
            {
                $_foundit = true;
                $this->assertEquals(1, preg_match("/InventoryGetApis.php$/", $_api['pathname']));
                break;
            }
        }

        if (!$_foundit)
        {
            $this->fail("Can't find m3.inventory.getApis API which should be there");
        }

        $_foundit = false;
        foreach ($_results['api'] as $_api)
        {
            if ($_api['api_name'] === 'ringside.admin.createApp')
            {
                $_foundit = true;
                $this->assertEquals(1, preg_match("/AdminCreateApp.php$/", $_api['pathname']));
                break;
            }
        }

        if (!$_foundit)
        {
            $this->fail("Can't find ringside.admin.createApp API which should be there");
        }
    }
}
?>