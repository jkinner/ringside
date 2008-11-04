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

require_once( 'BaseAPITestCase.php' );
require_once( "ringside/api/OpenFBAPIException.php" );
require_once( "ringside/api/facebook/UsersGetInfo.php" );

DEFINE ( "USERGETINFO_UNKNOWNUSER", '9' );
DEFINE ( "USERGETINFO_TESTUSER", '2' );
DEFINE ( "USERGETINFO_TESTUSER_LESSDATA", '3' );

class UsersGetInfoTestCase extends BaseAPITestCase {
   

   public static function providerTestCases()
   {
   	$appUser = USERGETINFO_TESTUSER;
   
      $uids = USERGETINFO_TESTUSER;
      $fields = "first_name,last_name" ; 
      $expected =  array( "user"=> array() );
      $expected['user'][] = array( "first_name"=>"Test1", "last_name"=>"User1", "uid"=>USERGETINFO_TESTUSER);
      $tests[] = array ( $appUser, $uids, $fields, $expected );
   	   
      $uids = USERGETINFO_TESTUSER;
      $fields = "first_name" ;
      $expected =  array( "user"=> array() );
      $expected['user'][] = array("first_name"=>"Test1", "uid"=>USERGETINFO_TESTUSER);
      $tests[] = array ( $appUser, $uids, $fields, $expected );

      $uids = USERGETINFO_TESTUSER;
      $fields = "pic" ;
      $expected =  array( "user"=> array() );
      $expected['user'][] = array("pic"=>"http://no.such.url/pic.jpg","uid"=>USERGETINFO_TESTUSER);
      $tests[] = array ( $appUser, $uids, $fields, $expected );
      
      $uids = USERGETINFO_TESTUSER_LESSDATA;
      $fields = "pic" ;
      $expected =  array( "user"=> array() );
      $expected['user'][] = array("pic"=>null, "uid"=>USERGETINFO_TESTUSER_LESSDATA);
      $tests[] = array ( $appUser, $uids, $fields, $expected );      
      
      return $tests;
   }

   /**
    * @dataProvider providerTestCases
    */
   public function testCases( $appUser, $uids, $fields, $expected)
   {   
      
   	    $appId = 17100;
   	    $params = array( "uids"=>$uids, "fields"=>$fields );
   	    $getInfo = $this->initRest( new UsersGetInfo(), $params, $appUser, $appId );
   	
    	$results = $getInfo->execute();    	
    	$this->assertEquals( json_encode( $expected ), json_encode( $results ) );    	
   }

	public function testExecute() {
	
		$flds = array("uid", "about_me", "activities", "affiliations", "books", "birthday",
						  "first_name", "interests", "last_name", "education_history", "movies",
						  "music", "pic", "political", "quotes", "relationship_status",
						  "profile_update_time", "religion", "sex", "significant_other_id",
						  "timezone", "tv", "is_app_user", "has_added_app", "current_location",
						  "hometown_location", "meeting_for", "meeting_sex", "pic_small",
						  "pic_big", "pic_square","status","name","work_history","notes_count",
						  "wall_count","hs_info");
						  //TODO: hs_info, 
	
   	$inputParams = array("uids" => "2",  "fields" => implode(",", $flds));
    		
   	$appId = 17100;
   	    $ginfo = $this->initRest( new UsersGetInfo(), $inputParams, 2, $appId );
      $result = $ginfo->execute();        
      
      $this->assertTrue(array_key_exists("user", $result));
      
      $utree = $result["user"];
      $this->assertEquals(count($utree), 1);
      
      $utree = $utree[0];
      
		//print_r($utree);
      
      $this->assertEquals($utree["about_me"], "About me - nothing!");
      $this->assertEquals($utree["activities"], "Boating, pumpkin carving, procuring comestibles, etc. ");
      $this->assertEquals($utree["birthday"], "2007-12-31");
      $this->assertEquals($utree["books"], "Snakes on a plane, the book");
      $this->assertEquals($utree["first_name"], "Test1");
      $this->assertEquals($utree["name"], "Test1 User1");
      $this->assertEquals($utree["interests"], "Music,sports,tv guide,nuclear physics");
      $this->assertEquals($utree["last_name"], "User1");      
      $this->assertEquals($utree["movies"], "Snakes on a plane");      
      $this->assertEquals($utree["music"], "Miles Davis,Brittney Spears");
      $this->assertEquals($utree["notes_count"], "0");
      $this->assertEquals($utree["wall_count"], "0");   //TODO: wall count is unimplemented!
      $this->assertEquals($utree["political"],0);
      $this->assertEquals($utree["profile_update_time"], "2008-01-01 00:00:00");    
      $this->assertEquals($utree["quotes"], "Nationalism is an infantile disease - Albert Einstien");      
      $this->assertEquals($utree["relationship_status"], 0);
      $this->assertEquals($utree["religion"], "Scientologist");
      $this->assertEquals($utree["sex"], 0);
      $this->assertEquals($utree["significant_other_id"], "0");      
      $this->assertEquals($utree["timezone"], "-4");
      $this->assertEquals($utree["tv"], "Telemundo");
      $this->assertEquals($utree["pic"], "http://no.such.url/pic.jpg");
      $this->assertEquals($utree["pic_big"], "http://no.such.url/pic_big.jpg");
      $this->assertEquals($utree["pic_small"], "http://no.such.url/pic_small.jpg");
      $this->assertEquals($utree["pic_square"], "http://no.such.url/pic_square.jpg");
      
      $this->assertEquals($utree["is_app_user"], "0");
      $this->assertEquals($utree["has_added_app"], "0");      
      
      $affs = $utree["affiliations"]['affiliation'];
      $this->assertEquals(3, count($affs));
      
      $aff = $affs[0];
      $this->assertEquals("1", $aff["nid"]);
      $this->assertEquals("Philadelphia", $aff["name"]);
      
      $aff = $affs[1];
      $this->assertEquals("2", $aff["nid"]);
      $this->assertEquals("Arts and Crafts", $aff["name"]);
      
      $aff = $affs[2];
      $this->assertEquals("3", $aff["nid"]);
      $this->assertEquals("Northeast High", $aff["name"]);
      
      $ehist = $utree["education_history"]["education_info"];
      $this->assertEquals(2, count($ehist));
      
      $einfo = $ehist[0];      
      $name = $einfo["name"];
      $this->assertEquals($name, "Temple University");
      $year = $einfo["year"];
      $this->assertEquals($year, "1999");
      $c = $einfo["concentrations"];
      $concs = $c["concentration"];
      $this->assertEquals(count($concs), 2);
      $c1 = $concs[0];
      $this->assertEquals($c1, "Communications");
      $c2 = $concs[1];
      $this->assertEquals($c2, "Philosophy");
      
      $einfo = $ehist[1];      
      $name = $einfo["name"];
      $this->assertEquals($name, "Georgia Tech");
      $year = $einfo["year"];
      $this->assertEquals($year, "2006");      
      $c = $einfo["concentrations"];
      $concs = $c["concentration"];      
      $this->assertEquals(count($concs), 2);
      $c1 = $concs[0];
      $this->assertEquals($c1, "Rocket Science");
      $c2 = $concs[1];
      $this->assertEquals($c2, "Awesomeness");
      
      $hloc = $utree["hometown_location"];
      $this->assertEquals($hloc["city"], "Bowmont");
      $this->assertEquals($hloc["state"], "NJ");
      $this->assertEquals($hloc["country"], "USA");
      $this->assertEquals($hloc["zip"], "00181");
      
      $cloc = $utree["current_location"];
      $this->assertEquals($cloc["city"], "Eightown");
      $this->assertEquals($cloc["state"], "NI");
      $this->assertEquals($cloc["country"], "USA");
      $this->assertEquals($cloc["zip"], "87654");
      
      $mfor = $utree["meeting_for"]["seeking"];
      $this->assertEquals(2, count($mfor));
      $this->assertEquals("Random Play", $mfor[0]);
      $this->assertEquals("Whatever I can get", $mfor[1]);
      
      $msex = $utree["meeting_sex"]["sex"];
      $this->assertEquals(2, count($msex));
      $this->assertEquals("M", $msex[0]);
      $this->assertEquals("F", $msex[1]);
      
      $stat = $utree["status"];
      $m = $stat["message"];
      $this->assertEquals($m, "CooCoo for Coco Puffs");
      $t = $stat["time"];
      $this->assertTrue(isset($t));

      $w = $utree["work_history"];
      $winfo = $w["work_info"];
      $this->assertEquals(2, count($winfo));
		
      $whist = $winfo[0];      
		$wloc = $whist["location"];
		$city = $wloc["city"];
		$this->assertEquals($city, "Hairydelphia");
		$state = $wloc["state"];
		$this->assertEquals($state, "PA");
		$country = $wloc["country"];
      $this->assertEquals($country, "USA");
      $cname = $whist["company_name"];
      $this->assertEquals($cname, "Spacely Sprockets");
      $pos = $whist["position"];
      $this->assertEquals($pos, "Sprocket Engineer");
      $desc = $whist["description"];
      $this->assertEquals($desc, "Now is the time on sprockets when we dance");
  		$sdate = $whist["start_date"];    
      $this->assertEquals($sdate, "2002-01-01");
      $edate = $whist["end_date"];    
      $this->assertEquals($edate, "2003-02-04");
      
      $whist = $winfo[1];      
		$wloc = $whist["location"];
		$city = $wloc["city"];
		$this->assertEquals($city, "New York");
		$state = $wloc["state"];
		$this->assertEquals($state, "NY");
		$country = $wloc["country"];
      $this->assertEquals($country, "USA");
      $cname = $whist["company_name"];
      $this->assertEquals($cname, "McDonalds");
      $pos = $whist["position"];
      $this->assertEquals($pos, "Hamburgler");
      $desc = $whist["description"];
      $this->assertEquals($desc, "I steal hamburgers.");
  		$sdate = $whist["start_date"];    
      $this->assertEquals($sdate, "2004-05-01");
	}
	
	public function testMultipleUserExecute()
	{
	   $flds = array("uid");
	   $inputParams = array("uids" => "2,3",  "fields" => implode(",", $flds));
	   $appId = 17100;
	   $ginfo = $this->initRest( new UsersGetInfo(), $inputParams, 2, $appId );
	   
      $result = $ginfo->execute();
      
      $this->assertTrue(array_key_exists("user", $result));
      
      $utree = $result["user"];
      $this->assertEquals(count($utree), 2);
      
      $uinfo = $utree[0];      
      $this->assertEquals(2, $uinfo["uid"]);
      
      $uinfo = $utree[1];      
      $this->assertEquals(3, $uinfo["uid"]);
      
      
      
	}
}

?>
