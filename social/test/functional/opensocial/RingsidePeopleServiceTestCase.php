<?php
 /*
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
  */


function test_autoload($className)
{
	$src_root=dirname(__FILE__).'/../../../vendor/shindig/includes/shindig';
	$social_root=dirname(__FILE__).'/../../..';
	
	// Check for the presense of this class in our all our directories.
	$fileName = $className.'.php';
	if (file_exists("$src_root/common/$fileName")) {
		    require "$src_root/common/$fileName";
		    
	} elseif (file_exists("$src_root/../ringside/opensocial/$fileName")) {
		// Support ringside/opensocial for OS includes
		require "$src_root/../ringside/opensocial/$fileName";          
	} elseif (file_exists("$social_root/public/gadgets/$fileName")) {
		// Support ringside/opensocial for OS includes
		require "$social_root/public/gadgets/$fileName";          
	} elseif (file_exists("$src_root/gadgets/$fileName")) {
		          require "$src_root/gadgets/$fileName";		          
	} elseif (file_exists("$src_root/gadgets/samplecontainer/$fileName")) {
		          require "$src_root/gadgets/samplecontainer/$fileName";		          
	} elseif (file_exists("$src_root/gadgets/http/$fileName")) {
		          require "$src_root/gadgets/http/$fileName";		      
	} elseif (file_exists("$src_root/socialdata/$fileName")) {
		          require "$src_root/socialdata/$fileName";
	} elseif (file_exists("$src_root/socialdata/samplecontainer/$fileName")) {
		          require "$src_root/socialdata/samplecontainer/$fileName";
	} elseif (file_exists("$src_root/socialdata/opensocial/$fileName")) {
		          require "$src_root/socialdata/opensocial/$fileName";
		          
	} elseif (file_exists("$src_root/socialdata/opensocial/model/$fileName")) {
		          require "$src_root/socialdata/opensocial/model/$fileName";
		          
	} elseif (file_exists("$src_root/socialdata/http/$fileName")) {
		          require "$src_root/socialdata/http/$fileName";
	} else {
		error_log("Could not load class for $className");
	}
}

spl_autoload_register('test_autoload');

require_once('SuiteTestUtils.php');
require_once('ringside/opensocial/RingsidePeopleService.php' );
require_once('ringside/opensocial/RingsideGadgetToken.php' );

/**
 * Tests The RingsidePersonService which serves as an adapter between
 * Ringside and the OpenSocial Container for People classes..
 *
 * @author William Reichardt <wreichardt@ringsidenetworks.com>
 * @see http://code.google.com/apis/opensocial/docs/0.7/reference/opensocial.Person.Field.html
 */
class RingsidePeopleServiceTestCase extends PHPUnit_Framework_TestCase {
	
	public function setUp() {
	}
	
	public function tearDown() {
	}
	
	/**
	 * Manufacure a Mock token for use with the test.
	 *
	 * @return unknown
	 */
	public function getToken(){
		$uid_='100000';
		$vid_='100001';
		$api_key_='4333592132647f39255bb066151a2099';
		$api_secret_='b37428ff3f4320a7af98b4eb84a4aa99';
		$serverUrl='http://localhost:8080/restserver.php';
		
		$app_client= new RingsideApiClientsRest( $api_key_, $api_secret_, null, $serverUrl );
		$authToken = $app_client->auth_createToken();		
		$res = $app_client->auth_approveToken($uid_);		
		$this->assertEquals("1", $res["result"]);		
		$session_=$app_client->auth_getSession($authToken);
		
		$methods = array();
		$arguments=array();
		$token=$this->getMock('RingsideGadgetToken');//,$methods,$arguments
		$token->expects($this->any())->method('getAppClient')->will($this->returnValue($app_client));
		$token->expects($this->any())->method('getAppId')->will($this->returnValue($api_key_));
		$token->expects($this->any())->method('getDomain')->will($this->returnValue('ringside'));
		$token->expects($this->any())->method('getOwnerId')->will($this->returnValue($uid_));
		$token->expects($this->any())->method('getViewerId')->will($this->returnValue($vid_));
		$token->expects($this->any())->method('getAppUrl')->will($this->returnValue('http://localhost:8080/canvas.php/footprints'));
		$token->expects($this->any())->method('getModuleId')->will($this->returnValue('footprints'));
		return $token;
	}
	
	
	public function getIdSpec($type,$json=null){
		return new IdSpec($json,$type);
	}
	
	public function testGetIdsOwner() {
		$peopleService=new RingsidePeopleService();
		$ownerIds = $peopleService->getIds($this->getIdSpec('OWNER'), $this->getToken());
		$this->assertEquals(sizeof($ownerIds), 1);
		$this->assertEquals("100000",$ownerIds[0]);
	}
	
	public function testGetIdsViewer() {
		$peopleService=new RingsidePeopleService();
		$ownerIds = $peopleService->getIds($this->getIdSpec('VIEWER'), $this->getToken());
		$this->assertEquals(sizeof($ownerIds), 1);
		$this->assertEquals("100001",$ownerIds[0]);
	}
	
	public function testGetIdsOwnerFriends() {
		$peopleService=new RingsidePeopleService();
		$ownerIds = $peopleService->getIds($this->getIdSpec('OWNER_FRIENDS'), $this->getToken());
		$this->assertEquals(sizeof($ownerIds), 4);
		$this->assertEquals("100009",$ownerIds[0]);
		$this->assertEquals("100001",$ownerIds[1]);
		$this->assertEquals("100002",$ownerIds[2]);
		$this->assertEquals("100003",$ownerIds[3]);
	}
	
	public function testGetIdsViewerFriends() {
		$peopleService=new RingsidePeopleService();
		$ownerIds = $peopleService->getIds($this->getIdSpec('VIEWER_FRIENDS'), $this->getToken());
		$this->assertEquals(sizeof($ownerIds), 2);
		$this->assertEquals("100000",$ownerIds[0]);
		$this->assertEquals("100009",$ownerIds[1]);
	}

	public function testGetIdsUserIds() {
		$peopleService=new RingsidePeopleService();
		$ownerIds = $peopleService->getIds($this->getIdSpec('USER_IDS','[100000,100001,100002]'), $this->getToken());
		$this->assertEquals(sizeof($ownerIds), 3);
		$this->assertEquals("100000",$ownerIds[0]);
		$this->assertEquals("100001",$ownerIds[1]);
		$this->assertEquals("100002",$ownerIds[2]);
	}

	/**
	 * Objective: Create a really simple user request and don't expect much in return.
	 * Test out bad values and the enforcement of defaults.
	 * Verify delivery of expected data in the result. 
	 *
	 */
	public function testGetPeopleSimple() {
		$peopleService=new RingsidePeopleService();
		$ids=array("100000","100001");
		$sortOrder='';
		$filter='';
		$first='';
		$max=''; 
		$profileDetails=array('name');
		$token=$this->getToken();
		$peeps=$peopleService->getPeople($ids, $sortOrder, $filter, $first, $max, $profileDetails, $token);
		$this->assertEquals(2,$peeps->getResponse()->totalSize);
		$people=$peeps->getResponse()->getItems();
		$this->assertEquals("Joe Robinson",$people[0]->getName()->getUnstructured());
		$this->assertEquals("Jack Robinson",$people[1]->getName()->getUnstructured());
	}
	
/**
	 * Objective: Request a name order be placed on results and verify the order is present
	 * in the response
	 *
	 */
	public function testGetPeopleOrdering() {
		$peopleService=new RingsidePeopleService();
		$ids=array("100000","100001");
		$sortOrder='name';
		$filter='';
		$first='';
		$max=''; 
		$profileDetails=array('name');
		$token=$this->getToken();
		$peeps=$peopleService->getPeople($ids, $sortOrder, $filter, $first, $max, $profileDetails, $token);
		$this->assertEquals(2,$peeps->getResponse()->totalSize);
		$people=$peeps->getResponse()->getItems();
		$this->assertEquals("Jack Robinson",$people[0]->getName()->getUnstructured());
		$this->assertEquals("Joe Robinson",$people[1]->getName()->getUnstructured());
	}
	
	/**
	 * Objective: Test Mapping of simple return times to see if the Facebook data
	 * is accuratly copied to the OS Person object.
	 *
	 */
	public function testGetPeopleCompleteMapping(){
		$peopleService=new RingsidePeopleService();
		$ids=array("100000");
		$sortOrder='';
		$filter='';
		$first='';
		$max=''; 
		$profileDetails=array('thumbnailUrl','currentLocation','aboutMe','status','gender','relationshipStatus','dateOfBirth','timeZone','jobs','jobInterests','schools','interests','music','movies','tvShows','books','activities','quotes','turnOns','romance','lookingFor','religion','politicalViews');
		$token=$this->getToken();
		$peeps=$peopleService->getPeople($ids, $sortOrder, $filter, $first, $max, $profileDetails, $token);
		$this->assertEquals(1,$peeps->getResponse()->totalSize);
		$people=$peeps->getResponse()->getItems();
		$this->assertEquals("Joe Robinson",$people[0]->getName()->getUnstructured());
		$joerob=$people[0];

		// Do a batch of tests that verify conversion of simple types
		$this->assertEquals("100000",$joerob->getId());
		$this->assertEquals("http://localhost:8080/avatars/photo/bbickel.jpg",$joerob->getThumbnailUrl());
		$this->assertEquals("MALE",$joerob->getGender());
		$this->assertEquals("2007-12-31",$joerob->getDateOfBirth());
		$this->assertEquals("-240",$joerob->getTimeZone());
		$this->assertEquals("Scientologist",$joerob->getReligion());
		$this->assertEquals("Liberal",$joerob->getPoliticalViews());
		
		// Music
		$music=$joerob->getMusic();
		$this->assertEquals(2,sizeof($music));
		$this->assertEquals("Miles Davis",$music[0]);
		$this->assertEquals("Brittney Spears",$music[1]);
		
		// Movies
		$movies=$joerob->getMovies();
		$this->assertTrue(is_array($movies));
		$this->assertEquals(1,sizeof($movies));
		$this->assertEquals("Snakes on a plane",$movies[0]);
		
		// TV
		$tv=$joerob->getTvShows();
		$this->assertTrue(is_array($tv));
		$this->assertEquals(1,sizeof($tv));
		$this->assertEquals("Telemundo",$tv[0]);
		
		// Books
		$books=$joerob->getBooks();
		$this->assertTrue(is_array($books));
		$this->assertEquals(2,sizeof($books));
		$this->assertEquals("Snakes on a plane",$books[0]);
		$this->assertEquals(" the book",$books[1]);
		
		
		// Quotes
		$quotes=$joerob->getQuotes();
		$this->assertTrue(is_array($quotes));
		$this->assertEquals(1,sizeof($quotes));
		$this->assertEquals("Nationalism is an infantile disease - Albert Einstien",$quotes[0]);

		// Interests
		$interests=$joerob->getInterests();
		$this->assertTrue(is_array($interests));
		$this->assertEquals(4,sizeof($interests));
		$this->assertEquals("Music",$interests[0]);
		$this->assertEquals("sports",$interests[1]);
		$this->assertEquals("tv guide",$interests[2]);
		$this->assertEquals("nuclear physics",$interests[3]);
		
		// Test provided job
		$jobs=$joerob->getJobs();
		$this->assertTrue(is_array($jobs));
		$this->assertEquals(1,sizeof($jobs));
		$job=$jobs[0];
		$this->assertEquals('Now is the time on sprockets when we dance',$job->getDescription());
		$this->assertEquals('Hairydelphia,PA USA',$job->getAddress());
		$this->assertEquals('Now is the time on sprockets when we dance',$job->getDescription());
		$this->assertEquals('2003-02-04',$job->getEndDate());
		$this->assertEquals('2002-01-01',$job->getStartDate());
		$this->assertEquals('Spacely Sprockets',$job->getName());
		$this->assertEquals('Sprocket Engineer',$job->getTitle());
		
		//Schools
		$schools=$joerob->getSchools();
		$this->assertTrue(is_array($schools));
		$this->assertEquals(1,sizeof($schools));
		$school=$schools[0];
		$this->assertEquals('Temple University',$school->getName());
		$this->assertEquals('Communications,Philosophy',$school->getTitle());
		$this->assertEquals('1999',$school->getEndDate());
		
		// Looking for
		$lookingFor=$joerob->getLookingFor();
		$this->assertTrue(is_array($lookingFor));
		$this->assertEquals(1,sizeof($lookingFor));
		
		$this->assertEquals('Working',$status=$joerob->getStatus());		
		$this->assertEquals('About me - nothing!',$joerob->getAboutMe());
		
		// location
		$loc=$joerob->getCurrentLocation();
		$this->assertEquals("Bowmont, NJ 00181",$loc->getUnstructuredAddress());
		$this->assertEquals("Bowmont, NJ 00181 USA",$loc->getExtendedAddress());
		$this->assertEquals("USA",$loc->getCountry());
		$this->assertEquals("NJ",$loc->getRegion());
		$this->assertEquals("00181",$loc->getPostalCode());

		$this->assertEquals("Single",$joerob->getRelationshipStatus());
		// Not supported $joerob->getJobInterests()
		// Not supported  $this->assertEquals("",$joerob->getRomance());
		// Not supported  $act=$joerob->getActivities();
		
	}
	
}
