<?php

require_once 'ringside/api/bo/betterFriendsService.php';
require_once 'ringside/api/ServiceFactory.php';

class BetterFriendsServiceImplTestCase extends PHPUnit_Framework_TestCase
{

  private $betterFriendsService;
  private $aFriend;

  public function setUp()
  {
    $this->betterFriendsService = Api_ServiceFactory::create('betterFriendsService');
  }
  
  public function testAdd() {
    $this->assertTrue($this->betterFriendsService->add(1,100,500));
    $this->assertTrue($this->betterFriendsService->add(1,100,501));
  }
  
  public function testGet() {
      //error_log(var_export($this->betterFriendsService->get(1,100),1));
    $this->assertEquals($this->betterFriendsService->get(1,100),array(500,501));
  }
  
  public function testRemove() {
      $this->assertTrue($this->betterFriendsService->remove(1,100,501));
  }
  
  public function testGetAfterRemove() {
      $this->assertEquals($this->betterFriendsService->get(1,100),array(500));
  }


}
?>