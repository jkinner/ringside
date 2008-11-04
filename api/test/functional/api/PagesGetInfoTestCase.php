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
require_once ('BaseAPITestCase.php');
require_once ("ringside/api/OpenFBAPIException.php");
require_once ("ringside/api/facebook/PagesGetInfo.php");
define('PAGES_GETINFO_NO_SUCH_USER', '2999');
define("PAGE_GETINFO_ADMINFAN", "2011");
define("PAGE_GETINFO_FANOF_3_FRIEND", "2012");
define("PAGE_GETINFO_FANOF_1AND2_NOTFRIEND", "2013");
define("PAGE_GETINFO_NEVERFAN", "2014");
define("PAGE_GETINFO_1", "2050");
define("PAGE_GETINFO_1_NAME", "CRAZYONLINE");
define("PAGE_GETINFO_1_TYPE", "ONLINE_STORE");
define("PAGE_GETINFO_1_HASAPP", 1);
define("PAGE_GETINFO_2", "2051");
define("PAGE_GETINFO_2_NAME", "BIG BEAUTY");
define("PAGE_GETINFO_2_TYPE", "HEALTH_BEAUTY");
define("PAGE_GETINFO_2_HASAPP", 1);
define("PAGE_GETINFO_3", "2052");
define("PAGE_GETINFO_3_NAME", "ROCK ON");
define("PAGE_GETINFO_3_TYPE", "BAND");
define("PAGE_GETINFO_3_HASAPP", 0);
define("PAGE_GETINFO_VALUE_PIC", 'http://www.picit.com/image1.jpg');
define("PAGE_GETINFO_FIELD_NONE", 'none');
define("PAGE_GETINFO_FIELD_TYPE", 'type');
define("PAGE_GETINFO_FIELD_APP", 'has_added_app');
define("PAGE_GETINFO_FIELD_NAME", 'name');
define("PAGE_GETINFO_FIELD_PAGEID", 'page_id');
define("PAGE_GETINFO_FIELD_FOUNDED", 'founded');
define("PAGE_GETINFO_VALUE_FOUNDED", 'Early last year');
define("PAGE_GETINFO_FIELD_WEBSITE", 'website');
define("PAGE_GETINFO_VALUE_WEBSITE", 'http://mycrazystore.com/');
define("PAGE_GETINFO_FIELD_OVERVIEW", 'company_overview');
define("PAGE_GETINFO_VALUE_OVERVIEW", 'Crazy guys with crazy store');
define("PAGE_GETINFO_FIELD_MISSION", 'mission');
define("PAGE_GETINFO_VALUE_MISSION", 'A long mission with a nice vision');
define("PAGE_GETINFO_FIELD_PRODUCTS", 'products');
define("PAGE_GETINFO_VALUE_PRODUCTS", 'A b C and D');
define("PAGE_GETINFO_FIELD_LOCATION", 'location');
define("PAGE_GETINFO_VALUE_LOCATION", json_encode(array('street' => '1 chuck drive', 'city' => 'chucktown', 'country' => 'usa')));
define("PAGE_GETINFO_FIELD_PARKING", 'parking');
define("PAGE_GETINFO_VALUE_PARKING", json_encode(array('street' => false, 'lot' => true)));
define("PAGE_GETINFO_FIELD_HOURS", 'hours');
define("PAGE_GETINFO_VALUE_HOURS", json_encode(array('mon_1_open' => 1212312, 'mon_1_close' => 12123332)));
class PagesGetInfoTestCase extends BaseAPITestCase
{
	private $appId = 2401;
	public static function providerTestConstructor()
	{
		return array(array(PAGES_GETINFO_NO_SUCH_USER, array(), false), array(PAGES_GETINFO_NO_SUCH_USER, array("fields" => "name"), true), array(PAGES_GETINFO_NO_SUCH_USER, array("fields" => "name,type,test"), true), array(PAGES_GETINFO_NO_SUCH_USER, array("fields" => "name,type,test", "page_ids" => "1234"), true), array(PAGES_GETINFO_NO_SUCH_USER, array("fields" => "name,type,test", "page_ids" => "1234,52342"), true), array(PAGES_GETINFO_NO_SUCH_USER, array("fields" => "name,type,test", "uid" => "939393"), true), array(PAGES_GETINFO_NO_SUCH_USER, array("fields" => "name,type,test", "type" => "ALPHA"), true), array(PAGES_GETINFO_NO_SUCH_USER, array("fields" => "name,type,test", "page_ids" => "1234,52342", "uid" => "12314", "type" => "alpha"), true));
	}
	/**
	 * @dataProvider providerTestConstructor
	 */
	public function testConstructor($uid, $params, $pass)
	{
		try
		{
			$notif = $this->initRest(new PagesGetInfo(), $params, $uid, $this->appId);
			$this->assertTrue($pass, "This test should have failed with exception!");
			$this->assertNotNull($notif, "Object missing!");
		}catch(OpenFBAPIException $exception)
		{
			$this->assertFalse($pass, "This test should have not thrown an exception! " . $exception);
			$this->assertEquals(FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode());
		}
	}
	public static function providerTestExecute()
	{
		// test just empty results some through page id filter and some through uid filter (either uid empty or page_ids empty)
		$tests[] = array(PAGES_GETINFO_NO_SUCH_USER, array("fields" => "name,type,test", "page_ids" => "1234,52342", "uid" => "12314", "type" => "alpha"), array());
		$tests[] = array(PAGES_GETINFO_NO_SUCH_USER, array("fields" => "name,type,test", "page_ids" => "1234,52342", "uid" => "12314"), array());
		$tests[] = array(PAGES_GETINFO_NO_SUCH_USER, array("fields" => "name,type,test", "page_ids" => "1234,52342"), array());
		$tests[] = array(PAGES_GETINFO_NO_SUCH_USER, array("fields" => "name,type,test", "uid" => "12314", "type" => "alpha"), array());
		$tests[] = array(PAGES_GETINFO_NO_SUCH_USER, array("fields" => "name,type,test", "uid" => "12314"), array());
		$tests[] = array(PAGES_GETINFO_NO_SUCH_USER, array("fields" => "name,type,test"), array());
		// test results which lead through page_ids, uid parameter must be empty and fields and pages_ids must be set. type can be turned on here as well.
		// for each one test user as fan, not fan, never fan - this should have ZERO impact for testing through page_ids
		$testResults = array();
		$testResults[PAGE_GETINFO_1] = array();
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_1;
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_FOUNDED] = PAGE_GETINFO_VALUE_FOUNDED;
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_NAME] = PAGE_GETINFO_1_NAME;
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_WEBSITE] = PAGE_GETINFO_VALUE_WEBSITE;
		$tests[] = array(PAGE_GETINFO_ADMINFAN, array("fields" => "name,founded,website", "page_ids" => PAGE_GETINFO_1, "type" => PAGE_GETINFO_1_TYPE), $testResults);
		$testResults = array();
		$testResults[PAGE_GETINFO_1] = array();
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_1;
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_FOUNDED] = PAGE_GETINFO_VALUE_FOUNDED;
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_WEBSITE] = PAGE_GETINFO_VALUE_WEBSITE;
		$testResults[PAGE_GETINFO_1]['yikes'] = '';
		$tests[] = array(PAGE_GETINFO_ADMINFAN, array("fields" => "founded,yikes,website", "page_ids" => PAGE_GETINFO_1, "type" => PAGE_GETINFO_1_TYPE), $testResults);
		$testResults = array();
		$testResults[PAGE_GETINFO_1] = array();
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_1;
		$testResults[PAGE_GETINFO_1]['yikes'] = '';
		$tests[] = array(PAGE_GETINFO_ADMINFAN, array("fields" => "yikes", "page_ids" => PAGE_GETINFO_1, "type" => PAGE_GETINFO_1_TYPE), $testResults);
		$testResults = array();
		$testResults[PAGE_GETINFO_1] = array();
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_1;
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_WEBSITE] = PAGE_GETINFO_VALUE_WEBSITE;
		$testResults[PAGE_GETINFO_2] = array();
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_2;
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_WEBSITE] = '';
		$tests[] = array(PAGE_GETINFO_ADMINFAN, array("fields" => "website", "page_ids" => PAGE_GETINFO_1 . "," . PAGE_GETINFO_2), $testResults);
		$testResults = array();
		$testResults[PAGE_GETINFO_1] = array();
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_1;
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_WEBSITE] = PAGE_GETINFO_VALUE_WEBSITE;
		$testResults[PAGE_GETINFO_2] = array();
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_2;
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_WEBSITE] = '';
		$tests[] = array(PAGE_GETINFO_ADMINFAN, array("fields" => "website", "page_ids" => PAGE_GETINFO_1 . "," . PAGE_GETINFO_2 . ",2345"), $testResults);
		$testResults = array();
		$testResults[PAGE_GETINFO_1] = array();
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_1;
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_WEBSITE] = PAGE_GETINFO_VALUE_WEBSITE;
		$testResults[PAGE_GETINFO_1]['pic_big'] = PAGE_GETINFO_VALUE_PIC;
		$testResults[PAGE_GETINFO_1]['pic_small'] = PAGE_GETINFO_VALUE_PIC;
		$testResults[PAGE_GETINFO_1]['pic'] = PAGE_GETINFO_VALUE_PIC;
		$testResults[PAGE_GETINFO_1]['pic_square'] = PAGE_GETINFO_VALUE_PIC;
		$testResults[PAGE_GETINFO_1]['pic_large'] = PAGE_GETINFO_VALUE_PIC;
		$tests[] = array(PAGE_GETINFO_ADMINFAN, array("fields" => "website,pic,pic_big,pic_small,pic_large,pic_square", "page_ids" => PAGE_GETINFO_1 . ",2345"), $testResults);
		$testResults = array();
		$testResults[PAGE_GETINFO_1] = array();
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_1;
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_WEBSITE] = PAGE_GETINFO_VALUE_WEBSITE;
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_TYPE] = PAGE_GETINFO_1_TYPE;
		$testResults[PAGE_GETINFO_2] = array();
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_2;
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_WEBSITE] = '';
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_TYPE] = PAGE_GETINFO_2_TYPE;
		$tests[] = array(PAGE_GETINFO_ADMINFAN, array("fields" => "website,type", "page_ids" => PAGE_GETINFO_1 . "," . PAGE_GETINFO_2), $testResults);
		$testResults = array();
		$testResults[PAGE_GETINFO_1] = array();
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_1;
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_WEBSITE] = PAGE_GETINFO_VALUE_WEBSITE;
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_APP] = PAGE_GETINFO_1_HASAPP;
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_TYPE] = PAGE_GETINFO_1_TYPE;
		$testResults[PAGE_GETINFO_2] = array();
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_2;
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_WEBSITE] = '';
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_APP] = PAGE_GETINFO_2_HASAPP;
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_TYPE] = PAGE_GETINFO_2_TYPE;
		$testResults[PAGE_GETINFO_3] = array();
		$testResults[PAGE_GETINFO_3][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_3;
		$testResults[PAGE_GETINFO_3][PAGE_GETINFO_FIELD_WEBSITE] = '';
		$testResults[PAGE_GETINFO_3][PAGE_GETINFO_FIELD_APP] = PAGE_GETINFO_3_HASAPP;
		$testResults[PAGE_GETINFO_3][PAGE_GETINFO_FIELD_TYPE] = PAGE_GETINFO_3_TYPE;
		$tests[] = array(PAGE_GETINFO_ADMINFAN, array("fields" => "website,type,has_added_app", "page_ids" => PAGE_GETINFO_1 . "," . PAGE_GETINFO_2 . "," . PAGE_GETINFO_3), $testResults);
		$testResults = array();
		$testResults[PAGE_GETINFO_1] = array();
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_1;
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_LOCATION] = '';
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_PARKING] = '';
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_TYPE] = PAGE_GETINFO_1_TYPE;
		$testResults[PAGE_GETINFO_2] = array();
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_2;
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_LOCATION] = json_decode(PAGE_GETINFO_VALUE_LOCATION, true);
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_PARKING] = json_decode(PAGE_GETINFO_VALUE_PARKING, true);
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_TYPE] = PAGE_GETINFO_2_TYPE;
		$tests[] = array(PAGE_GETINFO_ADMINFAN, array("fields" => "location,type,parking", "page_ids" => PAGE_GETINFO_1 . "," . PAGE_GETINFO_2), $testResults);
		/* Test without page_ids set and without uid, but rather retrieve calling users pages.  Type filter can be specified. */
		$testResults = array();
		$testResults[PAGE_GETINFO_1] = array();
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_1;
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_NAME] = PAGE_GETINFO_1_NAME;
		$testResults[PAGE_GETINFO_1]['pic'] = PAGE_GETINFO_VALUE_PIC;
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_TYPE] = PAGE_GETINFO_1_TYPE;
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_APP] = PAGE_GETINFO_1_HASAPP;
		$testResults[PAGE_GETINFO_1]['widget'] = '';
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_LOCATION] = '';
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_PARKING] = '';
		$testResults[PAGE_GETINFO_2] = array();
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_2;
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_NAME] = PAGE_GETINFO_2_NAME;
		$testResults[PAGE_GETINFO_2]['pic'] = PAGE_GETINFO_VALUE_PIC;
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_TYPE] = PAGE_GETINFO_2_TYPE;
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_APP] = PAGE_GETINFO_2_HASAPP;
		$testResults[PAGE_GETINFO_2]['widget'] = '';
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_LOCATION] = json_decode(PAGE_GETINFO_VALUE_LOCATION, true);
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_PARKING] = json_decode(PAGE_GETINFO_VALUE_PARKING, true);
		$testResults[PAGE_GETINFO_3] = array();
		$testResults[PAGE_GETINFO_3][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_3;
		$testResults[PAGE_GETINFO_3][PAGE_GETINFO_FIELD_NAME] = PAGE_GETINFO_3_NAME;
		$testResults[PAGE_GETINFO_3]['pic'] = PAGE_GETINFO_VALUE_PIC;
		$testResults[PAGE_GETINFO_3][PAGE_GETINFO_FIELD_TYPE] = PAGE_GETINFO_3_TYPE;
		$testResults[PAGE_GETINFO_3][PAGE_GETINFO_FIELD_APP] = PAGE_GETINFO_3_HASAPP;
		$testResults[PAGE_GETINFO_3]['widget'] = '';
		$testResults[PAGE_GETINFO_3][PAGE_GETINFO_FIELD_LOCATION] = json_decode(PAGE_GETINFO_VALUE_LOCATION, true);
		$testResults[PAGE_GETINFO_3][PAGE_GETINFO_FIELD_PARKING] = json_decode(PAGE_GETINFO_VALUE_PARKING, true);
		$tests[] = array(PAGE_GETINFO_ADMINFAN, array("fields" => "name,pic,type,has_added_app,widget,location,parking"), $testResults);
		/* Test with UID and filters pages_id and type alternately - test that uid= and UID active are friends/not friends */
		$testResults = array();
		$testResults[PAGE_GETINFO_3] = array();
		$testResults[PAGE_GETINFO_3][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_3;
		$testResults[PAGE_GETINFO_3][PAGE_GETINFO_FIELD_NAME] = PAGE_GETINFO_3_NAME;
		$testResults[PAGE_GETINFO_3][PAGE_GETINFO_FIELD_WEBSITE] = '';
		$tests[] = array(PAGE_GETINFO_ADMINFAN, array("fields" => "name,website", "uid" => PAGE_GETINFO_FANOF_3_FRIEND), $testResults);
		$testResults = array();
		$testResults[PAGE_GETINFO_3] = array();
		$testResults[PAGE_GETINFO_3][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_3;
		$testResults[PAGE_GETINFO_3][PAGE_GETINFO_FIELD_NAME] = PAGE_GETINFO_3_NAME;
		$testResults[PAGE_GETINFO_3][PAGE_GETINFO_FIELD_WEBSITE] = '';
		$tests[] = array(PAGE_GETINFO_ADMINFAN, array("fields" => "name,website", "page_ids" => PAGE_GETINFO_3, "uid" => PAGE_GETINFO_FANOF_3_FRIEND), $testResults);
		$testResults = array();
		$tests[] = array(PAGE_GETINFO_ADMINFAN, array("fields" => "name,website", "page_ids" => PAGE_GETINFO_2, "uid" => PAGE_GETINFO_FANOF_3_FRIEND), $testResults);
		$testResults = array();
		$testResults[PAGE_GETINFO_3] = array();
		$testResults[PAGE_GETINFO_3][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_3;
		$testResults[PAGE_GETINFO_3][PAGE_GETINFO_FIELD_NAME] = PAGE_GETINFO_3_NAME;
		$testResults[PAGE_GETINFO_3][PAGE_GETINFO_FIELD_WEBSITE] = '';
		$tests[] = array(PAGE_GETINFO_ADMINFAN, array("fields" => "name,website", "page_ids" => PAGE_GETINFO_3 . "," . PAGE_GETINFO_2, "uid" => PAGE_GETINFO_FANOF_3_FRIEND), $testResults);
		$testResults = array();
		$testResults[PAGE_GETINFO_3] = array();
		$testResults[PAGE_GETINFO_3][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_3;
		$testResults[PAGE_GETINFO_3][PAGE_GETINFO_FIELD_NAME] = PAGE_GETINFO_3_NAME;
		$testResults[PAGE_GETINFO_3][PAGE_GETINFO_FIELD_WEBSITE] = '';
		$tests[] = array(PAGE_GETINFO_ADMINFAN, array("fields" => "name,website", "page_ids" => PAGE_GETINFO_3 . "," . PAGE_GETINFO_2, "uid" => PAGE_GETINFO_FANOF_3_FRIEND, "type" => PAGE_GETINFO_3_TYPE), $testResults);
		$testResults = array();
		$testResults[PAGE_GETINFO_1] = array();
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_1;
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_NAME] = PAGE_GETINFO_1_NAME;
		$testResults[PAGE_GETINFO_1][PAGE_GETINFO_FIELD_PARKING] = '';
		$testResults[PAGE_GETINFO_2] = array();
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_PAGEID] = PAGE_GETINFO_2;
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_NAME] = PAGE_GETINFO_2_NAME;
		$testResults[PAGE_GETINFO_2][PAGE_GETINFO_FIELD_PARKING] = json_decode(PAGE_GETINFO_VALUE_PARKING, true);
		$tests[] = array(PAGE_GETINFO_ADMINFAN, array("fields" => "name,parking", "uid" => PAGE_GETINFO_FANOF_1AND2_NOTFRIEND), $testResults);
		return $tests;
	}
	/**
	 * @dataProvider providerTestExecute
	 */
	public function testExecute($uid, $params, $expected)
	{
		// pass in uid
		try
		{
			$method = $this->initRest(new PagesGetInfo(), $params, $uid, $this->appId);
			$actual = $method->execute();
			if(count($expected) == 0)
			{
				$this->assertEquals(count($expected), count($actual), 'Results should have been empty');
			}else
			{
				$this->assertArrayHasKey('page', $actual, "Page array does not have a page entry: " . count($actual));
				$this->assertEquals(count($expected), count($actual['page']), 'Returned result has different #results than expected.');
				foreach($actual['page'] as $page)
				{
					$this->assertArrayHasKey('page_id', $page, "Page does not have page_id");
					$this->assertArrayHasKey($page['page_id'], $expected);
					$expectedPage = $expected[$page['page_id']];
					foreach($page as $key=>$value)
					{
						$this->assertArrayHasKey($key, $expectedPage, 'Actual has result not specified in expected');
						if(is_array($value))
						{
							$this->assertTrue(is_array($expectedPage[$key]), "failed for $key and page " . $page['page_id']);
							// every value in actual matches expected values
							foreach($value as $fKey=>$fValue)
							{
								$this->assertEquals($expectedPage[$key][$fKey], $fValue, "Actual value does not match expected for key $key -> $fKey for " . $page['page_id']);
							}
							// every value in expected is in actual (find missing keys)
							foreach($expectedPage[$key] as $eKey=>$eValue)
							{
								$this->assertArrayHasKey($eKey, $value, 'Expected has result not specified in actual');
							}
						}else
						{
							$this->assertEquals($expectedPage[$key], $value, "Actual value does not match expected for key $key for " . $page['page_id']);
						}
					}
					foreach($expectedPage as $key=>$value)
					{
						$this->assertArrayHasKey($key, $page, 'Expected has result not specified in actual');
					}
				}
			}
		}catch(OpenFBAPIException $exception)
		{
			$this->fail("Should not have thrown exception " . $exception);
		}
	}
}
?>
