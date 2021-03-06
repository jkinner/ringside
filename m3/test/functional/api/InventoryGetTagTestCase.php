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
require_once('m3/rest/InventoryGetTag.php');

class InventoryGetTagTestCase extends BaseM3ApiTestCase
{
    public function testGetTag()
    {
        $_params = array('tagNamespace' => 'fb', 'tagName' => 'name');
        $_apiCall = $this->initRest( new InventoryGetTag(), $_params);
        $_results = $_apiCall->execute();
        $this->assertTrue(is_array($_results));
        $this->assertTrue(is_array($_results['tag']));
        $this->assertEquals(1, count($_results));
        $this->assertArrayHasKey('tag_namespace', $_results['tag']);
        $this->assertArrayHasKey('tag_name', $_results['tag']);
        $this->assertArrayHasKey('handler_class', $_results['tag']);
        $this->assertArrayHasKey('source_file', $_results['tag']);
        $this->assertArrayHasKey('source_file_last_modified', $_results['tag']);
    }
}
?>