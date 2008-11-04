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
require_once('BasePhpClientTestCase.php');

require_once('ringside/core/client/RingsideRestClient.php');

class ItemsTestCase extends BasePhpClientTestCase {
	public function testItemsSetInfo() {
		$coreClient = new RingsideRestClient($this->fbClient);
		$coreClient->items_setInfo(123, 'http://localhost/foo.jpg', 'http://localhost/foo.html');
	}
	
	public function testItemsGetInfo() {
		$coreClient = new RingsideRestClient($this->fbClient);
		$coreClient->items_setInfo(123, 'http://localhost/foo.jpg', 'http://localhost/foo.html');
		$info = $coreClient->items_getInfo(123);
		$this->assertEquals('http://localhost/foo.jpg', $info[0]['url']);
		$this->assertEquals('http://localhost/foo.html', $info[0]['refurl']);
	}

	public function testItemsGetInfoSingleArray() {
		$coreClient = new RingsideRestClient($this->fbClient);
		$coreClient->items_setInfo(123, 'http://localhost/foo.jpg', 'http://localhost/foo.html');
		$info = $coreClient->items_getInfo(array(123));
		$this->assertEquals('http://localhost/foo.jpg', $info[0]['url']);
		$this->assertEquals('http://localhost/foo.html', $info[0]['refurl']);
	}
	
	public function testItemsRemove() {
		$coreClient = new RingsideRestClient($this->fbClient);
		$coreClient->items_setInfo(123, 'http://localhost/foo.jpg', 'http://localhost/foo.html');
		$info = $coreClient->items_getInfo(123);
		$this->assertNotEquals(false, $info);
		$coreClient->items_remove(123);
		$info = $coreClient->items_getInfo(123);
		$this->assertEquals(false, $info);
	}
	
	public function testItemsGetInfoMulti() {
		$coreClient = new RingsideRestClient($this->fbClient);
		$coreClient->items_setInfo(123, 'http://localhost/foo.jpg', 'http://localhost/foo.html');
		$coreClient->items_setInfo(345, 'http://localhost/bar.jpg', 'http://localhost/bar.html');
		$info = $coreClient->items_getInfo(array(123, 345));
		$this->assertTrue(is_array($info), "Expected an array of item info, got $info");
		$this->assertEquals(2, sizeof($info));
		$this->assertEquals('http://localhost/foo.jpg', $info[0]['url']);
		$this->assertEquals('http://localhost/foo.html', $info[0]['refurl']);
		$this->assertEquals('http://localhost/bar.jpg', $info[1]['url']);
		$this->assertEquals('http://localhost/bar.html', $info[1]['refurl']);
		
	}

	public function testItemsGetInfoMultiNotThere() {
		$coreClient = new RingsideRestClient($this->fbClient);
		$coreClient->items_setInfo(123, 'http://localhost/foo.jpg', 'http://localhost/foo.html');
		$coreClient->items_setInfo(345, 'http://localhost/bar.jpg', 'http://localhost/bar.html');
		$info = $coreClient->items_getInfo(array(123, 234));
		$this->assertTrue(is_array($info), "Expected an array of item info, got $info");
		$this->assertEquals(1, sizeof($info));
		$this->assertEquals('http://localhost/foo.jpg', $info[0]['url']);
		$this->assertEquals('http://localhost/foo.html', $info[0]['refurl']);
		
	}
}
?>
