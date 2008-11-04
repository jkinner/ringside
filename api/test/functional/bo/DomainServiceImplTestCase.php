<?php

require_once 'ringside/api/bo/DomainService.php';
require_once 'ringside/api/bo/AppService.php';
require_once 'ringside/api/ServiceFactory.php';

class DomainServiceImplTestCase extends PHPUnit_Framework_TestCase
{

  private $domainService;
  private $aDomain;
  private $anApp;

  public function setUp()
  {
    $this->domainService = Api_ServiceFactory::create('DomainService');
    $this->aDomain = $this->domainService->createDomain("my_guac","http://some_guac.com","some_api_key","some_secret");
  }
  
  public function testServiceFactoryCreatedDomainServiceObject()
  {
    $this->assertTrue($this->domainService != NULL);
  }
  
  public function testCreatingDomainObject()
  {
    $this->assertEquals($this->aDomain["name"],"my_guac");
    $this->assertEquals($this->aDomain["url"],"http://some_guac.com");
  }
  
  public function testGetDomain()
  {
    $this->assertTrue($this->domainService->getDomain($this->aDomain["id"]) != null);
  }
  
  public function testGetAllDomains()
  {
    $this->domainService->createDomain("site1","http://site1.com","some_api_key1","some_secret1");
    $this->domainService->createDomain("site2","http://site2.com","some_api_key2","some_secret2");
    $this->domainService->createDomain("site3","http://site3.com","some_api_key3","some_secret3");
    
    $domains = $this->domainService->getAllDomains();
    
    // dont access array element 0 because it is a class var
    $this->assertEquals($domains[1]["name"],"site1");
    $this->assertEquals($domains[2]["name"],"site2");
    $this->assertEquals($domains[3]["name"],"site3");
    
    $this->domainService->deleteDomain($domains[1]["id"]);
    $this->domainService->deleteDomain($domains[2]["id"]);
    $this->domainService->deleteDomain($domains[3]["id"]);

  }
  
  public function testDeleteDomain()
  {
    $domain = $this->domainService->createDomain("some_name","http://some_url.com","an_api_key","a_secret");
    $this->assertTrue($domain!==false);
    $this->domainService->deleteDomain($domain["id"]);
    $this->assertTrue($this->domainService->getDomain($domain["id"])==false);
  }

  
  public function testGetNativeIdByApiKey()
  {
    $this->assertEquals($this->domainService->getNativeIdByApiKey("some_api_key"),$this->aDomain["id"]);
  }
  
  public function testGetNativeIdByName()
  {
    $this->assertEquals($this->domainService->getNativeIdByName("my_guac"),$this->aDomain["id"]);
  }
  
  public function testGetAppsByApiKey()
  {
      $appService = Api_ServiceFactory::create('AppService');	
	  $this->assertTrue($appService != NULL);
	
	  $props = $this->getTestAppProps();
	  $name = 'mschack_goat_lovers_app';
	  $apiKey = '4987198123410234';
	  $secret = 'sf876dfa8s7fi24r12kj4h21';		
	
	  //test create
	  $this->anApp = $appService->createApp($name, $apiKey, $secret, $props, $nativeId = null, $domainKey = "some_api_key");

      $apps = $this->domainService->getAppsByApiKey("some_api_key");
      $null_apps = $this->domainService->getAppsByApiKey("some_api_key_doesnt_exist_ha_ha_ha_should_return_0");
      $appProps = $this->getTestAppProps();
      
      $this->assertEquals(0,count($null_apps));
      $this->assertEquals(1,count($apps));
      $this->assertEquals($apps[0]['canvas_url'],$appProps['canvas_url']);
  }
  
  public function tearDown()
  {
    Api_ServiceFactory::create('KeyService')->deleteKeyset($this->aDomain["id"],$this->aDomain["id"]);
    Api_ServiceFactory::create('AppService')->deleteApp($this->anApp["id"]);
    $this->domainService->deleteDomain($this->aDomain["id"]);
    unset($this->anApp);
    unset($this->aDomain);
    unset($this->domainService);
  }
  
  protected function getTestAppProps()
  {
  	$appProps = array();
  	$appProps['callback_url'] = 'someurl';
  	$appProps['canvas_url'] = 'somecanvasurl';		
  	$appProps['sidenav_url'] = 'somesidenav';
  	$appProps['isdefault'] = 1;
  	$appProps['icon_url'] = 'someiconur;';
  	$appProps['canvas_type'] = 0;
  	$appProps['desktop'] = 0;
  	$appProps['developer_mode'] = 1;
  	$appProps['author'] = 'goats';
  	$appProps['author_url'] = 'anotherurl';
  	$appProps['author_description'] = 'best author ever';
  	$appProps['support_email'] = 'goats@goats.goats';
  	$appProps['application_type'] = 'awesome';
  	$appProps['mobile'] = 0;
  	$appProps['deployed'] = 1;
  	$appProps['description'] = 'this app is awesome';
  	$appProps['default_fbml'] = 'none';
  	$appProps['tos_url'] = 'tos.html';
  	$appProps['postadd_url'] = 'postadd.html';
  	$appProps['postremove_url'] = 'postremove.html';
  	$appProps['privacy_url'] = 'privacy.html';
  	$appProps['ip_list'] = '192.168.0.1';
  	$appProps['about_url'] = 'about.html';
  	$appProps['logo_url'] = 'http://localhost/logo.jpg';
  	$appProps['edit_url'] = 'edit.html';
  	$appProps['default_column'] = 1;
  	$appProps['attachment_action'] = 'attach.html';
  	$appProps['attachment_callback_url'] = 'callback.html';

  	return $appProps;
  }

}
?>