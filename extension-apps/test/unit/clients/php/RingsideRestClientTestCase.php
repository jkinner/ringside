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
require_once('ringside/core/client/RingsideRestClient.php');
require_once( 'PHPUnit/Framework.php' );

define('METHOD_NAME_KEY', 'method_name');
define('PARAMS_KEY', 'params');

class RingsideRestClientTestCase extends PHPUnit_Framework_TestCase {
	var $client;
	var $mock_delegate;
	
	public function setUp() {
		$this->mock_delegate = new RingsideRestClientTestCase_MockDelegate();
		$this->client = new RingsideRestClient($this->mock_delegate);
	}
	
	public function testRatingsSet() {
		$this->client->ratings_set(1234, 2.5);
		$this->assertEquals(RingsideRestConstants::RATINGS_SET_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
		$this->assertEquals(3, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iid']), "Expected iid to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['vote']), "Expected vote to be set in parameters");
      $this->assertTrue(!isset($this->mock_delegate->called[0][PARAMS_KEY]['type']), "Expected type to be set in parameters");
		$this->assertEquals(1234, $this->mock_delegate->called[0][PARAMS_KEY]['iid']);
      $this->assertEquals(2.5, $this->mock_delegate->called[0][PARAMS_KEY]['vote'], 0.01);
      $this->assertNull($this->mock_delegate->called[0][PARAMS_KEY]['type'], "Exepected null list ID argument");

      $this->mock_delegate->reset();
	   $this->client->ratings_set(1234, 2.5, 43);
      $this->assertEquals(RingsideRestConstants::RATINGS_SET_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(3, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iid']), "Expected iid to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['vote']), "Expected vote to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['type']), "Expected type to be set in parameters");
      $this->assertEquals(1234, $this->mock_delegate->called[0][PARAMS_KEY]['iid']);
      $this->assertEquals(2.5, $this->mock_delegate->called[0][PARAMS_KEY]['vote'], 0.01);
      $this->assertEquals(43, $this->mock_delegate->called[0][PARAMS_KEY]['type']);
	}

   public function testRatingsGet() {
      $this->client->ratings_get( array(1234, 5678));
      $this->assertEquals(RingsideRestConstants::RATINGS_GET_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(2, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertEquals(array(1234, 5678), $this->mock_delegate->called[0][PARAMS_KEY]['iids']);
      $this->assertNull($this->mock_delegate->called[0][PARAMS_KEY]['uids'], "Expected null user ID array argument");
      
      $this->mock_delegate->reset();
      
      $this->client->ratings_get( array(1234, 5678), array(234, 345));
      $this->assertEquals(RingsideRestConstants::RATINGS_GET_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(2, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertEquals(array(1234, 5678), $this->mock_delegate->called[0][PARAMS_KEY]['iids']);
      $this->assertEquals(234, $this->mock_delegate->called[0][PARAMS_KEY]['uids'][0]);
      $this->assertEquals(345, $this->mock_delegate->called[0][PARAMS_KEY]['uids'][1]);
      $this->assertEquals(array(234, 345), $this->mock_delegate->called[0][PARAMS_KEY]['uids']);
   }
   
   public function testRatingsGetAverage() {
   	$this->client->ratings_getAverage(1234);
      $this->assertEquals(RingsideRestConstants::RATINGS_GETAVERAGE_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(3, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iid']), "Expected iid to be set in parameters");
      $this->assertNull($this->mock_delegate->called[0][PARAMS_KEY]['uids'], "Expected uids to be null in parameters");
      $this->assertEquals(1234, $this->mock_delegate->called[0][PARAMS_KEY]['iid']);
      
      $this->mock_delegate->reset();

      $this->client->ratings_getAverage(1234, array(1234, 5678));
      $this->assertEquals(RingsideRestConstants::RATINGS_GETAVERAGE_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(3, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iid']), "Expected iid to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['uids']), "Expected uids to be set in parameters");
      $this->assertEquals(1234, $this->mock_delegate->called[0][PARAMS_KEY]['iid']);
      $this->assertEquals(array(1234, 5678), $this->mock_delegate->called[0][PARAMS_KEY]['uids']);

      $this->mock_delegate->reset();

      $this->client->ratings_getAverage(1234, array(1234, 5678), 456);
      $this->assertEquals(RingsideRestConstants::RATINGS_GETAVERAGE_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(3, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iid']), "Expected iid to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['uids']), "Expected uids to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['type']), "Expected type to be set in parameters");
      $this->assertEquals(1234, $this->mock_delegate->called[0][PARAMS_KEY]['iid']);
      $this->assertEquals(array(1234, 5678), $this->mock_delegate->called[0][PARAMS_KEY]['uids']);
      $this->assertEquals(456, $this->mock_delegate->called[0][PARAMS_KEY]['type']);
   }

   public function testItemsSetInfo() {
      $this->client->items_setInfo(1234, 'http://www.ringsidenetworks.org/foo.jpg', 'http://www.ringsidenetworks.org/foo.html');
      $this->assertEquals(RingsideRestConstants::ITEMS_SETINFO_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(4, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iid']), "Expected iid to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['url']), "Expected url to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['refurl']), "Expected refurl to be set in parameters");
      $this->assertNull($this->mock_delegate->called[0][PARAMS_KEY]['datatype'], "Expected datatype to be null in parameters");
      $this->assertEquals(1234, $this->mock_delegate->called[0][PARAMS_KEY]['iid']);
   	$this->assertEquals('http://www.ringsidenetworks.org/foo.jpg', $this->mock_delegate->called[0][PARAMS_KEY]['url']);
      $this->assertEquals('http://www.ringsidenetworks.org/foo.html', $this->mock_delegate->called[0][PARAMS_KEY]['refurl']);
      
      $this->mock_delegate->reset();
      
      $this->client->items_setInfo(1234, 'http://www.ringsidenetworks.org/foo.jpg', 'http://www.ringsidenetworks.org/foo.html', 999);
      $this->assertEquals(RingsideRestConstants::ITEMS_SETINFO_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(4, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iid']), "Expected iid to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['url']), "Expected url to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['refurl']), "Expected refurl to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['datatype']), "Expected datatype to be set in parameters");
      $this->assertEquals(1234, $this->mock_delegate->called[0][PARAMS_KEY]['iid']);
      $this->assertEquals('http://www.ringsidenetworks.org/foo.jpg', $this->mock_delegate->called[0][PARAMS_KEY]['url']);
      $this->assertEquals('http://www.ringsidenetworks.org/foo.html', $this->mock_delegate->called[0][PARAMS_KEY]['refurl']);
      $this->assertEquals(999, $this->mock_delegate->called[0][PARAMS_KEY]['datatype']);
   }
   
   public function testItemsRemove() {
      $this->client->items_remove(1234);
      $this->assertEquals(RingsideRestConstants::ITEMS_REMOVE_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      // Includes the uninitialized "datatype" parameter
      $this->assertEquals(2, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iid']), "Expected iid to be set in parameters");
   }
   
   public function testItemsGetInfo() {
   	$this->client->items_getInfo(array(1234, 5678));
      $this->assertEquals(RingsideRestConstants::ITEMS_GETINFO_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(2, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iids']), "Expected iids to be set in parameters");
      $this->assertNull($this->mock_delegate->called[0][PARAMS_KEY]['datatype'], "Expected datatype to be null in parameters");
      $this->assertEquals(array(1234, 5678), $this->mock_delegate->called[0][PARAMS_KEY]['iids']);
   	
      $this->mock_delegate->reset();

      $this->client->items_getInfo(array(1234, 5678), 999);
      $this->assertEquals(RingsideRestConstants::ITEMS_GETINFO_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(2, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iids']), "Expected iids to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['datatype']), "Expected datatype to be set in parameters");
      $this->assertEquals(array(1234, 5678), $this->mock_delegate->called[0][PARAMS_KEY]['iids']);
      $this->assertEquals(999, $this->mock_delegate->called[0][PARAMS_KEY]['datatype']);
   }
   
   public function testFavoritesCreateList() {
      $this->client->favorites_createList("Foo");
      $this->assertEquals(RingsideRestConstants::FAVORITES_CREATELIST_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(1, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['name']), "Expected name to be set in parameters");
      $this->assertEquals("Foo", $this->mock_delegate->called[0][PARAMS_KEY]['name']);
   }

   public function testFavoritesGetLists() {
      $this->client->favorites_getLists();
      $this->assertEquals(RingsideRestConstants::FAVORITES_GETLISTS_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(1, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(array_key_exists('uid', $this->mock_delegate->called[0][PARAMS_KEY]), "Expected method to use uid parameter");
      $this->assertNull($this->mock_delegate->called[0][PARAMS_KEY]['uid'], "Expected uid to be null in parameters");

      $this->mock_delegate->reset();
      
      $this->client->favorites_getLists(1234);
      $this->assertEquals(RingsideRestConstants::FAVORITES_GETLISTS_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(1, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(array_key_exists('uid', $this->mock_delegate->called[0][PARAMS_KEY]), "Expected method to use uid parameter");
      $this->assertEquals(1234, $this->mock_delegate->called[0][PARAMS_KEY]['uid']);
   }
   
   public function testFavoritesSet() {
      $this->client->favorites_set(1234);
      $this->assertEquals(RingsideRestConstants::FAVORITES_SET_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(2, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iid']), "Expected iid to be set in parameters");
      $this->assertTrue(array_key_exists('lid', $this->mock_delegate->called[0][PARAMS_KEY]), "Expected method to use lid parameter");
      $this->assertNull($this->mock_delegate->called[0][PARAMS_KEY]['lid'], "Expected lid to be null in parameters");
      $this->assertEquals(1234, $this->mock_delegate->called[0][PARAMS_KEY]['iid']);
      
      $this->mock_delegate->reset();
   	
      $this->client->favorites_set(1234, 999);
      $this->assertEquals(RingsideRestConstants::FAVORITES_SET_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(2, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iid']), "Expected iid to be set in parameters");
      $this->assertTrue(array_key_exists('lid', $this->mock_delegate->called[0][PARAMS_KEY]), "Expected method to use lid parameter");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['lid']), "Expected lid to be set in parameters");
      $this->assertEquals(1234, $this->mock_delegate->called[0][PARAMS_KEY]['iid']);
      $this->assertEquals(999, $this->mock_delegate->called[0][PARAMS_KEY]['lid']);
   }
   
   public function testFavoritesGet() {
      $this->client->favorites_get(array(1234, 5678));
      $this->assertEquals(RingsideRestConstants::FAVORITES_GET_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(2, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['uids']), "Expected uids to be set in parameters");
      $this->assertNull($this->mock_delegate->called[0][PARAMS_KEY]['lid'], "Expected lid to be null in parameters");
      $this->assertEquals(array(1234, 5678), $this->mock_delegate->called[0][PARAMS_KEY]['uids']);
   	
      $this->mock_delegate->reset();

      $this->client->favorites_get(array(1234, 5678), 999);
      $this->assertEquals(RingsideRestConstants::FAVORITES_GET_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(2, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['uids']), "Expected uids to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['lid']), "Expected lid to be set in parameters");
      $this->assertEquals(array(1234, 5678), $this->mock_delegate->called[0][PARAMS_KEY]['uids']);
      $this->assertEquals(999, $this->mock_delegate->called[0][PARAMS_KEY]['lid']);
   }

   public function testFavoritesSetApp() {
      $this->client->favorites_setApp(1234);
      $this->assertEquals(RingsideRestConstants::FAVORITES_SET_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(2, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iid']), "Expected iid to be set in parameters");
      $this->assertTrue(array_key_exists('alid', $this->mock_delegate->called[0][PARAMS_KEY]), "Expected method to use alid parameter");
      $this->assertNull($this->mock_delegate->called[0][PARAMS_KEY]['alid'], "Expected lid to be null in parameters");
      $this->assertEquals(1234, $this->mock_delegate->called[0][PARAMS_KEY]['iid']);
      
      $this->mock_delegate->reset();
      
      $this->client->favorites_setApp(1234, 999);
      $this->assertEquals(RingsideRestConstants::FAVORITES_SET_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(2, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iid']), "Expected iid to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['alid']), "Expected alid to be set in parameters");
      $this->assertEquals(1234, $this->mock_delegate->called[0][PARAMS_KEY]['iid']);
      $this->assertEquals(999, $this->mock_delegate->called[0][PARAMS_KEY]['alid']);
   }
   
   public function testFavoritesGetApp() {
      $this->client->favorites_getApp(array(1234, 5678));
      $this->assertEquals(RingsideRestConstants::FAVORITES_GET_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(2, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['uids']), "Expected uids to be set in parameters");
      $this->assertTrue(array_key_exists('alid', $this->mock_delegate->called[0][PARAMS_KEY]), "Expected method to use alid parameter");
      $this->assertNull($this->mock_delegate->called[0][PARAMS_KEY]['alid'], "Expected lid to be null in parameters");
      $this->assertEquals(array(1234, 5678), $this->mock_delegate->called[0][PARAMS_KEY]['uids']);
      
      $this->mock_delegate->reset();

      $this->client->favorites_getApp(array(1234, 5678), 999);
      $this->assertEquals(RingsideRestConstants::FAVORITES_GET_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(2, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['uids']), "Expected uids to be set in parameters");
      $this->assertTrue(array_key_exists('alid', $this->mock_delegate->called[0][PARAMS_KEY]), "Expected method to use alid parameter");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['alid']), "Expected alid to be set in parameters");
      $this->assertEquals(array(1234, 5678), $this->mock_delegate->called[0][PARAMS_KEY]['uids']);
      $this->assertEquals(999, $this->mock_delegate->called[0][PARAMS_KEY]['alid']);
   }
   
   public function testFavoritesGetUsers() {
      $this->client->favorites_getUsers(1234);
      $this->assertEquals(RingsideRestConstants::FAVORITES_GETUSERS_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(3, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iid']), "Expected iid to be set in parameters");
      $this->assertEquals(1234, $this->mock_delegate->called[0][PARAMS_KEY]['iid']);
      
      $this->mock_delegate->reset();

      $this->client->favorites_getUsers(1234, array(1234, 5678));
      $this->assertEquals(RingsideRestConstants::FAVORITES_GETUSERS_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(3, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iid']), "Expected iid to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['uids']), "Expected uids to be set in parameters");
      $this->assertNull($this->mock_delegate->called[0][PARAMS_KEY]['lid'], "Expected lid to be null in parameters");
      $this->assertEquals(1234, $this->mock_delegate->called[0][PARAMS_KEY]['iid']);
      $this->assertEquals(array(1234, 5678), $this->mock_delegate->called[0][PARAMS_KEY]['uids']);
      
      $this->mock_delegate->reset();

      $this->client->favorites_getUsers(1234, array(1234, 5678), 999);
      $this->assertEquals(RingsideRestConstants::FAVORITES_GETUSERS_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(3, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iid']), "Expected iid to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['uids']), "Expected uids to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['lid']), "Expected lid to be set in parameters");
      $this->assertEquals(1234, $this->mock_delegate->called[0][PARAMS_KEY]['iid']);
      $this->assertEquals(array(1234, 5678), $this->mock_delegate->called[0][PARAMS_KEY]['uids']);
      $this->assertEquals(999, $this->mock_delegate->called[0][PARAMS_KEY]['lid']);
   }

   public function testFavoritesGetFBML() {
      $this->client->favorites_getFBML(1234);
      $this->assertEquals(RingsideRestConstants::FAVORITES_GETFBML_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(3, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iid']), "Expected iid to be set in parameters");
      $this->assertEquals(1234, $this->mock_delegate->called[0][PARAMS_KEY]['iid']);
      
      $this->mock_delegate->reset();

      $this->client->favorites_getFBML(1234, array(1234, 5678));
      $this->assertEquals(RingsideRestConstants::FAVORITES_GETFBML_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(3, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iid']), "Expected iid to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['uids']), "Expected uids to be set in parameters");
      $this->assertNull($this->mock_delegate->called[0][PARAMS_KEY]['lid'], "Expected lid to be null in parameters");
      $this->assertEquals(1234, $this->mock_delegate->called[0][PARAMS_KEY]['iid']);
      $this->assertEquals(array(1234, 5678), $this->mock_delegate->called[0][PARAMS_KEY]['uids']);
      
      $this->mock_delegate->reset();

      $this->client->favorites_getFBML(1234, array(1234, 5678), 999);
      $this->assertEquals(RingsideRestConstants::FAVORITES_GETFBML_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(3, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iid']), "Expected iid to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['uids']), "Expected uids to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['lid']), "Expected lid to be set in parameters");
      $this->assertEquals(1234, $this->mock_delegate->called[0][PARAMS_KEY]['iid']);
      $this->assertEquals(array(1234, 5678), $this->mock_delegate->called[0][PARAMS_KEY]['uids']);
      $this->assertEquals(999, $this->mock_delegate->called[0][PARAMS_KEY]['lid']);
   }

   public function testFavoritesSetFBML() {
   	$testFBML = "<fb:name uid='loggedinuser' useyou='false'/>";
      $this->client->favorites_setFBML(1234, $testFBML);
      $this->assertEquals(RingsideRestConstants::FAVORITES_SETFBML_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(3, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iid']), "Expected iid to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['fbml']), "Expected fbml to be set in parameters");
      $this->assertNull($this->mock_delegate->called[0][PARAMS_KEY]['lid'], "Expected lid to be null in parameters");
      $this->assertEquals(1234, $this->mock_delegate->called[0][PARAMS_KEY]['iid']);
      $this->assertEquals($testFBML, $this->mock_delegate->called[0][PARAMS_KEY]['fbml']);
            
      $this->mock_delegate->reset();

      $this->client->favorites_setFBML(1234, $testFBML, 999);
      $this->assertEquals(RingsideRestConstants::FAVORITES_SETFBML_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(3, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iid']), "Expected iid to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['fbml']), "Expected fbml to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['lid']), "Expected lid to be set in parameters");
      $this->assertEquals(1234, $this->mock_delegate->called[0][PARAMS_KEY]['iid']);
      $this->assertEquals($testFBML, $this->mock_delegate->called[0][PARAMS_KEY]['fbml']);
      $this->assertEquals(999, $this->mock_delegate->called[0][PARAMS_KEY]['lid']);
   }

   public function testFavoritesDelete() {
      $this->client->favorites_delete(1234);
      $this->assertEquals(RingsideRestConstants::FAVORITES_DELETE_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(2, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iid']), "Expected iid to be set in parameters");
      $this->assertNull($this->mock_delegate->called[0][PARAMS_KEY]['lid'], "Expected lid to be null in parameters");
      $this->assertEquals(1234, $this->mock_delegate->called[0][PARAMS_KEY]['iid']);
            
      $this->mock_delegate->reset();

      $this->client->favorites_delete(1234, 999);
      $this->assertEquals(RingsideRestConstants::FAVORITES_DELETE_METHOD, $this->mock_delegate->called[0][METHOD_NAME_KEY]);
      $this->assertEquals(2, sizeof($this->mock_delegate->called[0][PARAMS_KEY]));
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['iid']), "Expected iid to be set in parameters");
      $this->assertTrue(isset($this->mock_delegate->called[0][PARAMS_KEY]['lid']), "Expected lid to be set in parameters");
      $this->assertEquals(1234, $this->mock_delegate->called[0][PARAMS_KEY]['iid']);
      $this->assertEquals(999, $this->mock_delegate->called[0][PARAMS_KEY]['lid']);
   }
}

class RingsideRestClientTestCase_MockDelegate {
   public $called;
   
   public function addServer($namespace, $server) {
   	// Do nothing
   }
   
	public function call_method($method_name, $params) {
		$this->called[] = array( METHOD_NAME_KEY => $method_name, PARAMS_KEY => $params );
	}
	
	public function reset() {
		$this->called = null;
	}
}
