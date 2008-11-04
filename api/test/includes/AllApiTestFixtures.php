<?php

include_once 'ringside/api/ServiceFactory.php';

class AllApiTestFixtures
{

	public static function create()
	{	
		self::createLocalDomain();
		self::createApps();	
	}
	
	public static function createLocalDomain()
	{
		$domainService = Api_ServiceFactory::create('DomainService');
		$localDomainId = $domainService->getNativeIdByName('Ringside');
		if ($localDomainId == NULL) $domainService->createDomain('Ringside', 'http://localhost', 'test-api-key', 'test-secret');
	}
	
	
	public static function createApps()
	{
		$appService = Api_ServiceFactory::create('AppService');
		
		
		// application (1200)
		$appId = 11000;
		if (! $appService->appExists($appId)) {
			$props = array();
			$props['callback_url'] = 'http://url.com/callback';
			$props['canvas_url'] = 'app1200';
			$props['sidenav_url'] = 'app1200';
			$props['isdefault'] = 0;
			$props['support_email'] = 'jack@mail.com';
			$props['canvas_type'] = 1; 
			$props['application_type'] = 1;
			$props['mobile'] = 0;
			$props['deployed'] = 1;
			$props['description'] = 'Greatest appplication ever!';
			$props['default_fbml'] = '<fb:name />';		
			$props['postadd_url'] = 'http://url.com/postadd';
			$props['postremove_url'] = 'http://url.com/postremove';
			$props['privacy_url'] = 'http://url.com/privacy';
			$props['ip_list'] = '12.34.56.78';
			$props['about_url'] = 'http://url.com/about'; 
			$apiKey = 'application-1200';
			$secret = 'application-1200';
			
			$name = 'Application 1200';
			$nid = $appService->createApp($name, $apiKey, $secret, $props, $appId);
		}
		
		
		// car app (8100)
		$appId = 8100;
		if (! $appService->appExists($appId)) {
			$props = array();
			$props['callback_url'] = 'localhost://';
			$props['canvas_url'] = 'auto';
			$props['sidenav_url'] = 'sideUrl 1';
			$props['isdefault'] = 0;
			$apiKey = 'test_case_key_8100';
			$secret = 'secrety_key_1000';
			
			$name = 'car app';
			$nid = $appService->createApp($name, $apiKey, $secret, $props, $appId);
		}
		
		$appId = 16200;
		if (! $appService->appExists($appId)) {
			// car app (16200)
	 		$apiKey = 'test_case_key-16200';
	 		$secret = 'secretkey';
	 		
	 		$name = 'car app';
	 		$nid = $appService->createApp($name, $apiKey, $secret, $props, $appId);
		}
		
		$appId = 8101;
		if (! $appService->appExists($appId)) {
			// coffee app (8101)
			$props = array();
			$props['callback_url'] = 'localhost://';
			$props['canvas_url'] = 'coffee';
			$props['sidenav_url'] = 'sideUrl 2';
			$props['isdefault'] = 0;
			$apiKey = 'test_case_key_8101';
			$secret = 'secrety_key_1001';
			
			$name = 'coffee app';
			$nid = $appService->createApp($name, $apiKey, $secret, $props, $appId);
		}
		
		
		
		$appId = 8102;
		if (! $appService->appExists($appId)) {
			// coffee app (8102)
			$props = array();
			$props['callback_url'] = 'localhost://';
			$props['canvas_url'] = 'coffee';
			$props['canvas_type'] = 1;
			$props['sidenav_url'] = 'sideUrl 2';
			$props['isdefault'] = 1;
			$apiKey = 'test_case_key_8102';
			$secret = 'secrety_key_1002';
			
			$name = 'coffee app';
			$nid = $appService->createApp($name, $apiKey, $secret, $props, $appId);
		}
		
		
		$appId = 17100;
		if (! $appService->appExists($appId)) {
			// test app (17100)
			$props = array();
			$props['callback_url'] = 'theCallbackUrl';
			$props['canvas_url'] = 'theCanvasUrl';
			$props['sidenav_url'] = 'theSideNavUrl';
			$props['isdefault'] = 1;
			$props['support_email'] = 'nobody@nowhere.com';
			$props['canvas_type'] = 1; 
			$props['application_type'] = 1;
			$props['mobile'] = 0;
			$props['deployed'] = 1;
			$props['description'] = 'Client test app';
			$props['default_fbml'] = '<fb:name />';		
			$props['postadd_url'] = 'http://url.com/postadd';
			$props['postremove_url'] = 'http://url.com/postremove';
			$props['privacy_url'] = 'http://url.com/privacy';
			$props['ip_list'] = '';
			$props['about_url'] = 'http://url.com/about';
			$props['author'] = 'John Q';  
			$props['author_url'] = 'http://www.jonq.tv';
			$props['author_description'] = 'John Q Industries';
			$apiKey = 'test_case_key-17100';
			$secret = 'secretkey';
			
			$name = 'test app';
			$nid = $appService->createApp($name, $apiKey, $secret, $props, $appId);
		}

		
		$appId = 34000;
		if (! $appService->appExists($appId)) {
			/**
	         * We need some apps populated for the COmments testing 
	         * Using 34000
	         */
	 		$apiKey = 'test_case_key-34000';
	 		$secret = 'secretkey';
	 		
	 		$name = 'comments';
	 		$nid = $appService->createApp($name, $apiKey, $secret, $props, $appId);
		}
 		
		$appId = 34001;
		if (! $appService->appExists($appId)) {
	 		$props['isdefault'] = 0;
	 		$apiKey = 'test_case_key-34001';
	 		$secret = 'secretkey';
	 		
	 		$name = 'rackets';
	 		$nid = $appService->createApp($name, $apiKey, $secret, $props, $appId);
		}
 		
		
		$appId = 36000;
 		/**
         * Payments 
         * Using 36000
         */
		if (! $appService->appExists($appId)) {
	        $props['isdefault'] = 0;
	 		$props['canvas_url'] = 'canvasurl';
	 		$props['sidenav_url'] = 'sideurl';
	  		$apiKey = 'test_case_key-36000';
	  		$secret = 'secretkey';
	  		
	  		$name = 'appname';
	  		$nid = $appService->createApp($name, $apiKey, $secret, $props, $appId);
		}	
  		
		$appId = 36050;
		if (! $appService->appExists($appId)) {
	  		$props['isdefault'] = 0;
			$props['canvas_url'] = 'canvasurl';
			$props['sidenav_url'] = 'sideurl';
	 		$apiKey = 'test_case_key-36050';
	 		$secret = 'secretkey';
	 		
	 		$name = 'appname';
	 		$nid = $appService->createApp($name, $apiKey, $secret, $props, $appId);
		}
 		
		$appId = 36110;
		if (! $appService->appExists($appId)) {
	 		$props['isdefault'] = 1;
			$props['canvas_url'] = 'canvasurl';
			$props['sidenav_url'] = 'sideurl';
	 		$apiKey = 'test_case_key-36110';
	 		$secret = 'secretkey';
	 		
	 		$name = 'appname';
	 		$nid = $appService->createApp($name, $apiKey, $secret, $props, $appId);
		}

		
		
		$appId = 36060;
		if (! $appService->appExists($appId)) {
		 	$props['isdefault'] = 1;
			$props['canvas_type'] = 1;
			$props['canvas_url'] = 'canvasurl';
			$props['sidenav_url'] = 'sideurl';
		 	$apiKey = 'test_case_key-36060';
		 	$secret = 'secretkey';
		 	
		 	$name = 'appname';
		 	$nid = $appService->createApp($name, $apiKey, $secret, $props, $appId);
		}
 		
		
		
 		/**
         * Testing for UsersGetAppSession
         */
		$appId = 35000;
		if (! $appService->appExists($appId)) {
	        $props['isdefault'] = 0;
	 		$props['canvas_type'] = 1;
	 		$props['canvas_url'] = 'canvasurl';
	 		$props['sidenav_url'] = 'sideurl';
	  		$apiKey = 'test_case_key-35000';
	  		$secret = 'secretkey';
	  		
	  		$name = 'uas-no-default';
	  		$nid = $appService->createApp($name, $apiKey, $secret, $props, $appId);
	  		
	  		$props['isdefault'] = 1;
	 		$props['canvas_type'] = 1;
	 		$props['canvas_url'] = 'canvasurl';
	 		$props['sidenav_url'] = 'sideurl';
	  		$apiKey = 'test_case_key-35001';
	  		$secret = 'secretkey';
	  		$appId = 35001;
	  		$name = 'uas-is-default';
	  		$nid = $appService->createApp($name, $apiKey, $secret, $props, $appId);
		}
  		
		$appId = 3100;
		if (! $appService->appExists($appId)) {
	  		/*
	        * friends
	        */
	        $props['isdefault'] = 0;
	 		$props['canvas_type'] = 1;
	 		$props['canvas_url'] = 'auto';
	 		$props['sidenav_url'] = 'sideUrl 1';
	  		$apiKey = 'test_case_key_3100';
	  		$secret = 'secretkey';
	  		
	  		$name = 'car app';
	  		$nid = $appService->createApp($name, $apiKey, $secret, $props, $appId);
		}
  		
  		/*
        * testing user auth
        */
        $appId = 6100;
        if (! $appService->appExists($appId)) {
	        $props['isdefault'] = 0;
	 		$props['canvas_type'] = 1;
	 		$props['canvas_url'] = 'auto';
	 		$props['sidenav_url'] = 'sideUrl 1';
	  		$apiKey = 'test_case_key-6100';
	  		$secret = 'secretkey';
	  		
	  		$name = 'car app';
	  		$nid = $appService->createApp($name, $apiKey, $secret, $props, $appId);
        }
  		
  		/** FBML TESTS (7000)**/
        $appId = 7100;
        if (! $appService->appExists($appId)) {
	  		$props['isdefault'] = 0;
	 		$props['canvas_type'] = 1;
	 		$props['canvas_url'] = 'auto';
	 		$props['sidenav_url'] = 'sideUrl 1';
	  		$apiKey = 'test_case_key-7100';
	  		$secret = 'secretkey';
	  		
	  		$name = 'car app';
	  		$nid = $appService->createApp($name, $apiKey, $secret, $props, $appId);
        }
  		
        $appId = 7101;
        if (! $appService->appExists($appId)) {
		  	$props['isdefault'] = 0;
		 	$props['canvas_type'] = 1;
		 	$props['canvas_url'] = 'auto';
		 	$props['sidenav_url'] = 'sideUrl 1';
		  	$apiKey = 'test_case_key-7101';
		  	$secret = 'secretkey';
		  	
		  	$name = 'car app';
		  	$nid = $appService->createApp($name, $apiKey, $secret, $props, $appId);
        }
  		
        
        $appId = 7102;
        if (! $appService->appExists($appId)) {
	  		$props['isdefault'] = 1;
	 		$props['canvas_type'] = 1;
	 		$props['canvas_url'] = 'auto';
	 		$props['sidenav_url'] = 'sideUrl 1';
	  		$apiKey = 'test_case_key-7102';
	  		$secret = 'secretkey';
	  		
	  		$name = 'car app';
	  		$nid = $appService->createApp($name, $apiKey, $secret, $props, $appId);
        }
  		
  		/**
         * EVENTS GET TESTS (4000) 
         */
        $appId = 4100;
        if (! $appService->appExists($appId)) {
	        $props['isdefault'] = 0;
	 		$props['canvas_type'] = 1;
	 		$props['canvas_url'] = 'auto';
	 		$props['sidenav_url'] = 'sideUrl 1';
	  		$apiKey = 'test_case_key-4100';
	  		$secret = 'secretkey';
	  		
	  		$name = 'car app';
	  		$nid = $appService->createApp($name, $apiKey, $secret, $props, $appId);
        }
  		
  		/**
         * Admin calls - 110xx
         */
        $appId = 11001;
        if (! $appService->appExists($appId)) {
	        $props = array();
			$props['callback_url'] = 'theCallbackUrl';
			$props['canvas_url'] = 'app1201';
			$props['sidenav_url'] = 'app1201';
			$props['isdefault'] = 0;
			$props['support_email'] = 'jeff@mail.com';
			$props['canvas_type'] = 1;
			$props['description'] = 'Second Greatest appplication ever!';
			$props['default_fbml'] = '<fb:name />';		
			$props['postadd_url'] = 'http://url.com/postadd';
			$props['postremove_url'] = 'http://url.com/postremove';
			$props['privacy_url'] = 'http://url.com/privacy';
			$props['ip_list'] = '12.34.56.78';
			$props['about_url'] = 'http://url.com/about';
	   		$apiKey = 'test_case_key-11001';
	   		$secret = 'secretkey';
	   		
	   		$name = 'Iam11001';
	   		$nid = $appService->createApp($name, $apiKey, $secret, $props, $appId);
        }
   		
   		/**
         * Testing feeds from a data perspective only really needs 
         * friends information and user_app info.  Everything else is 
         * new inserts. 
         * Using 16000
         */
        $appId = 16100;
        if (! $appService->appExists($appId)) {
	        $props['isdefault'] = 0;
	  		$props['canvas_type'] = 1;
	  		$props['canvas_url'] = 'auto';
	  		$props['sidenav_url'] = 'sideUrl 1';
	   		$apiKey = 'test_case_key-16100';
	   		$secret = 'secretkey';
	   		
	   		$name = 'car app';
	   		$nid = $appService->createApp($name, $apiKey, $secret, $props, $appId);
        }
	}
	
	public static function destroyApps()
	{
		$appService = Api_ServiceFactory::create('AppService');
		$appService->deleteApp(11000);
		$appService->deleteApp(8100);
		$appService->deleteApp(8101);
		$appService->deleteApp(8102);
		$appService->deleteApp(17100);
		$appService->deleteApp(34000);
		$appService->deleteApp(34001);
		$appService->deleteApp(36000);
		$appService->deleteApp(36110);
		$appService->deleteApp(36060);
		$appService->deleteApp(35000);
		$appService->deleteApp(35001);
		$appService->deleteApp(3100);
		$appService->deleteApp(6100);
		$appService->deleteApp(7100);
		$appService->deleteApp(7101);
		$appService->deleteApp(7102);
		$appService->deleteApp(4100);
		$appService->deleteApp(11001);
		$appService->deleteApp(16100);
	}
	
	public static function destroy()
	{
		self::destroyApps();	
	}


}

AllApiTestFixtures::destroy();
AllApiTestFixtures::create();


?>