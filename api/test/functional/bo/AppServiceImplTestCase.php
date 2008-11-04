<?php

require_once 'BaseBoTestCase.php';
require_once 'ringside/api/bo/App.php';
require_once 'ringside/api/ServiceFactory.php';

class AppServiceImplTestCase extends BaseBoTestCase
{	
	public function testStuff()
	{
		$appService = Api_ServiceFactory::create('AppService');	
		$this->assertTrue($appService != NULL);
		
		$props = $this->getTestAppProps();
		$name = 'mynewapp22';
		$apiKey = '3821857235nnd';
		$secret = 'adf2893fh232323';		
		
		//test create
		$appId = $appService->createApp($name, $apiKey, $secret, $props);
		
		//test getAllApps
		$allApps = $appService->getAllApps();
		//error_log(var_export($allApps,true));
		$this->assertTrue(count($allApps)>0);
		$this->assertTrue(array_key_exists("canvas_url",$allApps[0]));

		//test query by property
		$ids = $appService->getNativeIdsByProperty('canvas_url', $props['canvas_url']);
		$this->assertNotNull($ids);
		$this->assertEquals(1, count($ids));
		$this->assertEquals($appId, $ids[0]);

		//test exists
		$this->assertTrue($appService->appExists($appId));
		
		//test query by api key
		$id = $appService->getNativeIdByApiKey($apiKey);
		$this->assertEquals($appId, $id);
		
		//test getApp
		$app = $appService->getApp($appId);
		$this->assertEquals($name, $app['name']);
		foreach ($app as $aname => $aval) {
			if (!in_array($aname, array('id','created','modified','name'))) {
				$this->assertEquals($props[$aname], $aval);
			}
		}
		
		//test update
		$newAuthor = 'Goats McGoat';
		$newProps = array('author' => $newAuthor);
		$appService->updateApp($appId, $newProps);
		$app = $appService->getApp($appId);
		$this->assertEquals($newAuthor, $app['author']);
		
		//test delete
		$appService->deleteApp($appId);
		
		//test create with id specified
		$appId = 44;
		$createdId = $appService->createApp($name, $apiKey, $secret, $props, $appId);
		$this->assertEquals($appId, $createdId);
		
		$appService->deleteApp($appId);
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