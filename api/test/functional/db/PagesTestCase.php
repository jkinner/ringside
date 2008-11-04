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
require_once ('BaseDbTestCase.php');
require_once ("ringside/api/dao/Pages.php");
require_once ("RsOpenFBDbTestUtils.php");

define("PAGE_ONE", "2000");
define("PAGE_TWO_NO_APPS", "2001");
define("PAGE_THREE_NO_FRIENDS", "2002");

define("PAGE_ONE_APP_1_ENABLED", "2200");
define("PAGE_ONE_APP_2_DISABLED", "2201");
define("PAGE_ONE_APP_3_ENABLED", "2202");
define("PAGE_THREE_APP_1_ENABLED", "2200");

define("PAGE_ONE_USER_1_ADMIN_FAN", "2100");
define("PAGE_ONE_USER_1_ADMIN", "2101");
define("PAGE_ONE_USER_1_FAN", "2102");
define("PAGE_ONE_USER_2_FAN", "2103");
define("PAGE_ONE_USER_3_FAN", "2104");

define("PAGE_TWO_USER_1_ADMIN", "2100");
define("PAGE_TWO_USER_1_FAN", "2103");
define("PAGE_TWO_USER_2_FAN", "2104");

define("PAGE_QUERY_ONE", "2050");
define("PAGE_QUERY_ONE_NAME", "CRAZYONLINE");
define("PAGE_QUERY_ONE_TYPE", "ONLINE_STORE");
define("PAGE_QUERY_FAN", "2110");
define("PAGE_QUERY_WASFAN", "2111");
define("PAGE_QUERY_NOTFAN", "2112");

define("PAGE_QUERY_TWO", "2051");
define("PAGE_QUERY_TWO_NAME", "BIG BEAUTY");
define("PAGE_QUERY_TWO_TYPE", "HEALTH_BEAUTY");

define("PAGE_QUERY_FIELD_NONE", 'none');
define("PAGE_QUERY_FIELD_FOUNDED", 'founded');
define("PAGE_QUERY_VALUE_FOUNDED", 'Early last year');
define("PAGE_QUERY_FIELD_WEBSITE", 'website');
define("PAGE_QUERY_VALUE_WEBSITE", 'http://mycrazystore.com/');
define("PAGE_QUERY_FIELD_OVERVIEW", 'company_overview');
define("PAGE_QUERY_VALUE_OVERVIEW", 'Crazy guys with crazy store');
define("PAGE_QUERY_FIELD_MISSION", 'mission');
define("PAGE_QUERY_VALUE_MISSION", 'A long mission with a nice vision');
define("PAGE_QUERY_FIELD_PRODUCTS", 'products');
define("PAGE_QUERY_VALUE_PRODUCTS", 'A b C and D');
define("PAGE_QUERY_FIELD_LOCATION", 'location');
define("PAGE_QUERY_VALUE_LOCATION", json_encode(array('street' => '1 chuck drive', 'city' => 'chucktown', 'country' => 'usa')));
define("PAGE_QUERY_FIELD_PARKING", 'parking');
define("PAGE_QUERY_VALUE_PARKING", json_encode(array('street' => false, 'lot' => true)));
define("PAGE_QUERY_FIELD_HOURS", 'hours');
define("PAGE_QUERY_VALUE_HOURS", json_encode(array('mon_1_open' => 1212312, 'mon_1_close' => 12123332)));

class PagesTestCase extends BaseDbTestCase
{
	
	public static function providerIsAppAdded()
	{
		return array(array(PAGE_ONE, PAGE_ONE_APP_1_ENABLED, true), array(PAGE_ONE, PAGE_ONE_APP_2_DISABLED, false), array(PAGE_TWO_NO_APPS, PAGE_ONE_APP_1_ENABLED, false));
	}
	
	/**
	 * @dataProvider providerIsAppAdded
	 */
	public function testIsAppAdded($pageId, $appId, $expected)
	{
		$actual = Api_Dao_Pages::hasApp($pageId, $appId);
		$this->assertEquals($expected, $actual);
	}
	
	public static function providerIsAppAddedForPages()
	{
		return array(array(PAGE_ONE, PAGE_ONE_APP_2_DISABLED, false), array(PAGE_THREE_NO_FRIENDS, PAGE_ONE_APP_2_DISABLED, true), array(PAGE_ONE, PAGE_ONE_APP_2_DISABLED, false), array(PAGE_THREE_NO_FRIENDS, PAGE_ONE_APP_2_DISABLED, true), array(2, PAGE_ONE_APP_2_DISABLED, false));
	}
	
	/**
	 * @dataProvider providerIsAppAddedForPages
	 */
	public function testIsAppAddedForPages($pageId, $appId, $expected)
	{
		$actual = Api_Dao_Pages::hasApp($pageId, $appId);
		$this->assertEquals($expected, $actual);
	}
	
	public function testIsAppAddedException()
	{
		$actual = Api_Dao_Pages::hasApp(- 1, 1);
		$this->assertFalse($actual);
	}
	
	public static function providerIsAdmin()
	{
		return array(array(PAGE_ONE, PAGE_ONE_USER_1_ADMIN_FAN, true), array(PAGE_ONE, PAGE_ONE_USER_1_ADMIN, true), array(PAGE_ONE, PAGE_ONE_USER_1_FAN, false));
	}
	
	/**
	 * @dataProvider providerIsAdmin
	 */
	public function testIsAdmin($pageId, $uid, $expected)
	{
		$actual = Api_Dao_Pages::isAdmin($pageId, $uid, RsOpenFBDbTestUtils::getDbCon());
		$this->assertEquals($expected, $actual);
	}
	
	public function testIsAdminException()
	{
		$actual = Api_Dao_Pages::isAdmin(- 1, 1);
		$this->assertFalse($actual);
	}
	
	public static function providerIsFan()
	{
		return array(array(PAGE_ONE, PAGE_ONE_USER_1_ADMIN_FAN, true), array(PAGE_ONE, PAGE_ONE_USER_1_ADMIN, false), array(PAGE_ONE, PAGE_ONE_USER_1_FAN, true));
	}
	
	/**
	 * @dataProvider providerIsFan
	 */
	public function testIsFan($pageId, $uid, $expected)
	{
		$actual = Api_Dao_Pages::isFan($pageId, $uid);
		$this->assertEquals($expected, $actual);
	}
	
	public function testIsFanException()
	{
		$actual = Api_Dao_Pages::isFan(- 1, 1);
		$this->assertFalse($actual);
	}
	
	public static function providerTestQueryByPage()
	{
		// test get page does not exist
		$tests [] = array(array(- 1), array(PAGE_QUERY_FIELD_NONE), array());
		
		// test get single page single field
		$testResults = array();
		$testResults [PAGE_QUERY_ONE_NAME] = array();
		$testResults [PAGE_QUERY_ONE_NAME] [PAGE_QUERY_FIELD_FOUNDED] = PAGE_QUERY_VALUE_FOUNDED;
		
		$tests [] = array(array(PAGE_QUERY_ONE), array(PAGE_QUERY_FIELD_FOUNDED), $testResults);
		
		// test get single page single field not there
		$testResults = array();
		$testResults [PAGE_QUERY_ONE_NAME] = array();
		$tests [] = array(array(PAGE_QUERY_ONE), array(PAGE_QUERY_FIELD_NONE), $testResults);
		
		// test get multi pages single field
		$testResults = array();
		$testResults [PAGE_QUERY_ONE_NAME] = array();
		$testResults [PAGE_QUERY_ONE_NAME] [PAGE_QUERY_FIELD_FOUNDED] = PAGE_QUERY_VALUE_FOUNDED;
		$testResults [PAGE_QUERY_TWO_NAME] = array();
		$testResults [PAGE_QUERY_TWO_NAME] [PAGE_QUERY_FIELD_FOUNDED] = PAGE_QUERY_VALUE_FOUNDED;
		$tests [] = array(array(PAGE_QUERY_ONE, PAGE_QUERY_TWO), array(PAGE_QUERY_FIELD_FOUNDED), $testResults);
		
		// test get mulit pages single field not there
		$testResults = array();
		$testResults [PAGE_QUERY_ONE_NAME] = array();
		$testResults [PAGE_QUERY_TWO_NAME] = array();
		$tests [] = array(array(PAGE_QUERY_ONE, PAGE_QUERY_TWO), array(PAGE_QUERY_FIELD_NONE), $testResults);
		
		// test get multi pages multi fields
		$testResults = array();
		$testResults [PAGE_QUERY_ONE_NAME] = array();
		$testResults [PAGE_QUERY_ONE_NAME] [PAGE_QUERY_FIELD_FOUNDED] = PAGE_QUERY_VALUE_FOUNDED;
		$testResults [PAGE_QUERY_ONE_NAME] [PAGE_QUERY_FIELD_WEBSITE] = PAGE_QUERY_VALUE_WEBSITE;
		$testResults [PAGE_QUERY_TWO_NAME] = array();
		$testResults [PAGE_QUERY_TWO_NAME] [PAGE_QUERY_FIELD_FOUNDED] = PAGE_QUERY_VALUE_FOUNDED;
		$testResults [PAGE_QUERY_TWO_NAME] [PAGE_QUERY_FIELD_LOCATION] = json_decode(PAGE_QUERY_VALUE_LOCATION, true);
		$tests [] = array(array(PAGE_QUERY_ONE, PAGE_QUERY_TWO), array(PAGE_QUERY_FIELD_FOUNDED, PAGE_QUERY_FIELD_WEBSITE, PAGE_QUERY_FIELD_LOCATION), $testResults);
		
		// test get multi pages multi fields some not there
		$testResults = array();
		$testResults [PAGE_QUERY_ONE_NAME] = array();
		$testResults [PAGE_QUERY_ONE_NAME] [PAGE_QUERY_FIELD_FOUNDED] = PAGE_QUERY_VALUE_FOUNDED;
		$testResults [PAGE_QUERY_ONE_NAME] [PAGE_QUERY_FIELD_WEBSITE] = PAGE_QUERY_VALUE_WEBSITE;
		$testResults [PAGE_QUERY_TWO_NAME] = array();
		$testResults [PAGE_QUERY_TWO_NAME] [PAGE_QUERY_FIELD_FOUNDED] = PAGE_QUERY_VALUE_FOUNDED;
		$testResults [PAGE_QUERY_TWO_NAME] [PAGE_QUERY_FIELD_LOCATION] = json_decode(PAGE_QUERY_VALUE_LOCATION, true);
		$tests [] = array(array(PAGE_QUERY_ONE, PAGE_QUERY_TWO), array(PAGE_QUERY_FIELD_NONE, PAGE_QUERY_FIELD_FOUNDED, PAGE_QUERY_FIELD_WEBSITE, PAGE_QUERY_FIELD_LOCATION), $testResults);
		
		// test get multi pages no pages exist
		$testResults = array();
		$tests [] = array(array(- 1, - 2), array(PAGE_QUERY_FIELD_FOUNDED), $testResults);
		
		// test get multi pages multi fields some not there with filter type one
		$testResults = array();
		$testResults [PAGE_QUERY_ONE_NAME] = array();
		$testResults [PAGE_QUERY_ONE_NAME] [PAGE_QUERY_FIELD_FOUNDED] = PAGE_QUERY_VALUE_FOUNDED;
		$testResults [PAGE_QUERY_ONE_NAME] [PAGE_QUERY_FIELD_WEBSITE] = PAGE_QUERY_VALUE_WEBSITE;
		$tests [] = array(array(PAGE_QUERY_ONE, PAGE_QUERY_TWO), array(PAGE_QUERY_FIELD_NONE, PAGE_QUERY_FIELD_FOUNDED, PAGE_QUERY_FIELD_WEBSITE, PAGE_QUERY_FIELD_LOCATION), $testResults);
		
		// test get multi pages multi fields some not there with filter type two
		$testResults = array();
		$testResults [PAGE_QUERY_TWO_NAME] = array();
		$testResults [PAGE_QUERY_TWO_NAME] [PAGE_QUERY_FIELD_FOUNDED] = PAGE_QUERY_VALUE_FOUNDED;
		$testResults [PAGE_QUERY_TWO_NAME] [PAGE_QUERY_FIELD_LOCATION] = json_decode(PAGE_QUERY_VALUE_LOCATION, true);
		$tests [] = array(array(PAGE_QUERY_ONE, PAGE_QUERY_TWO), array(PAGE_QUERY_FIELD_NONE, PAGE_QUERY_FIELD_FOUNDED, PAGE_QUERY_FIELD_WEBSITE, PAGE_QUERY_FIELD_LOCATION), $testResults);
		
		// test get multi pages multi fields some not there with filter unkown
		$testResults = array();
		$tests [] = array(array(PAGE_QUERY_ONE, PAGE_QUERY_TWO), array(PAGE_QUERY_FIELD_NONE, PAGE_QUERY_FIELD_FOUNDED, PAGE_QUERY_FIELD_WEBSITE, PAGE_QUERY_FIELD_LOCATION), $testResults);
		
		return $tests;
	
	}
	
	/**
	 * @dataProvider providerTestQueryByPage
	 *
	 */
	public function testQueryByPage($pageIds, $fields, $expected)
	{
		$pages = Api_Dao_Pages::getPagesByIds($pageIds, $fields);
		$this->compareQueryResults($pages, $expected);
	}
	
	public static function providerTestQueryByFan()
	{
		// test each parameters as blanks and/or invalid
		$tests [] = array(- 1, array('test'), null, array());
		$tests [] = array(- 1, array('unknown'), null, array());
		$tests [] = array(- 1, array('unknown'), array(- 1), array());
		$tests [] = array(- 1, array('unknown'), array(- 1), array());
		
		// test user is a fan and real field and filter by multiple page ids
		// test user is a fan and real field and filter by multiple page ids som real
		// test user is a fan and real field and filter by multiple page ids all fake
		

		// test user is a fan and real field
		$testResults = array();
		$testResults [PAGE_QUERY_ONE_NAME] = array();
		$testResults [PAGE_QUERY_ONE_NAME] [PAGE_QUERY_FIELD_FOUNDED] = PAGE_QUERY_VALUE_FOUNDED;
		$testResults [PAGE_QUERY_TWO_NAME] = array();
		$testResults [PAGE_QUERY_TWO_NAME] [PAGE_QUERY_FIELD_FOUNDED] = PAGE_QUERY_VALUE_FOUNDED;
		$tests [] = array(PAGE_QUERY_FAN, array(PAGE_QUERY_FIELD_FOUNDED), null, $testResults);
		
		// test user was a fan, but no longer is.
		$testResults = array();
		$tests [] = array(PAGE_QUERY_WASFAN, array(PAGE_QUERY_FIELD_FOUNDED), null, $testResults);
		
		// test user was never a fan.
		$testResults = array();
		$tests [] = array(PAGE_QUERY_NOTFAN, array(PAGE_QUERY_FIELD_FOUNDED), null, $testResults);
		
		// test user is a fan and fake field
		$testResults = array();
		$testResults [PAGE_QUERY_ONE_NAME] = array();
		$testResults [PAGE_QUERY_TWO_NAME] = array();
		$tests [] = array(PAGE_QUERY_FAN, array(PAGE_QUERY_FIELD_NONE), null, $testResults);
		
		// test user is a fan and real field and type is real
		$testResults = array();
		$testResults [PAGE_QUERY_ONE_NAME] = array();
		$testResults [PAGE_QUERY_ONE_NAME] [PAGE_QUERY_FIELD_FOUNDED] = PAGE_QUERY_VALUE_FOUNDED;
		$tests [] = array(PAGE_QUERY_FAN, array(PAGE_QUERY_FIELD_FOUNDED), null, $testResults);
		
		// test user is a fan and real field and type is fake
		$testResults = array();
		$tests [] = array(PAGE_QUERY_FAN, array(PAGE_QUERY_FIELD_FOUNDED), null, $testResults);
		
		// test user is a fan and real field and filter by one page id does not exist
		$testResults = array();
		$tests [] = array(PAGE_QUERY_FAN, array(PAGE_QUERY_FIELD_FOUNDED), array(- 1), $testResults);
		
		// test user is a fan and real field and filter by one page id does
		$testResults = array();
		$testResults [PAGE_QUERY_ONE_NAME] = array();
		$testResults [PAGE_QUERY_ONE_NAME] [PAGE_QUERY_FIELD_FOUNDED] = PAGE_QUERY_VALUE_FOUNDED;
		$tests [] = array(PAGE_QUERY_FAN, array(PAGE_QUERY_FIELD_FOUNDED), array(PAGE_QUERY_ONE), $testResults);
		
		// test user is a fan and real field and filter by multiple page id does
		$testResults = array();
		$testResults [PAGE_QUERY_ONE_NAME] = array();
		$testResults [PAGE_QUERY_ONE_NAME] [PAGE_QUERY_FIELD_FOUNDED] = PAGE_QUERY_VALUE_FOUNDED;
		$testResults [PAGE_QUERY_TWO_NAME] = array();
		$testResults [PAGE_QUERY_TWO_NAME] [PAGE_QUERY_FIELD_FOUNDED] = PAGE_QUERY_VALUE_FOUNDED;
		$tests [] = array(PAGE_QUERY_FAN, array(PAGE_QUERY_FIELD_FOUNDED), array(PAGE_QUERY_ONE, PAGE_QUERY_TWO), $testResults);
		
		// test user is a fan and real field and filter by multiple page id but user is not fan of all
		$testResults = array();
		$testResults [PAGE_QUERY_ONE_NAME] = array();
		$testResults [PAGE_QUERY_ONE_NAME] [PAGE_QUERY_FIELD_FOUNDED] = PAGE_QUERY_VALUE_FOUNDED;
		$testResults [PAGE_QUERY_TWO_NAME] = array();
		$testResults [PAGE_QUERY_TWO_NAME] [PAGE_QUERY_FIELD_FOUNDED] = PAGE_QUERY_VALUE_FOUNDED;
		$tests [] = array(PAGE_QUERY_FAN, array(PAGE_QUERY_FIELD_FOUNDED), array(PAGE_QUERY_ONE, PAGE_ONE, PAGE_QUERY_TWO), $testResults);
		
		return $tests;
	
	}
	
	/**
	 * @dataProvider providerTestQueryByFan
	 */
	public function testQueryByFan($uid, $fields, $pageIds, $expected)
	{
		$pages = null;
		if(null == $pages)
		{
			$pages = Api_Dao_Pages::getPagesByUid($uid, $fields);
		}else
		{
			$pages = Api_Dao_Pages::getPagesByUidAndPageIds($uid, $fields, $pageIds);
		}
		
		$this->assertNotNull($pages, "testQueryByFan test case failed!  Pages was null for params: uid = $uid, fields = $fields, pageIds = $pageIds");
		$this->compareQueryResults($pages, $expected);
	}
	
	public function compareQueryResults($pages, $expected)
	{
		foreach($pages as $page)
		{
			if(array_key_exists($page->name, $expected))
			{
				$this->assertArrayHasKey($page->name, $expected, $page->name . ' retrieved from getPagesByIds, but not in expected results set!');
				$sub = $expected [$page->name];
				foreach($page->RingsidePagesInfo as $pi)
				{
					if(array_key_exists($pi->name, $sub))
					{
						$this->assertArrayHasKey($pi->name, $sub, $pi->name . ' retrieved from getPagesByIds, but not in expected results set!');
						$eValue = $sub [$pi->name];
						$value = null;
						if($pi->json_encoded == 1)
						{
							$value = json_decode($pi->value, true);
						}else
						{
							$value = $pi->value;
						}
						
						$this->assertEquals($eValue, $value, "$eValue from expcted is not equal to $value from page!");
					}
				}
			}
		}
	}
}
?>
