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

require_once("BasePhpClientTestCase.php");

class PhpClientUsersTestCase extends BasePhpClientTestCase
{	
	public function testUsersGetLoggedInUser()
	{
		$uid = $this->fbClient->users_getLoggedInUser();
		$this->assertEquals($uid, 17001);
	}
	
	public function testUsersIsAppAdded()
	{
		$added = $this->fbClient->users_isAppAdded();
		$this->assertEquals($added, 1);
	}
	
	public function testUsersHasAppPermission()
	{
		//$this->fbClient->printResponse = true;
		$res = $this->fbClient->call_method("facebook.users.hasAppPermission",
														array("ext_perm" => "status_update"));
		$this->assertEquals($res, 1);
		
		$res = $this->fbClient->call_method("facebook.users.hasAppPermission",
														array("ext_perm" => "photo_upload"));
		$this->assertEquals($res, 0);
		
		$res = $this->fbClient->call_method("facebook.users.hasAppPermission",
														array("ext_perm" => "create_listing"));
		$this->assertEquals($res, 1);
		$this->fbClient->printResponse = false;
	}
	
	public function testUsersSetStatus()
	{	
		//get old status
		$arr = $this->fbClient->users_getInfo(17001, "uid,status");
		$user1 = $arr[0];
		
		$stat = $user1["status"];
      $oldMessage = $stat["message"];
		
		$res = $this->fbClient->call_method("facebook.users.setStatus",
														array("status" => "writing code",
																"clear" => "true"));
		$this->assertEquals($res, 1);
		
		$arr = $this->fbClient->users_getInfo(17001, "uid,status");
		$user1 = $arr[0];		
		$stat = $user1["status"];
		$m = $stat["message"];
		$this->assertEquals("writing code", $m);

		//reset status
		$res = $this->fbClient->call_method("facebook.users.setStatus",
														array("status" => $oldMessage,
																"clear" => "true"));
		//verify that status has been reset
		$arr = $this->fbClient->users_getInfo(17001, "uid,status");
		$user1 = $arr[0];		
		$stat = $user1["status"];
		$m = $stat["message"];
		$this->assertEquals($oldMessage, $m);
	}
	
	public function testUsersGetInfo()
	{	
		$uinfo = $this->fbClient->users_getInfo(17001, "uid,about_me,activities,affiliations,birthday,books,current_location," .
                  	      						"education_history,first_name,is_app_user,has_added_app,hometown_location," .
        												   "interests,last_name,meeting_for,meeting_sex,movies,music,name," .
        												   "notes_count,pic,pic_big,pic_small,pic_square,political,profile_update_time," .
        												   "quotes,relationship_status,religion,sex,significant_other_id,status," .
        												   "timezone,tv,wall_count,work_history");
		//print_r($uinfo);
		$user1 = $uinfo[0];
		
		$this->assertEquals($user1["about_me"], "About me - nothing!");		
      $this->assertEquals($user1["activities"], "Boating, pumpkin carving, procuring comestibles, etc. ");
      $this->assertEquals($user1["birthday"], "2007-12-31");
      $this->assertEquals($user1["books"], "Snakes on a plane, the book");
      $this->assertEquals($user1["first_name"], "Test1");
      $this->assertEquals($user1["interests"], "Music,sports,tv guide,nuclear physics");
      $this->assertEquals($user1["last_name"], "User1");      
      $this->assertEquals($user1["movies"], "Snakes on a plane");      
      $this->assertEquals($user1["music"], "Miles Davis,Brittney Spears");
      //$this->assertEquals($user1["political"], "Liberal");
      $this->assertEquals($user1["profile_update_time"], "2008-01-01 00:00:00");    
      $this->assertEquals($user1["quotes"], "Nationalism is an infantile disease - Albert Einstien");      
      //$this->assertEquals($user1["relationship_status"], "Single");
      $this->assertEquals($user1["religion"], "Scientologist");
      //$this->assertEquals($user1["sex"], "M");
      $this->assertEquals($user1["significant_other_id"], "0");      
      $this->assertEquals($user1["timezone"], "-4");
      $this->assertEquals($user1["tv"], "Telemundo");
      
      //test affiliations
      $affs = $user1["affiliations"];
      $this->assertEquals(count($affs), 3);
      
      $aff = $affs[0];
      $nid = $aff["nid"];
      $this->assertEquals($nid, 1);
      $name = $aff["name"];
      $this->assertEquals($name, "Philadelphia");
      
      $aff = $affs[1];
      $nid = $aff["nid"];
      $this->assertEquals($nid, 2);
      $name = $aff["name"];
      $this->assertEquals($name, "Arts and Crafts");
      
      $aff = $affs[2];
      $nid = $aff["nid"];
      $this->assertEquals($nid, 3);
      $name = $aff["name"];
      $this->assertEquals($name, "Northeast High");
      
      
      //test current location
      $cloc = $user1["current_location"];
      $this->assertEquals($cloc["city"], "Bowmont");
      $this->assertEquals($cloc["state"], "NJ");
      $this->assertEquals($cloc["country"], "USA");
      $this->assertEquals($cloc["zip"], "00181");
      
      
      //test education history
      $ehist = $user1["education_history"];
      $this->assertEquals(count($ehist), 2);
            
      $einfo = $ehist[0];      
      $name = $einfo["name"];
      $this->assertEquals($name, "Temple University");
      $year = $einfo["year"];
      $this->assertEquals($year, "1999");
      $concs = $einfo["concentrations"];
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
      $concs = $einfo["concentrations"];
      $this->assertEquals(count($concs), 2);
      $c1 = $concs[0];
      $this->assertEquals($c1, "Rocket Science");
      $c2 = $concs[1];
      $this->assertEquals($c2, "Awesomeness");
      
      
      //test hometown location
      $hloc = $user1["hometown_location"];
      $city = $hloc["city"];
      $this->assertEquals($city, "Eightown");
      $state = $hloc["state"];
      $this->assertEquals($state, "NI");
      $country = $hloc["country"];
      $this->assertEquals($country, "USA");
      $zip = $hloc["zip"];
      $this->assertEquals($zip, "87654");
      
      
      //test meeting for      
      $mfor = $user1["meeting_for"];
      $this->assertEquals(count($mfor), 2);
      $s1 = $mfor[0];
      $this->assertEquals($s1, "Random Play");
      $s2 = $mfor[1];
      $this->assertEquals($s2, "Whatever I can get");
      

      //test meeting sex
      $msex = $user1["meeting_sex"];
      $this->assertEquals(count($msex), 2);
      $m1 = $msex[0];
      $this->assertEquals($m1, "M");
      $m2 = $msex[1];
      $this->assertEquals($m2, "F");
      
      
      //test status message
      $stat = $user1["status"];
      $m = $stat["message"];
      $this->assertEquals($m, "CooCoo for Coco Puffs");
      $t = $stat["time"];
      $this->assertTrue(isset($t));
      
      
      //test work history
      $whist = $user1["work_history"][0];      
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
	}
	
	public function testUsersGetAppList()
	{
		$resp = $this->fbClient->users_getAppList();		
		
		$app = $resp[0];
		$this->assertEquals(17100, $app["app_id"]);
		$this->assertEquals(17001, $app["user_id"]);
		$this->assertEquals("test app", $app["name"]);
      $this->assertEquals("theCanvasUrl", $app["canvas_url"]);
      $this->assertEquals("theSideNavUrl", $app["sidenav_url"]);
//      $this->assertEquals("test_case_key-17100", $app["api_key"]);
		//TODO: thorough testing
	}
}


?>
