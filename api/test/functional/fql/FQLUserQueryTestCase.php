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

require_once("BaseFQLTestCase.php");

class FQLUserQueryTestCase extends BaseFQLTestCase
{
	public function testQuery()
	{
		//just tests the execution, both the api-tests and client-tests thoroughly
		//test the values of the response
		$flds = array("uid", "about_me", "activities", "affiliations", "books", "birthday",
						  "first_name", "interests", "last_name", "education_history", "movies",
						  "music", "pic", "political", "quotes", "relationship_status",
						  "profile_update_time", "religion", "sex", "significant_other_id",
						  "timezone", "tv", "current_location", "is_app_user", "has_added_app",
						  "hometown_location","meeting_for","meeting_sex","pic_big","pic_small",
						  "pic_square","profile_update_time","status","name","work_history",
							"wall_count","notes_count");
		
		$uid = 17001;
		$fql = "SELECT " . implode(",", $flds) . " FROM user WHERE uid=$uid";		
		
		$resp = $this->m_fqlEngine->query(17100, $uid, $fql);
		
		$this->assertTrue(array_key_exists("user", $resp));
		$this->assertEquals(1, count($resp["user"]));
		$this->assertTrue(array_key_exists("uid", $resp["user"][0]));
		$this->assertEquals($uid, $resp["user"][0]["uid"]);
	}
	
	public function testMultipleQuery()
	{
		$flds = array("uid", "about_me", "activities", "affiliations", "books", "birthday",
						  "first_name", "interests", "last_name", "education_history", "movies",
						  "music", "pic", "political", "quotes", "relationship_status",
						  "profile_update_time", "religion", "sex", "significant_other_id",
						  "timezone", "tv", "current_location", "is_app_user", "has_added_app",
						  "hometown_location","meeting_for","meeting_sex","pic_big","pic_small",
						  "pic_square","profile_update_time","status","name","work_history",
							"wall_count","notes_count");
		
		$uid1 = 17001;
		$uid2 = 17002;
		$fql = "SELECT " . implode(",", $flds) . " FROM user WHERE uid IN ($uid1, $uid2)";		
		
		$resp = $this->m_fqlEngine->query(17100, $uid1, $fql);
		
		$this->assertTrue(array_key_exists("user", $resp));
		$this->assertEquals(2, count($resp["user"]));
		
		$this->assertTrue(array_key_exists("uid", $resp["user"][0]));
		$this->assertEquals($uid1, $resp["user"][0]["uid"]);
		
		$this->assertTrue(array_key_exists("uid", $resp["user"][1]));
		$this->assertEquals($uid2, $resp["user"][1]["uid"]);
		
		//print_r($resp);		
	}
	
	public function testNestedQuery1()
	{
		$uid = 17001;
		$fql = "SELECT uid FROM user WHERE uid IN (SELECT uid FROM user WHERE uid=$uid)";
		$resp = $this->m_fqlEngine->query(17100, $uid, $fql);
		
		$this->assertTrue(array_key_exists("user", $resp));
		$this->assertEquals(1, count($resp["user"]));
		
		$this->assertTrue(array_key_exists("uid", $resp["user"][0]));
		$this->assertEquals($uid, $resp["user"][0]["uid"]);
	}
	
	public function testNestedQuery2()
	{
		$uid = 17001;
		$fuid1 = 17002;
		$fuid2 = 17003;
		$fuid3 = 17004;
		
		$fql = "SELECT uid FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1=$uid)";
		$resp = $this->m_fqlEngine->query(17100, $uid, $fql);
		
		$this->assertTrue(array_key_exists("user", $resp));
		$this->assertEquals(3, count($resp["user"]));
		
		$this->assertTrue(array_key_exists("uid", $resp["user"][0]));
		$this->assertEquals($fuid1, $resp["user"][0]["uid"]);		
		$this->assertTrue(array_key_exists("uid", $resp["user"][1]));
		$this->assertEquals($fuid2, $resp["user"][1]["uid"]);		
		$this->assertTrue(array_key_exists("uid", $resp["user"][2]));
		$this->assertEquals($fuid3, $resp["user"][2]["uid"]);
	}
	
	public function testDotNotationSelect()
	{
		$uid = 17001;
		
		$nflds = array("about_me", "activities", "books", "birthday",
						   "first_name", "interests", "last_name", "movies",
						   "music", "pic", "political", "quotes", "relationship_status",
						   "profile_update_time", "religion", "sex", "significant_other_id",
						   "timezone", "tv", "current_location", "is_app_user", "has_added_app",
						   "hometown_location","meeting_for","meeting_sex","pic_big","pic_small",
						   "pic_square","profile_update_time","name","wall_count","notes_count");
		
		$flds = array("uid","status.message","affiliations.name","work_history.location.city",
						  "work_history.company_name","work_history.location.state",
						  "education_history.name","education_history.concentrations");
		$fql = "SELECT " . implode(",", $flds) . " FROM user WHERE uid=$uid";
		$resp = $this->m_fqlEngine->query(17100, $uid, $fql);
		
		$this->assertTrue(array_key_exists("user", $resp));
		$this->assertEquals(1, count($resp["user"]));
		$this->assertTrue(array_key_exists("uid", $resp["user"][0]));
		
		$this->assertEquals($uid, $resp["user"][0]["uid"]);
		
		$this->assertEquals("CooCoo for Coco Puffs", $resp["user"][0]["status"]["message"]);
		$this->assertTrue(!array_key_exists("time", $resp["user"][0]["status"]));
		
		$this->assertEquals(2, count($resp["user"][0]["work_history"]["work_info"]));
		
		$w = $resp["user"][0]["work_history"]["work_info"][0];
		$this->assertTrue(!array_key_exists("position", $w));
		$this->assertTrue(!array_key_exists("description", $w));
		$this->assertTrue(!array_key_exists("start_date", $w));
		$this->assertTrue(!array_key_exists("end_date", $w));
		$this->assertEquals("Spacely Sprockets", $w["company_name"]);
		$this->assertEquals("Hairydelphia", $w["location"]["city"]);
		$this->assertEquals("PA", $w["location"]["state"]);
		
		$w = $resp["user"][0]["work_history"]["work_info"][1];
		$this->assertTrue(!array_key_exists("position", $w));
		$this->assertTrue(!array_key_exists("description", $w));
		$this->assertTrue(!array_key_exists("start_date", $w));
		$this->assertTrue(!array_key_exists("end_date", $w));
		$this->assertEquals("McDonalds", $w["company_name"]);
		$this->assertEquals("New York", $w["location"]["city"]);
		$this->assertEquals("NY", $w["location"]["state"]);
		
		$this->assertEquals(2, count($resp["user"][0]["education_history"]["education_info"]));
		
		$e = $resp["user"][0]["education_history"]["education_info"][0];
		$this->assertTrue(!array_key_exists("year", $e));
		$this->assertEquals("Temple University", $e["name"]);		
		$this->assertEquals(2, count($e["concentrations"]["concentration"]));		
		$this->assertEquals("Communications", $e["concentrations"]["concentration"][0]);
		$this->assertEquals("Philosophy", $e["concentrations"]["concentration"][1]);
		
		$e = $resp["user"][0]["education_history"]["education_info"][1];
		$this->assertTrue(!array_key_exists("year", $e));
		$this->assertEquals("Georgia Tech", $e["name"]);		
		$this->assertEquals(2, count($e["concentrations"]["concentration"]));		
		$this->assertEquals("Rocket Science", $e["concentrations"]["concentration"][0]);
		$this->assertEquals("Awesomeness", $e["concentrations"]["concentration"][1]);
		
		$this->assertEquals(3, count($resp["user"][0]["affiliations"]["affiliation"]));
		
		$a = $resp["user"][0]["affiliations"]["affiliation"][0];
		$this->assertTrue(!array_key_exists("nid", $a));
		$this->assertEquals("Philadelphia", $a["name"]);
		
		$a = $resp["user"][0]["affiliations"]["affiliation"][1];
		$this->assertTrue(!array_key_exists("nid", $a));
		$this->assertEquals("Arts and Crafts", $a["name"]);
		
		$a = $resp["user"][0]["affiliations"]["affiliation"][2];
		$this->assertTrue(!array_key_exists("nid", $a));
		$this->assertEquals("Northeast High", $a["name"]);

		foreach ($nflds as $badfield) {
			$this->assertTrue(!array_key_exists($badfield, $resp["user"][0]));
		}
	}
	
	public function testDotNotationWhere()
	{	
		$uid = 17001;
		$fql = "SELECT uid,affiliations FROM user WHERE uid IN (17001, 17002, 17003) " .
				 	"AND affiliations.name='Philadelphia'";
		$resp = $this->m_fqlEngine->query(17100, $uid, $fql);
	
		$this->assertTrue(array_key_exists("user", $resp));
		$this->assertEquals(1, count($resp["user"]));
		$this->assertTrue(array_key_exists("uid", $resp["user"][0]));
		
		$this->assertEquals($uid, $resp["user"][0]["uid"]);
	}
	
	public function testSelectFunction()
	{
		$uid = 17001;
		
		$funcs = array("concat(\"prefix-\", uid, \"-suffix\")",
							"concat(\"message:\",status.message)",
							"now()", "rand()", "strlen(\"goats\")",
							"substr(\"long string\",3,5)",
							"strpos(\"goats\",\"many goats gallop across the rocks\")",
							"lower(\"ALL UPPER\")",
							"upper(\"all lower\")");
		
		$fql = "SELECT uid, " . implode(",", $funcs) . 
				 " FROM user WHERE uid=$uid";
		$resp = $this->m_fqlEngine->query(17100, $uid, $fql);
		
		//print_r($resp);
		
		$this->assertTrue(array_key_exists("user", $resp));
		$this->assertEquals(1, count($resp["user"]));
		
		$this->assertTrue(array_key_exists("uid", $resp["user"][0]));
		$this->assertEquals($uid, $resp["user"][0]["uid"]);
		
		$this->assertTrue(array_key_exists("anon", $resp["user"][0]));
		$vals = $resp["user"][0]["anon"];
		$this->assertEquals(count($funcs), count($vals));
		
		$this->assertEquals("prefix-17001-suffix", $vals[0]);
		$this->assertEquals("message:CooCoo for Coco Puffs", $vals[1]);		
		$this->assertTrue(isset($vals[2]));
		$p = $vals[3];
		$this->assertTrue(($p <= 1) && ($p >= 0));
		$this->assertEquals(5, $vals[4]);
		$this->assertEquals("ng st", $vals[5]);
		$this->assertEquals(6, $vals[6]);
		$this->assertEquals("all upper", $vals[7]);
		$this->assertEquals("ALL LOWER", $vals[8]);
	}
	
	public function testWhereFunction()
	{
		$fql = "SELECT uid FROM user WHERE uid IN (17001, 17002, 17003) AND concat(\"xxx\", last_name)=\"xxxUser1\"";
		
		$uid = 17001;
		$resp = $this->m_fqlEngine->query(17100, $uid, $fql);
		
		$this->assertTrue(array_key_exists("user", $resp));
		$this->assertEquals(1, count($resp["user"]));
		
		$this->assertTrue(array_key_exists("uid", $resp["user"][0]));
		$this->assertEquals($uid, $resp["user"][0]["uid"]);	
	}
	
	public function testRestrictedAccess()
	{
		//17005 is not a friend of 17001, shouldn't be able to get status
		$fql = 'SELECT uid,first_name,status FROM user WHERE uid IN (17003, 17004, 17005)';
		$uid = 17001;
		$resp = $this->m_fqlEngine->query(17100, $uid, $fql);
		
		//print_r($resp);
		
		$this->assertTrue(array_key_exists('user', $resp));
		$this->assertEquals(3, count($resp['user']));
		
		$this->assertTrue(array_key_exists('uid', $resp['user'][0]));
		$this->assertEquals(17003, $resp['user'][0]['uid']);	
		$this->assertTrue(array_key_exists('message', $resp['user'][0]['status']));
				
		$this->assertTrue(array_key_exists('uid', $resp['user'][1]));
		$this->assertEquals(17004, $resp['user'][1]['uid']);
		$this->assertTrue(array_key_exists('message', $resp['user'][1]['status']));
		
		$this->assertTrue(array_key_exists('uid', $resp['user'][2]));
		$this->assertEquals(17005, $resp['user'][2]['uid']);
		$this->assertTrue($resp['user'][2]['status'] == null);
	}
	
	public function testCase1()
	{
		$fql = "SELECT uid,name,pic_small,interests FROM user " .
				 "WHERE has_added_app=0 AND uid IN " .
				 "(SELECT uid2 FROM friend WHERE uid1=17001)";
		
		$uid = 17001;
		$resp = $this->m_fqlEngine->query(17100, $uid, $fql);
		
		$this->assertTrue(array_key_exists("user", $resp));
		$this->assertEquals(1, count($resp["user"]));
		
		$this->assertTrue(array_key_exists("uid", $resp["user"][0]));
		$this->assertEquals(17004, $resp["user"][0]["uid"]);	
	}
	
	public function testCase2()
	{
		//user has no friends
		$fql = 'SELECT uid,name,pic_small,interests FROM user WHERE ' .
			   'has_added_app=0 and uid IN (SELECT uid2 FROM friend WHERE uid1=17006)';
		$uid = 17006;
		$resp = $this->m_fqlEngine->query(17100, $uid, $fql);
		
		$this->assertEquals(0, count($resp['user']));
		
	}
	
}

?>
