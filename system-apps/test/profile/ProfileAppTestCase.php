<?php
require_once 'PHPUnit/Framework.php';
require_once('apps/profile/ProfileApp.php');
require_once('ringside/api/clients/RingsideApiClients.php');
require_once('ringside/social/config/RingsideSocialConfig.php');
require_once('ringside/api/db/RingsideApiDbDatabase.php');
require_once('apps/profile/model/user.php');

class ProfileAppTestCase extends PHPUnit_Framework_TestCase {
	private $app;
	protected function setUp()
	{	
		parent::setUp();
//		$this->app=new ProfileApp();
	}
	
	protected function tearDown(){
		parent::tearDown();
	}
	
	public function testTimeSinceUpdate(){
		// 10 Minutes 1206028575/1206028586
	
		$dataBaseTime="2008-03-20 11:56:15";//1206028598
		$test10sec=  1206028585;
		$test10min=  1206029198;
		$test10hours=1206064198;
		$test10days=1206892572;

		//Seconds
		$arryUpdateTime=ProfileApp::timeSinceUpdate($dataBaseTime,$test10sec);
		$this->assertEquals(2,sizeof($arryUpdateTime));
		$this->assertEquals('seconds',$arryUpdateTime['units']);
		$this->assertEquals(10,$arryUpdateTime['value']);


		//Minutes
		$arryUpdateTime=ProfileApp::timeSinceUpdate($dataBaseTime,$test10min);
		$this->assertEquals(2,sizeof($arryUpdateTime));
		$this->assertEquals('minutes',$arryUpdateTime['units']);
		$this->assertEquals(10,$arryUpdateTime['value']);
		
		//Hours
		$arryUpdateTime=ProfileApp::timeSinceUpdate($dataBaseTime,$test10hours);
		$this->assertEquals(2,sizeof($arryUpdateTime));
		$this->assertEquals('hours',$arryUpdateTime['units']);
		$this->assertEquals(10,$arryUpdateTime['value']);
		
		//Days
		$arryUpdateTime=ProfileApp::timeSinceUpdate($dataBaseTime,$test10days);
		$this->assertEquals(2,sizeof($arryUpdateTime));
		$this->assertEquals('days',$arryUpdateTime['units']);
		$this->assertEquals(10,$arryUpdateTime['value']);
	}
	
	//getAllFbmlForUserApps
	//getIconBasedAppSummary
	//getAllFormattedFbmlForUserApps
	//getAllFbmlForUserApps
	//getAllFormattedFbmlForUserApps
	//getFbmlForApp
	//getFbmlForAppName
	//getFormatedFbmlFromAppName
	//getProfileValues
	//getUserData
	//printNumericOption
	//getEditFormData
	//getEditFormData
	//createSaveArray
	//saveContactChanges
	//updateOneToManyArray
	//extractValue
	//getItem
	//savePersonalChanges
	//saveEducationChanges
	//updateSchoolDataArray
	//saveRelationshipChanges
	//saveGenericChanges
	//getRelationshipFormData
	//saveWorkChanges
	//getIconBasedAppSummary
	//getStatusBlock
	//getProfileInfoWideBlock
	//saveStatusChanges
	
	
}
?>