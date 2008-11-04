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

require_once( 'ringside/social/config/RingsideSocialConfig.php');
require_once( 'ringside/social/RingsideSocialUtils.php' );
require_once( 'ringside/api/clients/RingsideApiClientsRest.php' );
require_once( 'shindig/socialdata/opensocial/PeopleService.php' );

/**
 * A factory for Open Social People Objects Based on Facebook API calls.
 * 
 * @author William Reichardt <wreichardt@ringsidenetworks.com>
 * @see http://code.google.com/apis/opensocial/docs/0.7/reference/opensocial.Person.Field.html
 * 
 */
class RingsidePeopleService extends PeopleService {
	/**
	 * Contains a map of opensocial fields to facebook field names
	 * @var array
	 */
	private $fbMap;
	/**
	 * Contains a map of facebook fields to opensocial field names
	 * @var array
	 */
	private $osMap;
	/**
	 * Contains a map of opensocial field names which are expected to be stored
	 * as arrays. Used to convert Facebook comma separated lists to arrays
	 * @var array
	 */
	private $osIsArray;
	
  public function __construct()
  {
  	$this->fbMap=array(
		'id'=>'uid',
		'thumbnailUrl'=>'pic',
		'profileUrl'=>'',
		'currentLocation'=>'current_location',
		'addresses'=>'',
		'phoneNumbers'=>'',
		'aboutMe'=>'about_me',
		'status'=>'status',
		'profileSong'=>'',
		'profileVideo'=>'',
		'gender'=>'sex',
		'sexualOrientation'=>'',
		'relationshipStatus'=>'relationship_status',
		'age'=>'',
		'dateOfBirth'=>'birthday',
		'bodyType'=>'',
		'ethnicity'=>'',
		'smoker'=>'',
		'drinker'=>'',
		'children'=>'',
		'pets'=>'',
		'livingArrangement'=>'',
		'timeZone'=>'timezone',
		'languagesSpoken'=>'',
		'jobs'=>'work_history',
		'jobInterests'=>'',
		'schools'=>'education_history',
		'interests'=>'interests',
		'urls'=>'',
		'music'=>'music',
		'movies'=>'movies',
		'tvShows'=>'tv',
		'books'=>'books',
		'activities'=>'',
		'sports'=>'',
		'heroes'=>'',
		'quotes'=>'quotes',
		'cars'=>'',
		'food'=>'',
		'turnOns'=>'meeting_for',
		'turnOffs'=>'',
		'tags'=>'',
		'romance'=>'meeting_sex',
		'scaredOf'=>'',
		'happiestWhen'=>'',
		'fashion'=>'',
		'humor'=>'',
		'lookingFor'=>'meeting_for',
		'religion'=>'religion',
		'politicalViews'=>'political'
	);
	
	// Build a reverse lookup map
  	$keys=array_keys($this->fbMap);
  	foreach($keys as $key){
  		if($this->fbMap[$key]!=''){
  			$this->osMap[$this->fbMap[$key]]=$key;
  		}
  	}
	
  	$this->osIsArray=array(
  		'books',
  		'cars',
    	'activities',
    	'food',
    	'jobInterests',
    	'languagesSpoken',
      	'movies',
      	'music',
      	'quotes',
      	'sports',
      	'tags',
      	'turnOns',
        'turnOffs',
        'interests',
  		'tvShows'
  	);
	
  }
	
	private function comparator($person, $person1)
	{
		$name = $person['name']->getUnstructured();
		$name1 = $person1['name']->getUnstructured();
		if ($name == $name1) {
			return 0;
		}
		return ($name < $name1) ? - 1 : 1;
	}
	
	
	/**
	 * Returns an array of Person();
	 *
	 * @param array $ids
	 * @param string $sortOrder
	 * @param unknown_type $filter
	 * @param integer $first
	 * @param integer $max
	 * @param array $profileDetails
	 * @param Token $token
	 * @return ResponseItem
	 */
	public function getPeople($ids, $sortOrder, $filter, $first, $max, $profileDetails, $token)
	{
		// Call comes here
		//error_log("*****Query GET PEOPLE ID=[".implode(",",$ids)."] SORT=".$sortOrder." filter=$filter first=$first max=$max profileDetails=[".implode(",",$profileDetails)."] viewer=".$token->getViewerId()." owner ".$token->getOwnerId());
		$people=array();
		
		if(!is_numeric($first)){
			$first=0;
		}
		if(!is_numeric($max)){
			$max=1000;
		}
			
		$flds = $this->osFieldsToFb($profileDetails);
		
		// SortOrder can be ony topFriends or name, assume topFriends is by UID
		$orderBy=' ';
		if($sortOrder=='name'){
			$orderBy=' ORDER BY last_name,first_name';
		}
		
		// $filter is either all or hasApp
		$whereFilter = "";
		if($filter=="hasApp"){
			$whereFilter='  ';	
		}
		
		// Max cannnot exceed 1000 at a time
		
		
			$fql = "SELECT " . $flds . " FROM user WHERE uid in (".implode(',',$ids).") $whereFilter".$orderBy;
			$client=$token->getAppClient();
			$resp = $client->fql_query($fql);
			foreach($resp as $firstPerson){
				$peep=new Person($uid,new Name($firstPerson['first_name']." ".$firstPerson['last_name']));	
	
				// Populate this person with extra data
				$firstPersonKeys=array_keys($firstPerson);
				foreach($firstPersonKeys as $fbKey){
					if(array_key_exists($fbKey,$this->osMap)){
						$osKey=$this->osMap[$fbKey];
						
						$fbValue=$firstPerson[$fbKey];
						$osValue=$this->convertToOSArrayType($osKey,$fbValue);
						$peep->$osKey=$osValue;
					}
				}
				
				RingsidePeopleService::enforceOpenSocialDatatypes($peep);			
				
				$people[]=$peep;
			}		

		$totalSize = count($people);
		//error_log($totalSize." People Returned.");
		$last = $first + $max;
		$last = min($last, $totalSize);
		$people = array_slice($people, $first, $last);
		$collection = new ApiCollection($people, $first, $totalSize);
		return new ResponseItem(null, null, $collection);
	}
	
	public function getIds($idSpec, $token)
	{
		$ids = array();
		switch ( $idSpec->getType()) {
			case 'OWNER' :
				$ids[] = $token->getOwnerId();
				break;
			case 'VIEWER' :
				$ids[] = $token->getViewerId();
				break;
			case 'OWNER_FRIENDS' :
				$ids = $this->getFriends($token->getOwnerId(),$token);
				break;
			case 'VIEWER_FRIENDS' :
				$ids = $this->getFriends($token->getViewerId(),$token);
				break;
			case 'USER_IDS' :
				$ids = $idSpec->fetchUserIds();
				break;
		}
		return $ids;
	}
	
	/**
	 * Given an uid returns the id's of their friends as an array.
	 *
	 * @param unknown_type $uid
	 */
	private function getFriends($uid,$token){
		$apiClientApplication=$token->getAppClient();
		$arryFriends=$apiClientApplication->friends_get($uid);
		return $arryFriends;
	}
	
	/**
	 * Given a list of OS fields return a list of FQL fields
	 *
	 * @param unknown_type $profileDetails
	 */
	private function osFieldsToFb(array $profileDetails){
		$hasUid=false;
		$hasFirst=false;
		$hasLast=false;
		
		$fbFields='';
		foreach($profileDetails as $fieldName){
			$fbFieldValue=$this->fbMap[$fieldName];
			if($fbFieldValue=='uid')
				{ $hasUid=true;}
			if($fbFieldValue=='first_name')
				{ $hasFirst=true;}
			if($fbFieldValue=='last_name')
				{ $hasLast=true;}
			if($fbFieldValue!=''){
				$fbFields=$fbFields.$fbFieldValue.",";
			}
		}
		
		// All queries must contain uid,first_name,last_name
		if(!$hasUid){
			$fbFields=$fbFields.'uid,';
		}
		if(!$hasFirst){
			$fbFields=$fbFields.'first_name,';
		}
		if(!$hasLast){
			$fbFields=$fbFields.'last_name,';
		}
		
		// When done, remove the traling comma
		if(substr($fbFields,strlen($fbFields)-1,1)==','){
			$fbFields=substr($fbFields,0,strlen($fbFields)-1);
		}
		return $fbFields;
	}
	
	/**
	 * Just because field names have been maped does not mean their values
	 * obay the OS spec's enum values or datatypes. This function  performs
	 * a series of checks to make sure this occurs.
	 *
	 * @param  $rawOSPerson
	 */
	private static function enforceOpenSocialDatatypes( &$rawOSPerson){
		RingsidePeopleService::enforceGender($rawOSPerson);
		RingsidePeopleService::enforceTimezone($rawOSPerson);
		RingsidePeopleService::enforceJobsArray($rawOSPerson);
		RingsidePeopleService::enforceSchoolsArray($rawOSPerson);
		RingsidePeopleService::enforceStatus($rawOSPerson);
		RingsidePeopleService::enforceCurrentLocation($rawOSPerson);
	}
	
	/**
	 * Only Acceptable values are MALE or FEMALE
	 *
	 * @param  Person $rawOSPerson
	 * @see http://code.google.com/apis/opensocial/docs/0.7/reference/opensocial.Enum.Gender.html
	 */
	private static function enforceGender( Person &$rawOSPerson){
		$gender=$rawOSPerson->getGender();
		if($gender=='M'||$gender=='m'){
			$rawOSPerson->setGender('MALE');
		} else {
			$rawOSPerson->setGender('FEMALE');
		}
	}
	
	/**
	 * Facebook timezones are in hours from GMT and OS are in minutes
	 *
	 * @param  Person $rawOSPerson
	 * @see http://code.google.com/apis/opensocial/docs/0.7/reference/opensocial.Person.Field.html#TIME_ZONE
	 */
	private static function enforceTimezone( Person &$rawOSPerson){
		$rawOSPerson->setTimeZone($rawOSPerson->getTimeZone()*60);
	}

	/**
	 * Convert FB Jobs into OS array of Organizations.
	 * 
	 * @param Person $rawOSPerson
	 */
	private static function enforceJobsArray( Person &$rawOSPerson){

		$arryJobs=$rawOSPerson->getJobs();
		if(is_null($arryJobs)){
			return;
		}
		
		$organizations=array();
		foreach($arryJobs as $job){
			$org=new Organization();
			$jobLocation=$job['location'];
			$org->setAddress($jobLocation['city'].",".$jobLocation['state']." ".$jobLocation['country'] );	
		    $org->setDescription($job['description'] );	
		    $org->setStartDate($job['start_date'] );
		    $org->setEndDate($job['end_date'] );
		    $org->setName($job['company_name']);
		    $org->setTitle($job['position']);
		    $organizations[]=$org;
		}
		
		$rawOSPerson->setJobs($organizations);
	}

	/**
	 * Convert FB fql object into and array of OS Organizations
	 *
	 * @param Person $rawOSPerson
	 */
	private static function enforceSchoolsArray( Person &$rawOSPerson){

		$arrySchools=$rawOSPerson->getSchools();
		if(is_null($arrySchools)){
			return;
		}
		
		$schools=array();
		foreach($arrySchools as $school){
			$org=new Organization();
		    $org->setDescription($school['description'] );	
		    $org->setEndDate($school['year'] );
		    $org->setName($school['name']);
		    $org->setTitle(implode(',',$school['concentrations']));
		    $schools[]=$org;
		}
		
		$rawOSPerson->setSchools($schools);
	}
	
	/**
	 * Convert FB Status (Status+time) into OS status (Status as string)
	 *
	 * @param Person $rawOSPerson
	 */
	private static function enforceStatus( Person &$rawOSPerson){
		$status=$rawOSPerson->getStatus();
		if(is_array($status)){
			$rawOSPerson->setStatus($status['message']);
		}
		
	}
	
	private static function enforceCurrentLocation( Person &$rawOSPerson){
		$loc=$rawOSPerson->getCurrentLocation();
		if(is_array($loc)){
			$add=new Address($loc['city'].", ".$loc['state']." ".$loc['zip']);
			//$add->setgetUnstructuredAddress($loc['city'].", ".$loc['state']." ".$loc['zip']);
			$add->setExtendedAddress($loc['city'].", ".$loc['state']." ".$loc['zip']." ".$loc['country']);
			$add->setRegion($loc['state']);
			$add->setCountry($loc['country']);
			$add->setPostalCode($loc['zip']);
			$rawOSPerson->setCurrentLocation($add);
		}
		
	}
	
	/**
	 * In many cases there exists analogous fields between os and fb but
	 * os requires them to be arrays.
	 *
	 * @param unknown_type $osFieldName
	 * @param unknown_type $fbValue
	 */
	private function convertToOSArrayType($osFieldName,$fbValue){
		if(in_array($osFieldName,$this->osIsArray)){
			return explode(',',$fbValue);
		} else {
			return $fbValue;
		}
	}
	
}
