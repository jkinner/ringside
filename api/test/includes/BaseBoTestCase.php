<?php

include_once 'PHPUnit/Framework.php';
include_once 'AllBoTestFixtures.php';

class BaseBoTestCase extends PHPUnit_Framework_TestCase
{
	public function setUp() 
	{
		AllBoTestFixtures::create();	
	}
   
	public function tearDown()
	{
   		AllBoTestFixtures::destroy();
	}	
}


?>