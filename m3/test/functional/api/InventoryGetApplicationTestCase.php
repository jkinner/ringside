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
require_once('m3/rest/InventoryGetApplication.php');
require_once('m3/rest/InventoryGetApplications.php');

class InventoryGetApplicationTestCase extends BaseM3ApiTestCase
{
    public function testGetApplication()
    {
        // need to get a list of all apps so we can pick one to get for our test
        $_apiCall = $this->initRest( new InventoryGetApplications(), null);
        $_allApps = $_apiCall->execute();
        $this->assertTrue(is_array($_allApps));
        $this->assertArrayHasKey('application', $_allApps);
        $this->assertGreaterThan(0, count($_allApps['application']));
        
        // Ok, now test...
        $_params = array('appId' => $_allApps['application'][0]['id']);
        $_apiCall = $this->initRest( new InventoryGetApplication(), $_params);
        $_results = $_apiCall->execute();
        $this->assertTrue(is_array($_results));
        $this->assertArrayHasKey('application', $_results);
        $this->assertEquals(1, count($_results), "there should be an app key");
        $this->assertGreaterThan(0, count($_results['application']), "there should be an application here");
        $_app = $_results['application'];
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
}
?>