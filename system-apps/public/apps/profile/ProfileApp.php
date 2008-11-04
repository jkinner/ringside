<?php
// FIXME: DAOs does not belong in this file
require_once('ringside/api/clients/RingsideApiClients.php');
require_once('ringside/api/db/RingsideApiDbDatabase.php');
require_once('ringside/api/dao/UserProfile.php');
require_once('ringside/apps/model/user.php');
require_once('ringside/api/dao/App.php');
require_once('ringside/api/bo/App.php');
require_once('ringside/api/ServiceFactory.php');


/**
 * This class is responsible for performing all html gneration and api/database 
 * operations used in the profile application.
 * 
 */
class ProfileApp 
{
	
	private $uid;
	private $database;
	private $ringside;
	private $readOnly;
	private $restClient;
	private $debugging=false;
	
	/**
	 * Constructing this class forces a user to login in on any page it is created on.
	 * $uid sets the user the profile app will refer to.
	 *
	 * @param string $uid The user who's profile we are displaying
	 * @param boolean $readOnly should be set if you are allowing a user to view but not edit the contents of the profile.
	 * @param Object $restClient The client to use to populate this page
	 */
    public function __construct( $uid=-1,$readOnly=false, $restClient = null ) {
   		$this->ringside = new RingsideApiClients( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey  );
    	$this->readOnly=$readOnly;
    	if($uid==-1){
			$this->ringside->require_login(); 
			$this->uid=$this->ringside->get_loggedin_user() ;
				
    	} else {
    		$this->uid=$uid;
    	}
    	
    	$this->database = RingsideApiDbDatabase::getDatabaseConnection(); // remove this
    	$this->restClient = $restClient;
    }
	
	/**
	 * Causes the narrow collumn to be printed to the output stream.
	 */
	public function printNarrow(){
		$block=array();
		$this->getNarrowProfile($block);
		print join("\n",$block);
	}
	
	/**
	 * Causes the left or narrow collumn to be generated and returned as an 
	 * array.
	 * 
	 * @param array $block an existing array to be appended to add the profile to
	 */
	function getNarrowProfile(&$block){
		
		// Mark region as narrow by setting flavor
		$block[]="<rs:flavor name=\"narrow\">";
		
		// Generate Profile Picture
		$block[]="<div class=\"profile_narrow_header\">";
		$block[]="<p align='center'><fb:profile-pic uid=\"$this->uid\" profilepicsize=\"profile-narrow-col\" /></p>";
		$block[]="</div><!-- END profile_narrow_header -->";
		
		// Render Profile Actions
		$block[]="<div class=\"profile_actions\">";
		$block[]="   <rs:flavor name=\"profile-actions\">";
		
		// Write out the raw user profile fbml to be filtered for
		// profile actions.
		$block=array_merge($block, $this->getAllFbmlForUserApps());
		$block[]="<br/>\n";
		$block[]="	 </rs:flavor><p>";
		/*
		 *
		 *	mschachter (6/30/2008): there is no such tag rs:if-loggedin, what to do here?
		$block[]="	 <rs:if-loggedin uid=\"profileowner\">";
		
		if($this->readOnly){
			array_push($block,"	<fb:name firstnameonly='true' uid='$this->uid' />&nbsp;is online now.");
		} else {
			array_push($block,"	<fb:name firstnameonly='true' uid='$this->uid' />&nbsp;are online now.");
		}
		
		$block[]="	 </rs:if-loggedin>";
		*/
		$block[]="</div><!-- END profile_actions -->";
		
		// Generate listing of users installed apps
		$block[]="<div class=\"profile_apps\">";		
		
		// Generate the profile app profile in wide
		$app_appBlock=$this->getIconBasedAppSummary();
		$block=array_merge($block,$app_appBlock);
		$block[]="</div><!-- END profile_apps -->";

		// Allow all apps to render their profiles
		$appsBlockArry=$this->getAllFormattedFbmlForUserApps("narrow");
		$block=array_merge($block,$appsBlockArry);
		
		$block[]="<div id='app-mini-feed-header' class='wide_app_header'> ";
		$block[]='Mini Feed';
		$block[]="</div>";
		$block[]="<rs:feed uid='".$this->uid."' />";
	
		
		// End narrow region
		array_push($block,"</rs:flavor>");
		
	}
	
	/**
	 * Returns an associative array of all app fbml blocks
	 * indexed by their app id. Used to render apps for displaying
	 * profile actions.
	 */
	function getAllFbmlForUserApps(){
	    $userAppList = $this->restClient->users_getAppList();

	    $aggregateFbml="";
		$fbmlArry=array();		
		foreach ($userAppList as $index=>$appInfo) {			
			$returnedFbml=$this->getFbmlForApp($appInfo['app_id'],$this->uid);
			$app_id=$appInfo['app_id'];
			$fbmlArry[$app_id]=$returnedFbml;
		}
		
		return $fbmlArry;
	}
	
	/**
	 * Returns an array of all app fbml blocks
	 * formatted for display.
	 * $location enum(narrow|wide) indicates the style to be used when fbml is returned
	 * $arryExcludeAppNames is an array of app names which should not appear in the list
	 * 
	 */
	function getAllFormattedFbmlForUserApps($location="narrow",$arryExcludeAppNames=null){
	
	    $userAppList=Api_Bo_App::getApplicationListByUserId($this->uid);
	    $this->debug("DUMPING userAppList for $this->uid"." which is ".count($userAppList)." apps");
		$aggregateFbml="";
		$fbmlArry=array();
		
		if ( !empty( $userAppList ) )
		{
		   foreach ($userAppList as $index=>$appInfo1) 
		   {
		   	  $appInfo=$appInfo1['RingsideApp']; 
			  $this->debug("Processing ".$appInfo['name']." ".$appInfo['app_id']);
		      $returnedFbml=$this->getFbmlForApp($appInfo1['app_id'],$this->uid);
		      $app_id=$appInfo1['app_id'];
		      $app_name=$appInfo['name'];
		      	
		      $enabled=true;
		      	
		      // Don't show this app, its on the ignore list
		      if(is_array($arryExcludeAppNames)){		         	
		         if(in_array($app_name,$arryExcludeAppNames)){
		            $enabled=false;
		         }
		      }
		      	
		      // Don't show this app, its not it this collumn
		      if($appInfo1['profile_col']!=$location){
		         $enabled=false;
		      }

		      if($appInfo1['profile_col']!=''&&$location='wide'){
		         $enabled=true;
		      }		      	
		      // Don't show the app if it has nothing to say
		      if($returnedFbml==''){
		         $enabled=false;
		      }
		      	
		      if($enabled){		      	
		         $app_appBlock=$this->getFormatedFbmlFromAppName($app_name,$location,true,$returnedFbml);
		         $fbmlArry=array_merge($fbmlArry,$app_appBlock);
		         	
		      }
		   }
		}
		
		return $fbmlArry;
	}
	
		
	/** 
	 * Returns the fbml for singlle app $app_id for user $uid.
	 */
	public function getFbmlForApp($app_id,$uid){
		$fbml = $this->restClient->profile_getFBML( $uid, $app_id );
		$this->debug("Returned FBML is ($uid, $app_id) $fbml");
		return $fbml;
	}
	
	/**
	 * Prints the current wide collumn format.
	 */
	public function printWide(){
		xdebug_break( );
		$block=array();
		$this->getWideProfile($block);
		print join("\n",$block);
	}

	/**
	 * Generates the wide collumn and returns it in an array.
	 */
	function getWideProfile(&$block){
		
		// Mark region as narrow by setting flavor
		array_push($block,"<rs:flavor name=\"wide\">\n");
		
		// Render Name and Status area
		array_push($block, "<b><fb:name useyou='false' uid=\"$this->uid\"  /></b><br>");
		$status_block=$this->getStatusBlock();
		$block=array_merge($block,$status_block);
		array_push($block, "<p></p>" );
		
		// Generate the profile app profile in widew
		//$app_appBlock=$this->getFormatedFbmlFromAppName("profile","wide",false);
		$app_appBlock=$this->getProfileInfoWideBlock();
		$block=array_merge($block,$app_appBlock);
		
		// Allow all apps to render their profiles except profile
		$appsBlockArry=$this->getAllFormattedFbmlForUserApps("wide");//,array("profile")
		$block=array_merge($block,$appsBlockArry);
		
		$appsBlockArry=$this->getAllFormattedHtmlForUserOpenSocialApps("wide");
		$block=array_merge($block,$appsBlockArry);
		
		// End narrow region
		array_push($block,"</rs:flavor>");
		
	}

	/** 
	 * Returns the profile fbml for the app $appName name for user $uid.
	 * Useful for rendering an ap out of sequence in the list.
	 */
	//TODO FIX- THis is wrong. It is accessing the default FBML. Not the user's fbml.
	public function getFbmlForAppName($appName){
		// FIXME: This layer should not be able to access the dao directly
		$appInfo = Api_Dao_App::getApplicationInfoByName($appName, $this->database);
		$app_id=$appInfo['id'];
		return $this->getFbmlForApp($app_id,$this->uid);
	}

	/** 
	 * Returns the profile fbml for the app $appName name for user $uid.
	 * Useful for rendering an app out of sequence in the list.
	 */
	public function getFormatedFbmlFromAppName($appName,$location="narrow",$showName=true,$preRenderedFbml=''){
		
		try{
			if($showName){
				// Write optional header block
				$block[]="<div id='app-$appName-header' class='".$location."_app_header'> ";
				$block[]= $appName;
				//$block[]="<div style='float: right;' ><form id='formmove' name='formmove'  method='post' ><input name='action' type='hidden' value='saveColChange'/><input name='appname' type='hidden' value='$app_name'/><a onClick='form.submit();' href=''> [move] </a></form></div>";
				$block[]="</div>";
			}
			$block[]="<a name=\"app-$appName\"><div id=\"app-$appName\" class=\"".$location."_app\"> ";
			if($preRenderedFbml==''){
				$block[]= $this->getFbmlForAppName($appName);
			} else {
				$block[]= $preRenderedFbml;
			}
			$block[]="</div></a><br>";
			return $block;
		} catch (Exception $e) {	
			
			$block=array();
			$block[]= "<p>Please install the $appName application to see the information in this block.</p>";
			return $block;
		}
	}

	/** 
	 * Returns a assocative array of profile values for the profile fields in $fieldNames.
	 */
	public function getProfileValues($fieldNames){
		$dbUserProfile = new Api_Dao_UserProfile();
		$dbUserProfile->setUserId($this->uid);
       	$dbUserProfile->retrieveFromDb($fieldNames, $this->database);
       	return $userProfile = $dbUserProfile->getUserProfile();
	}

	/**
	 * Returns the user activerecord for the uid the class was constructed with.
	 */
	public function getUserData(){
		$user=new User();
		$this->userModel=$user->find($this->uid);
		return $this->userModel;
	}
	
	/**
	 * Generates and prints a set of options for a select form element.
	 * $from the starting index of the option
	 * $to the ending index
	 * $selected the numeric or text value of the item to mark as being selected in the list
	 * $arryLabels an array of option text values to use.
	 * $useLabelAsSelectionValue if true assumes the $selected value is a label, not an index
	 * $useLabelAsReturnValue if true, the posted value will be the option label, not the option index.
	 * 
	 */
	public function printNumericOption($from,$to,$selected,$arryLabels=null,$useLabelAsSelectionValue=false,$useLabelAsReturnValue=false){
		for($index=$from;$index<=$to;$index++){
			$selectedVal="";
			
			if(is_null($arryLabels)){
				$valueText="".$index;
			} else {
				$valueText=$arryLabels[$index-$from];
			}
			
			if($useLabelAsSelectionValue){
				if($valueText==$selected){
					$selectedVal="selected=\"true\"";	
				}
			} else {
				if($index==$selected){
					$selectedVal="selected=\"true\"";
				}
			}

			
			print("<option ");
			if($useLabelAsReturnValue){
				print("value=\"$valueText\" ");
			} else {
				print("value=\"$index\" ");	
			}
			print($selectedVal.">$valueText</option>");
		
			
		}
	}
	
	/**
	 * Generates derived fields needed to set select fields in the edit form.
	 * Returns all calculated fields in an array.
	 */
	public function getEditFormData(){
		$user=$this->getUserData();
		// Build custom variables for form display
		$dateParts=getdate(strtotime($user->userbasicprofile->dob));
		$birthDayOfMonth=0;
		$birthMonth=0;
		$birthYear=1909;
		if(array_key_exists('mday',$dateParts))  {$birthDayOfMonth=$dateParts['mday'];}
		if(array_key_exists('mon',$dateParts))  {$birthMonth=$dateParts['mon'];}
		if(array_key_exists('year',$dateParts))  {$birthYear=$dateParts['year'];}
		$sex=0;
		if($user->userbasicprofile->sex=='M') {$sex=2;}
		if($user->userbasicprofile->sex=='F') {$sex=1;}
		return array('birthDayOfMonth'=>$birthDayOfMonth,'birthMonth'=>$birthMonth,'birthYear'=>$birthYear,'sex'=>$sex);
	}
	
	/**
	 * Attemps to write changes back to database if changes are posted.
	 * Returns true if suceeds so the calling form can display a feedback
	 * message.
	 */
	public function saveEditChanges($req){
		if(!array_key_exists('action',$req)){
			return false;
		}
		
		if($req['action']=='Save Changes'){
			//sex,birthday_month,birthday_day,birthday_year	,birthday_visibility,hometown,political,religion
			$changesArry=array();
			$user=$this->getUserData();
			$writeback=false;
			if(array_key_exists('sex',$req)){
				if($req['sex']!=0){
					
					if($req['sex']==1)
						$changesArry['sex']="F";
					else
						$changesArry['sex']="M";
				}
			}
			
			if(array_key_exists('birthday_month',$req)){
				if($req['birthday_month']!='Month:'){
					$changesArry['dob']=$req['birthday_year']."-".$req['birthday_month']."-".$req['birthday_day'];
				}
			}
			
			if(array_key_exists('dob_privilage',$req)){
				$changesArry['dob_privilage']=$req['dob_privilage'];
			}
			
			
			if(array_key_exists('political',$req)){
					$changesArry['political']=$req['political'];
			}

			if(array_key_exists('religion',$req)){
					error_log('Religion is '.$req['religion']);
					$changesArry['religion']=$req['religion'];
			}

			if(array_key_exists('hometown',$req)){
					$changesArry['hometown']=$req['hometown'];
			}

			return $user->userbasicprofile->save($changesArry,true);		
		}
		
		return false;
	}
	
	function createSaveArray($arryNames,$req){
		$arrySaveValues=array();
		foreach( $arryNames as $key){
			$arrySaveValues[$key]=$req[$key];
		}
		$arrySaveValues['user_id']= $this->uid;
		return $arrySaveValues;
	}
	
	public function saveContactChanges($req){
		if(!array_key_exists('action',$req)){
			return false;
		}
		
		if($req['action']!='Save Changes'){
			return false;
		}
		
		//Processes form array for contacts
		$user=$this->getUserData();
		$this->updateOneToManyArray($user,'userprofileecontact','Userprofileecontact',5,array('contact_value','contact_type'),$req);
				
		$arryValidFields=array('mobile_phone','home_phone','address','city','zip','website');
		$contactData = $this->createSaveArray($arryValidFields,$req);
		if(sizeof($contactData)==0) {
		   return false;
		}

		if( !is_object($user->userprofilecontact)){
		   $user->userprofilecontact=new Userprofilecontact($contactData);
		}
		return $user->userprofilecontact->save($contactData,true);
		
	}

	/**
	 * Assuming there exists an activerecord array
	 *
	 * @param unknown_type $arObject - the user object to act on  (ie. $user )
	 * @param unknown_type $collectionName - the name of the collection within user object (ie. userprofileecontact)
	 * @param unknown_type $collectionClassName - the name of the class for the collection (ie. Userprofileecontact)
	 * @param unknown_type $itemCount - Number of items available within request
	 * @param unknown_type $arryUpdateFields - the fields to search for ( searches for field.# )
	 * @param unknown_type $req - the request with all the fields for us
	 * @return unknown  true - either it worked or it there was nothing to do, false if it fails.
	 */
	public function updateOneToManyArray($arObject,$collectionName,$collectionClassName,$itemCount,$arryUpdateFields,$req){

		// Loop once for each array item to be added
		// build up information as records to persist
		$arryRecords = array();
		for($index=1;$index<$itemCount+1;$index++){
		   
		   $arryUpdates = array();
		   
		   //print("Pass $index\n");
			foreach($arryUpdateFields as $fieldName){				
				$indexedFieldname=$fieldName.$index;
				$indexedValue=$req[$indexedFieldname];

				$arryUpdates[$fieldName]=$indexedValue;
			}
			
			// Don't add empties
			if( !empty( $arryUpdates ) && strlen($arryUpdates[$arryUpdateFields[0]])){
				$arryUpdates['user_id']=$this->uid;	
				$arryRecords[]=$arryUpdates;
			}
		}
		
		//		print("Now Writting <p>");
		//		var_dump($arryRecords);
		// Now file $arryRecords in the database

		// Delete all existing associations
		$colClass = new $collectionClassName();
		$colClass->delete_all("user_id=".$this->uid);

		if ( !empty( $arryRecords )) {

		   //create and add new items
		   $collections = array();
		   foreach( $arryRecords as $record){
		      $collections[] = new $collectionClassName( $record );
		   }
		    
		   $arObject->$collectionName=$collections;
		   return $arObject->save();
		}

		return true;
	}

	/**
	 * Given an array of objects, selects the $index item and
	 * returns the fieldname of that object or default if it could
	 * not be found.
	 */
	public function extractValue($arArry,$index,$fieldname,$default){
		if(!is_array($arArry)) return $default;
		if(sizeof($arArry)-1<$index) return "$default";
		$count=0;
		foreach($arArry as $row){
			if($count==$index){
				return $row->$fieldname;
			}
			$count++;
		}
		return $default;
	}
	
	public function extractObject($arArry,$index){
		if(!is_array($arArry)) return null;
		if(sizeof($arArry)-1<$index) return "$default";
		$count=0;
		foreach($arArry as $row){
			if($count==$index){
				return $row;
			}
			$count++;
		}
		return null;
	}

	public function getItem($assoArray,$index){
		
		$counter=0;
		foreach($assoArray as $item){
			if($counter==$index){
				return $item;
			}
			$counter++;
		}
		return null;
	}
	
	public function savePersonalChanges($req){
	
		if(!array_key_exists('action',$req)){
			return false;
		}
		
		if($req['action']!='Save Changes'){
			return false;
		}
				
		$arryValidFields=array('activities','interests','music','tv','movies',
			'books','quotes','about');
		$arrySaveData=$this->createSaveArray($arryValidFields,$req);
		
		if(sizeof($arrySaveData)==0) return false;
		
		// For a user without personal data yet.
		$user=$this->getUserData();
		if( !is_object($user->userprofilepersonal)){
		   $user->userprofilepersonal=new Userprofilepersonal($arrySaveData);
		}

		return $user->userprofilepersonal->save($arrySaveData,true);
		
		
	}
	
	public function saveEducationChanges($req){
			
		if(!array_key_exists('action',$req)){
			return false;
		}
		
		if($req['action']!='Save Changes'){
			return false;
		}
		//var_dump($req);
		$user=$this->getUserData();
		return $this->updateSchoolDataArray($user,'userprofileschool','Userprofileschool',3,array('school_name','grad_year','attended_for','concentrations'),$req);
		
		//$arryValidFields=array();
		
		//return $this->saveGenericChanges($arryValidFields,'userprofileschool',$req,true);
	}
	
	/** 
	 * Assuming there exists an activerecord array
	 */
	public function updateSchoolDataArray($arObject,$collectionName,$collectionClassName,$itemCount,$arryUpdateFields,$req){

		//$arryRecords=array();
		
		// Loop once for each array item to be added
		for($index=1;$index<$itemCount+1;$index++){
			foreach($arryUpdateFields as $fieldName){				
				$indexedFieldname=$fieldName.$index;
				$indexedValue=$req[$indexedFieldname];
				//print("ADDing $fieldName => $indexedValue<p>");
				$arryUpdates[$fieldName]=$indexedValue;
				//var_dump($indexedValue);
			}
			
			if(strlen($arryUpdates['school_name'])>0){
				$arryRecords[]=$arryUpdates;
			}
		}
		
		//print("Creating these records<p>");
		//var_dump($arryRecords);
		
		// Delete all existing associations
		$colClass=new $collectionClassName();
		$colClass->delete_all("user_id=".$this->uid);
		
		//create and add new school items
		foreach($arryRecords as $record){
					$newArryRecords[]=new $collectionClassName($record);
		}
		
		// We can't save these school items unless they have school_id's
		// So we must either lookup or create new ones
		$count=0;
		foreach($newArryRecords as $userProfEduItem){
			$schoolName=$userProfEduItem->school_name;
			$school=new School();
			//print("Query For: "."name='$schoolName' and school_type='".$arryRecords[$count]['attended_for']."'");
			$school=$school->find_first("name='$schoolName' and school_type='".$arryRecords[$count]['attended_for']."'");
			if($school==null){
				$school=new School(array('school_type'=>$arryRecords[$count]['attended_for'],'name'=>$schoolName));
				$school->save();
				$school=$school->find_first("name='$schoolName' and school_type='".$arryRecords[$count]['attended_for']."'");
			}
			
			// Now a school exists and we can update the Edu Item
			$userProfEduItem->school_id=$school->id;
			$count++;
		}
		
		// Now save our user prof items		
		$arObject->$collectionName=$newArryRecords;
		return $arObject->save();


	}

	public function saveRelationshipChanges($req){
		
		if(!array_key_exists('action',$req)){
			return false;
		}
		
		if($req['action']!='Save Changes'){
			return false;
		}
		
		$arryValidFields=array('status','alternate_name');
		$arrySaveData=$this->createSaveArray($arryValidFields,$req);
		
		// Now sew together the check boxes into fields
		if($req['interested_in_men']=='true' && $req['interested_in_women']=='true'){
			$meeting_sex="M,F";
		} else if($req['interested_in_men']=='true'){
			$meeting_sex="M";
		} else if($req['interested_in_women']=='true'){
			$meeting_sex="F";
		} else {
			$meeting_sex="";
		} 
		$arrySaveData['meeting_sex']=$meeting_sex;
		
		// Construct meeting for comma seperated list from checkboxes
		$meeting_for='';
		if($req['looking_for_dating']=='true'){
			$meeting_for=$meeting_for."Dating,";
		}
		if($req['looking_for_relationship']=='true'){
			$meeting_for=$meeting_for."A Relationship,";			
		}
		if($req['looking_for_networking']=='true'){
			$meeting_for=$meeting_for."Networking,";
		}
		if($req['looking_for_friendship']=='true'){
			$meeting_for=$meeting_for."Friendship,";
		}
		if(strlen($meeting_for)>1){
			$meeting_for=substr($meeting_for,0,strlen($meeting_for)-1);
		}
		$arrySaveData['meeting_for']=$meeting_for;
		
		
		// Save results
		$user=$this->getUserData();
		// Does a record already exists or do we need to create one?
		if(!is_object($user->userprofilerel)){
			$user->userprofilerel=new Userprofilerel($arrySaveData);
		}

		return $user->userprofilerel->save($arrySaveData,true);
		
	}
	
	function saveGenericChanges($arryValidFields,$nameOfManyClass,$req,$debug=false){
			if($debug){
				var_dump($req);
			}
			$arrySaveData=$this->createSaveArray($arryValidFields,$req);
			if(sizeof($arrySaveData)==0) return false;
			$user=$this->getUserData();
			return $user->$nameOfManyClass->save($arrySaveData,true);
	}
	
	function getRelationshipFormData(){
		$user=$this->getUserData();
		// Build custom variables for form display
		
		// Break out meeting_sex, a comma delemied list of sexes m,f
		$meetingSex=$user->userprofilerel->meeting_sex;
		$interested_in_men=false;
		$interested_in_women=false;		
		if(!is_null($meetingSex)){
			if(strstr(strtoupper($meetingSex),'M')){
				$interested_in_men=true;
			}
			if(strstr(strtoupper($meetingSex),'F')){
				$interested_in_women=true;
			}
		}

		// Break out meeting_for for comma separated values from the set
		//Friendship,Dating,A Relationship,Networking
		$lookingFor=$user->userprofilerel->meeting_for;
		$looking_for_friendship=false;
		$looking_for_dating=false;
		$looking_for_relationship=false;
		$looking_for_networking=false;
		if(!is_null($lookingFor)){
			if(strstr(strtoupper($lookingFor),'FRIENDSHIP')){
				$looking_for_friendship=true;
			}
			if(strstr(strtoupper($lookingFor),'DATING')){
				$looking_for_dating=true;
			}
			if(strstr(strtoupper($lookingFor),'A RELATIONSHIP')){
				$looking_for_relationship=true;
			}
			if(strstr(strtoupper($lookingFor),'NETWORKING')){
				$looking_for_networking=true;
			}
		}
		
		return array('interested_in_men'=>$interested_in_men,
			'interested_in_women'=>$interested_in_women,
			'looking_for_friendship'=>$looking_for_friendship,
			'looking_for_dating'=>$looking_for_dating,
			'looking_for_relationship'=>$looking_for_relationship,
			'looking_for_networking'=>$looking_for_networking);		
	}
	
	public function saveWorkChanges($req){
		if(!array_key_exists('action',$req)){
			return false;
		}
		
		if($req['action']!='Save Changes'){
			return false;
		}
		
		$totalJobs=2;
		
		
		// Add composite dates to the request
		for($index=0;$index<$totalJobs;$index++){
			
			$offset=$index+1;
			$startMonthKey=	'start_month'.$offset;
			$startYearKey=	'start_year'.$offset;
			$endMonthKey=	'end_month'.$offset;
			$endYearKey=	'end_year'.$offset;
			
			$start_date=$req[$startYearKey]."-1-".$req[$startMonthKey];
			$end_date=$req[$endYearKey]."-1-".$req[$endMonthKey];
			$startDateKey='start_date'.$offset;
			$endDateKey='end_date'.$offset;
			$req[$startDateKey]=$start_date;
			$req[$endDateKey]=$end_date;
		}

		for($index=0;$index<$totalJobs;$index++){
			if(!array_key_exists('current1',$req)){
				$req['current1']='false';
			}
			if(!array_key_exists('current2',$req)){
				$req['current2']='false';
			}
		}

		
//		var_dump($req);

		
		//Processes form array for contacts
		$user=$this->getUserData();
		return $this->updateOneToManyArray($user,'userprofilework','Userprofilework',$totalJobs,array('employer','position','description','city','current','start_date','end_date'),$req);
		
	}
	
	/**
	 * Generate a matrix of user added applications.
	 *
	 * @return array
	 */
	public function getIconBasedAppSummary(){
			//$userAppList = $this->restClient->users_getAppList();
			$userAppList=Api_Bo_App::getApplicationListByUserId($this->uid);
			
			//var_dump($userAppList);
			$app_name="apps";
			$location="narrow";
			$width=8;
			
			$block[]="<div id='app-$app_name' class='".$location."_app'> ";
			$colCount=0;
			$block[]="<table style='display: inline'>";

			if ( !empty( $userAppList ) ) 
			{
			   foreach($userAppList as $index=>$appInfo)
			   {
			      $app=$appInfo['RingsideApp']; 
					$this->debug(var_export($app,true));
			      if($colCount==$width){
			         $colCount=0;
			      }

			      $name = $app['name'];
			      $icon_url = $app['icon_url'];
			      if($icon_url==''||is_null($icon_url)){
			         $icon_url=RingsideApiClientsConfig::$webUrl.'/images/missing_app.gif';
			      }
			      if($colCount==0)
			      $block[]="<tr>";

			      $block[]="<td><a href='".RingsideApiClientsConfig::$webUrl."/canvas.php/".$app['canvas_url']."'><img src='$icon_url' width='16' height='16' alt='$name' title='$name' /></a></td>";
			      if($colCount==($width-1))
			      $block[]="</tr>";
			      	
			      $colCount++;

			   }
			   	
			}
			
			if($colCount<$width){	
				$span=$width-$colCount;
				$block[]="<td span=\"$span\"/>";
			}
			$block[]="</table>";
			
			$block[]="</div><br>";
			return $block;

	}
	
	/**
	 * Renders the users current status and a form which can be used to update
	 * your status.
	 */
	public function getStatusBlock($flavor='wide'){
		$statusRow=new Status();
		$statusRow=$statusRow->find($this->uid);
		$status_fbml[]="<div id='profile_status' class='profile_status'><div id='status_caption$flavor'>\n";				
		if(is_null($statusRow)){
			$status_text="Status Unknown";
			$status_fbml[]="$status_text<br>\n";
			//$status_fbml[]="And has never been updated.<br>\n";	
		} else {
			if(strtoupper(substr($statusRow->status,0,2))=='IS'){
				$status_text=$statusRow->status;
				$status_fbml[]="$status_text<br>\n";				
			} else {
				$status_text='is '.$statusRow->status;
				$status_fbml[]="$status_text<br>\n";				
			}
			$theTime=time();
			
			$updateParams=ProfileApp::timeSinceUpdate($statusRow->modified,time());			
			$timeSinceUpdate=$updateParams['value'];
			$timeUnitsSinceUpdate=$updateParams['units'];
			$status_fbml[]="Updated $timeSinceUpdate $timeUnitsSinceUpdate ago.\n";	
		} 

			if($this->readOnly){
				$status_fbml[]="</div>\n";
			} else {
				$status_fbml[]="<a href='' onClick='toggleEdit$flavor();'><img src='".RingsideApiClientsConfig::$webUrl."/images/status_icon.gif'/><span style='font-style: normal; font-size: small; vertical-align: top; '>edit</span></a></div>\n";
			}
			$status_fbml[]="<form id='statusForm$flavor' name='statusForm$flavor' method='post' style='display: none;' ><fb:name firstnameonly='true' useyou='false' uid='$this->uid' />&nbsp;\n";
  			$status_fbml[]="<input name='action' type='hidden' value='saveStatus'/><input name='textfieldStatus' value='$status_text' type='text' id='textfieldStatus' size='14' />\n";
  			$status_fbml[]="<a href=\"\" onClick=\"clearField$flavor(this);\">Clear Status</a> | <a href=\"\" onClick=\"toggleEdit$flavor();\">Cancel</a></form><br>\n";

		$status_fbml[]="</div><br>";
		return $status_fbml;
	}

	public static function timeSinceUpdate($updateTime,$theTime){
			
			//print($updateTime."/".$theTime."<p>");
			$secondsSinceUpdate=$theTime-strtotime($updateTime);
			//print(Seconds."=".$secondsSinceUpdate."<p>");
			$minutesSinceUpdate=floor($secondsSinceUpdate/60);
			$hoursSinceUpdate=floor($secondsSinceUpdate/(60*60));
			$daysSinceUpdate=floor($secondsSinceUpdate/(60*60*24));
			if($daysSinceUpdate>0){
				$timeSinceUpdate=$daysSinceUpdate;
				$timeUnitsSinceUpdate='days';				
			} else if($hoursSinceUpdate>0){
				$timeSinceUpdate=$hoursSinceUpdate;
				$timeUnitsSinceUpdate='hours';
			} else if ($minutesSinceUpdate>0) {
				$timeSinceUpdate=$minutesSinceUpdate;
				$timeUnitsSinceUpdate='minutes';
			} else {
				$timeSinceUpdate=$secondsSinceUpdate;
				$timeUnitsSinceUpdate='seconds';
				
			}
			return array('value'=>$timeSinceUpdate,'units'=>$timeUnitsSinceUpdate);
	}

	public function getProfileInfoWideBlock(){
		
		$user=new User();
		$user=$user->find($this->uid);
		$basicProfile=$user->userbasicprofile;
		$relationshipProfile=$user->userprofilerel;
		$user=$user->find($this->uid);
		$status_fbml[]="<div id='profile_wide_info' class='profile_wide_info'>\n";				
		$status_fbml[]="<b>Sex: </b> $basicProfile->sex<br>\n";
		$relationshipStatus = '';
		if (!empty ($relationshipProfile)) {
		   $relationshipStatus = $relationshipProfile->status;
		}
		$status_fbml[]="<b>Relationship Status: </b>$relationshipStatus<br>\n";
		$status_fbml[]="<b>Political Views: </b>$basicProfile->political<br>\n";
		$status_fbml[]="<b>Religious Views: </b>$basicProfile->religion<br>\n";
		$status_fbml[]="<b>Birthday:</b> $basicProfile->dob<br>\n";
		$status_fbml[]="<br>\n";
		$status_fbml[]="</div><br>";
		return $status_fbml;
	}
	
	public function saveStatusChanges($req){
		if(!array_key_exists('action',$req)){
			return false;
		}
		
		if($req['action']!='saveStatus'){
			return false;
		}
		
		if($this->readOnly){
			return false;
		}
		
		$statusMessage=$req['textfieldStatus'];
		$statusRow=new Status();
		$statusRow=$statusRow->find($this->uid);
		
		if(is_object($statusRow)){
			$fields['uid']=$this->uid;
			$fields['status']=$statusRow->status;
			$fields['created']=$statusRow->modified;
			$fields['cleared']=strftime('%Y-%m-%d %H:%M:%S',time());
			$fields['aid']=-1;
			
			//Copy current status to history.
			$statusHistory=new StatusHistory($fields);
			$statusHistory->save();
		} else {
			// Declare a new status row
			$statusRow=new Status();
		}
		$fields['uid']=$this->uid;
		$fields['status']=$statusMessage;
		$fields['modified']=strftime('%Y-%m-%d %H:%M:%S',time());
		$fields['aid']=-1;
		
		$statusRow->save($fields);
		return true;
	}
	public function savePicture($req){
		if(!array_key_exists('action',$req)){
			return 0;
		}
		
		
		if($req['action']=='Upload Picture'){
			if($req['certify']!="true"){
				return -2;
			}	
		
			if ( isset($_FILES['upfile']) ) {
				$pic_url = RingsideWebUpload::cacheUpload($this->ringside->api_client , 'upfile' );
				if ( $pic_url === false ) {			
					return -1;//no image present
				} else {					
					$user=$this->getUserData();
					$user->userbasicprofile->pic_url=$pic_url;
					$user->userbasicprofile->save();
					return 1;// picture added
				}
			}		
		}

		if($req['action']=='Remove Picture'){
					$user=$this->getUserData();
					$user->userbasicprofile->pic_url="";
					$user->userbasicprofile->save();
					return 2; //picture removed
		}


		return 0;
	}

	/**
	 * Returns an array of all app fbml blocks
	 * formatted for display.
	 * $location enum(narrow|wide) indicates the style to be used when fbml is returned
	 * $arryExcludeAppNames is an array of app names which should not appear in the list
	 * 
	 */
	function getAllFormattedHtmlForUserOpenSocialApps($location="narrow",$arryExcludeAppNames=null){	
		$userAppList=Api_Bo_App::getApplicationListByUserId($this->uid);
	    $this->debug("******************** DUMPING OS userAppList for $this->uid"." which is ".count($userAppList)." apps");
		$aggregateFbml="";
		$fbmlArry=array();
		
		if ( !empty( $userAppList ) )
		{
		   foreach ($userAppList as $index=>$appInfo1) 
		   {
		   	  $appInfo=$appInfo1['RingsideApp']; 
			  $this->debug("Processing ".$appInfo['name']." ".$appInfo['app_id']);
			  $this->debug("$appInfo1=".var_export($appInfo1,true));
		      //$returnedFbml=$this->getFbmlForApp($appInfo1['app_id'],$this->uid);
		      $app_id=$appInfo1['app_id'];
		      $app_name=$appInfo['name'];
		      	
		      $enabled=true;
		      			      			      
		      // Don't show this app if it is not open social 
		      if($appInfo['canvas_type']<>2){
		         $enabled=false;
		         $this->debug("$app_name rejected as not open social.");
		      }
		      	
		      if($enabled){		      	
		         //$app_appBlock=$this->getFormatedFbmlFromAppName($app_name,$location,true,$returnedFbml);
		         //$fbmlArry=array_merge($fbmlArry,$app_appBlock);
				$block=array("<div id='app-$app_name-header' class='wide_app_header'> ");
				$block[]= $app_name;
				//$block[]="<div style='float: right;' ><form id='formmove' name='formmove'  method='post' ><input name='action' type='hidden' value='saveColChange'/><input name='appname' type='hidden' value='$app_name'/><a onClick='form.submit();' href=''> [move] </a></form></div>";
				$block[]="</div>";
		      	
		      	 $frameParams=$_REQUEST; 
		      	 $callbackQuery = http_build_query($frameParams );
		      	 $owner_id=$_REQUEST['id'];
		      	 if($owner_id==''){
		      	 	$owner_id=$this->uid;
		      	 }
		      	 
		      	 // Get api_key
		      	 $keyService = Api_ServiceFactory::create('KeyService');
		      	 $domainService = Api_ServiceFactory::create('DomainService');
		      	 $domainId = $domainService->getNativeIdByName('Ringside');
		      	 $app_keysArray = $keyService->getKeyset($app_id, $domainId);
		      	 
		      	 $app_keys = $app_keysArray;
		      	 /*
		      	 $app_keysArray=Api_Bo_App::getUsersAppKeys($this->uid,$app_id);
		      	 $app_keys=$app_keysArray[0];
		      	 $this->debug(var_export($app_keysArray,true));
		      	 foreach($app_keysArray as $testApp_keys){
		      	 	if($testApp_keys['network_id']==$socialApiKey){
		      	 		$app_keys=$testApp_keys;
		      	 	}
		      	 }
		      	 $app_keys=$app_keysArray[0];
				 */
		      	 $this->debug("$app_keys=".var_export($app_keys,true));
		      	 
		      	 $social_session= new RingsideSocialSession();
		      	 $social_session->addApiSessionKey($app_keys['api_key'],$app_keys['secret']);
		      	 $this->debug("Building Social Session with ".$app_keys['api_key']." and ".$app_keys['secret']);
		      	 $osGadgetUrl= RingsideSocialConfig::$socialRoot.'/gadgets/ifr?view=profile&synd=ringside&fb_sig_api_key='.$app_keys['api_key'].'&fb_sig_owner_id='.$owner_id.'&url='.urlencode($appInfo['callback_url']).'&social_session_key='.$social_session->getSessionKey();
		         $this->debug("osGadgetUrl= $osGadgetUrl");
		      	 $block[]='<iframe src="'.$osGadgetUrl.'" height="400" width="450"></iframe>';
		         $fbmlArry=array_merge($fbmlArry,$block);		         	
		      		
		         	
		      }
		   }
		}
		
		return $fbmlArry;
	}
	
	
	function getFeedBlock(){
		return array('<rs:feed uid="100000" />');
	}
	
	function debug($text){
		if($this->debugging){
			error_log($text);
		}		
	}
	
}

?>