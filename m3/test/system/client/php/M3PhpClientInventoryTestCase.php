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

class M3PhpClientInventoryTestCase extends BaseM3PhpClientTestCase
{
    public function testGetApis()
    {
        $_c = $this->getClient();
        $_results = $_c->inventoryGetApis();
        $this->assertTrue(is_array($_results));
        $this->assertArrayHasKey('m3', $_results, "Should have at least seen our M3 API used to get these results");
        $this->assertArrayHasKey('api_name', $_results['m3'][0], "Missing api_name key");
        $this->assertArrayHasKey('pathname', $_results['m3'][0], "Missing pathname key");
        $_foundit = false;
        foreach ($_results['m3'] as $_api)
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
        
        $this->assertArrayHasKey('ringside', $_results, "Why aren't the Ringside APIs deployed?");
        $this->assertArrayHasKey('api_name', $_results['ringside'][0], "Missing api_name key!");
        $this->assertArrayHasKey('pathname', $_results['ringside'][0], "Missing pathname key!");
        $_foundit = false;
        foreach ($_results['ringside'] as $_api)
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

    public function testGetApi()
    {
        $_c = $this->getClient();
        $_results = $_c->inventoryGetApi('m3.inventory.GetApi');
        $this->assertTrue(is_array($_results));
        $this->assertTrue(is_array($_results['api']));
        $this->assertEquals(1, count($_results));
        $this->assertArrayNotHasKey('m3', $_results['api'], "Should have been a flat array list, not grouped by namespace");
        $this->assertArrayNotHasKey('ringside', $_results['api'], "Should have been a flat array list, not grouped by namespace.");
        $this->assertArrayNotHasKey('facebook', $_results['api'], "Should have been a flat array list, not grouped by namespace!");
        $this->assertArrayHasKey("api_name", $_results['api']);
        $this->assertArrayHasKey("pathname", $_results['api']);
        $this->assertEquals('m3.inventory.GetApi', $_results['api']['api_name']);
        $this->assertEquals(1, preg_match("/InventoryGetApi.php$/", $_results['api']['pathname']));
        
        $_c = $this->getClient();
        $_results = $_c->inventoryGetApi('ringside.admin.createApp');
        $this->assertEquals('ringside.admin.createApp', $_results['api']['api_name']);
        $this->assertEquals(1, preg_match("/AdminCreateApp.php$/", $_results['api']['pathname']));
        
        $_c = $this->getClient();
        $_results = $_c->inventoryGetApi('facebook.fql.query');
        $this->assertEquals('facebook.fql.query', $_results['api']['api_name']);
        $this->assertEquals(1, preg_match("/FqlQuery.php$/", $_results['api']['pathname']));

        $_c = $this->getClient();
        $_results = $_c->inventoryGetApi('does.not.exist');
        $this->assertTrue(is_array($_results));
        $this->assertTrue(isset($_results['api'])); // it is set, its just an empty string
        $this->assertFalse(is_array($_results['api']));
    }

    public function testGetApplications()
    {
        $_c = $this->getClient();
        $_results = $_c->inventoryGetApplications();
        $this->assertTrue(is_array($_results));
        $this->assertGreaterThan(0, count($_results), "there should be at least one deployed application");
        $_app = $_results[0];
        $this->assertArrayHasKey("id", $_app);
        $this->assertArrayHasKey("callback_url", $_app);
        $this->assertArrayHasKey("name", $_app);
        $this->assertArrayHasKey("canvas_url", $_app);
        $this->assertArrayHasKey("sidenav_url", $_app);
        $this->assertArrayHasKey("isdefault", $_app);
        $this->assertArrayHasKey("desktop", $_app);
        $this->assertArrayHasKey("developer_mode", $_app);
        $this->assertArrayHasKey("author", $_app);
        $this->assertArrayHasKey("author_url", $_app);
        $this->assertArrayHasKey("author_description", $_app);
        $this->assertArrayHasKey("support_email", $_app);
        $this->assertArrayHasKey("canvas_type", $_app);
        $this->assertArrayHasKey("application_type", $_app);
        $this->assertArrayHasKey("mobile", $_app);
        $this->assertArrayHasKey("deployed", $_app);
        $this->assertArrayHasKey("description", $_app);
        $this->assertArrayHasKey("default_fbml", $_app);
        $this->assertArrayHasKey("tos_url", $_app);
        $this->assertArrayHasKey("icon_url", $_app);
        $this->assertArrayHasKey("postadd_url", $_app);
        $this->assertArrayHasKey("postremove_url", $_app);
        $this->assertArrayHasKey("privacy_url", $_app);
        $this->assertArrayHasKey("ip_list", $_app);
        $this->assertArrayHasKey("about_url", $_app);
        $this->assertArrayHasKey("logo_url", $_app);
        $this->assertArrayHasKey("edit_url", $_app);
        $this->assertArrayHasKey("default_column", $_app);
        $this->assertArrayHasKey("attachment_action", $_app);
        $this->assertArrayHasKey("attachment_callback_url", $_app); 
    }

    public function testGetApplication()
    {
        $_c = $this->getClient();
        
        // first get a list of applications so we can ask for one of them
        $_allApps = $_c->inventoryGetApplications();

        // ok, now we can test...
        $_results = $_c->inventoryGetApplication($_allApps[0]['id']);
        $this->assertTrue(is_array($_results));
        $this->assertArrayHasKey('application', $_results);
        $this->assertEquals(1, count($_results), "there should be a deployed application here");
        $_app = $_results['application'];
        $this->assertArrayHasKey("id", $_app);
        $this->assertEquals($_allApps[0]['id'], $_app['id']);
        $this->assertArrayHasKey("callback_url", $_app);
        $this->assertArrayHasKey("name", $_app);
        $this->assertArrayHasKey("canvas_url", $_app);
        $this->assertArrayHasKey("sidenav_url", $_app);
        $this->assertArrayHasKey("isdefault", $_app);
        $this->assertArrayHasKey("desktop", $_app);
        $this->assertArrayHasKey("developer_mode", $_app);
        $this->assertArrayHasKey("author", $_app);
        $this->assertArrayHasKey("author_url", $_app);
        $this->assertArrayHasKey("author_description", $_app);
        $this->assertArrayHasKey("support_email", $_app);
        $this->assertArrayHasKey("canvas_type", $_app);
        $this->assertArrayHasKey("application_type", $_app);
        $this->assertArrayHasKey("mobile", $_app);
        $this->assertArrayHasKey("deployed", $_app);
        $this->assertArrayHasKey("description", $_app);
        $this->assertArrayHasKey("default_fbml", $_app);
        $this->assertArrayHasKey("tos_url", $_app);
        $this->assertArrayHasKey("icon_url", $_app);
        $this->assertArrayHasKey("postadd_url", $_app);
        $this->assertArrayHasKey("postremove_url", $_app);
        $this->assertArrayHasKey("privacy_url", $_app);
        $this->assertArrayHasKey("ip_list", $_app);
        $this->assertArrayHasKey("about_url", $_app);
        $this->assertArrayHasKey("logo_url", $_app);
        $this->assertArrayHasKey("edit_url", $_app);
        $this->assertArrayHasKey("default_column", $_app);
        $this->assertArrayHasKey("attachment_action", $_app);
        $this->assertArrayHasKey("attachment_callback_url", $_app); 
    }

    public function testGetNetworks()
    {
        $_c = $this->getClient();
        $_results = $_c->inventoryGetNetworks();
        $this->assertTrue(is_array($_results));
        $this->assertGreaterThan(0, count($_results), "there should be at least one known network");
    }

    public function testGetNetwork()
    {
        $_c = $this->getClient();

        // first get a list of applications so we can ask for one of them
        $_allNetworks = $_c->inventoryGetNetworks();

        // ok, now we can test...
        $_results = $_c->inventoryGetNetwork($_allNetworks[0]['id']);
        $this->assertTrue(is_array($_results));
        $this->assertArrayHasKey('network', $_results);
        $this->assertEquals(1, count($_results), "there should be a network here");
        $_network = $_results['network'];
        $this->assertArrayHasKey("id", $_network);
        $this->assertEquals($_allNetworks[0]['id'], $_network['id']);
        $this->assertArrayHasKey("name", $_network);
    }


    public function testGetTags()
    {
        $_c = $this->getClient();
        $_results = $_c->inventoryGetTags();
        $this->assertTrue(is_array($_results));
        $this->assertGreaterThan(0, count($_results), "should have at least 1 tag deployed");
        $_tag = $_results[0];
        $this->assertArrayHasKey('tag_namespace', $_tag);
        $this->assertArrayHasKey('tag_name', $_tag);
        $this->assertArrayHasKey('handler_class', $_tag);
        $this->assertArrayHasKey('source_file', $_tag);
        $this->assertArrayHasKey('source_file_last_modified', $_tag);
    }

    public function testGetTag()
    {
        $_c = $this->getClient();
        
        // first get a list of applications so we can ask for one of them
        $_allTags = $_c->inventoryGetTags();

        // ok, now we can test...
        $_results = $_c->inventoryGetTag($_allTags[0]['tag_namespace'], $_allTags[0]['tag_name']);
        $this->assertTrue(is_array($_results));
        $this->assertArrayHasKey('tag', $_results);
        $this->assertEquals(1, count($_results), "there should be a deployed tag here");
        $_tag = $_results['tag'];
        $this->assertArrayHasKey('tag_namespace', $_tag);
        $this->assertArrayHasKey('tag_name', $_tag);
        $this->assertArrayHasKey('handler_class', $_tag);
        $this->assertArrayHasKey('source_file', $_tag);
        $this->assertArrayHasKey('source_file_last_modified', $_tag);
        $this->assertEquals($_allTags[0]['tag_namespace'], $_tag['tag_namespace']);
        $this->assertEquals($_allTags[0]['tag_name'], $_tag['tag_name']);
    }

    public function testGetVersionInfo()
    {
        $_c = $this->getClient();
        $_results = $_c->inventoryGetVersionInfo();
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
        $this->assertGreaterThan(2, count($_results['extensions'])); // need at least curl,mcrypt,pdo
        $this->assertArrayHasKey('name', $_results['extensions'][0]);
        $this->assertArrayHasKey('version', $_results['extensions'][0]);
        $this->assertArrayHasKey('name', $_results['extensions'][1]);
        $this->assertArrayHasKey('version', $_results['extensions'][1]);
    }
    
    public function testPing()
    {
        $_c = $this->getClient();
        $_results = $_c->inventoryPing();
        $this->assertEquals('1', $_results);        
    }
}

?>