<?php

require_once( 'ringside/api/config/RingsideApiConfig.php' );
require_once( 'ringside/social/config/RingsideSocialConfig.php' );
require_once( 'ringside/web/config/RingsideWebConfig.php' );

include_once 'ringside/api/ServiceFactory.php';

class RingsideWebFixtures
{

	public static function installLocalDomain()
	{
		include 'LocalSettings.php';
		
		$domainService = Api_ServiceFactory::create('DomainService');
		$localDomainId = $domainService->getNativeIdByName('Ringside');
		if ($localDomainId != NULL)  $domainService->deleteDomain($localDomainId);
		
		//$domainService->createDomain('Ringside', "http://$myHostPort", $networkKey, $socialSecretKey);	
		$domainService->createDomain('Ringside', "http://$myHostPort", 'xxxyyy', 'zzzwwww');
	}

	public static function installApps()
	{
		include 'LocalSettings.php';
	
		$appService = Api_ServiceFactory::create('AppService');
		$props = array();		
		$props['callback_url'] = '';
		$props['canvas_url'] = 'ringside';
		$props['sidenav_url'] = 'ringside';
		$props['isdefault'] = 1;		
		$props['canvas_type'] = 1; 
	
		$apiKey = RingsideSocialConfig::$apiKey;
		$secret = RingsideSocialConfig::$secretKey;

		$appId = 100109;
		$name = 'ringside';
		
		$appService->createApp($name, $apiKey, $secret, $props, $appId);
		
		
		$props = array();		
		$props['callback_url'] = "$demoUrl/fbfootprints/footprints/";
		$props['canvas_url'] = 'footprints';
		$props['sidenav_url'] = 'sideUrl 1';
		$props['isdefault'] = 0;		
		$props['canvas_type'] = 1; 
		$props['icon_url'] = "$demoUrl/images/icon-footprints.png";
		$props['description'] = 'Footprints is a demo application from Facebook.';
	
		$apiKey = '4333592132647f39255bb066151a2099';
		$secret = 'b37428ff3f4320a7af98b4eb84a4aa99';

		$appId = 100101;
		$name = 'Footprints';
		
		$appService->createApp($name, $apiKey, $secret, $props, $appId);
		
		
		$props = array();		
		$props['callback_url'] = "$demoUrl/espn/";
		$props['canvas_url'] = 'espn';
		$props['sidenav_url'] = 'sideUrl 1';
		$props['isdefault'] = 0;		
		$props['canvas_type'] = 1; 
		$props['icon_url'] = "$webUrl/images/default-app.png";
		$props['description'] = 'ESPN Headline News';
	
		$apiKey = 'espnkey';
		$secret = 'espnsecret';

		$appId = 100210;
		$name = 'ESPN Social Network';
		
		$appService->createApp($name, $apiKey, $secret, $props, $appId);
		
		
		$props = array();		
		$props['callback_url'] = "http://www.last.fm/opensocial/myfavouritemusic.xml";
		$props['canvas_url'] = 'lastfm';		
		$props['isdefault'] = 0;		
		$props['desktop'] = 0;
		$props['developer_mode'] = 0;
		$props['mobile'] = 0;
		$props['deployed'] = 0;
		$props['canvas_type'] = 2; 
		$props['icon_url'] = "http://cdn.last.fm/matt/nice_favicon.png";
		$props['description'] = 'This application allows you to play at your LastFM channels using an OpenSocial gadget.';
		$props['default_column'] = 1;
	
		$apiKey = 'f3ba8debc58637bcd2a74e1cfdd8b4f1';
		$secret = '796aa6bc8d81d958847eb38e85761882';

		$appId = 100202;
		$name = 'LastFM';
		
		$appService->createApp($name, $apiKey, $secret, $props, $appId);
		
		$props = array();		
		$props['callback_url'] = "http://www.sortr.com/social_gadgets/youtube_xml.php";
		$props['canvas_url'] = 'youtube';		
		$props['isdefault'] = 0;		
		$props['desktop'] = 0;
		$props['developer_mode'] = 0;
		$props['mobile'] = 0;
		$props['deployed'] = 0;
		$props['canvas_type'] = 2; 
		$props['icon_url'] = "http://www.youtube.com/favicon.ico";
		$props['description'] = 'Recommend movies to your friends.';
		$props['default_column'] = 1;
	
		$apiKey = '94f54af54dbcdfa9b1542a2a16bce68e';
		$secret = 'cc223c93558f876eab794e62f9711a36';

		$appId = 100213;
		$name = 'youtube';
		
		$appService->createApp($name, $apiKey, $secret, $props, $appId);
		
		
		$props = array();		
		$props['callback_url'] = "$socialUrl/trust.php";
		$props['canvas_url'] = '';		
		$props['isdefault'] = 0;		
		$props['desktop'] = 0;
		$props['developer_mode'] = 0;
		$props['mobile'] = 0;
		$props['deployed'] = 0;
		$props['canvas_type'] = 1; 
		$props['icon_url'] = "$webUrl/images/default-app.png";
		$props['description'] = 'This application exists only to support the Trail Map 7 widget demo and will not run in a canvas.';
		$props['default_column'] = 1;
	
		$apiKey = 'cecdc629adb83f81236ba9342ce68492';
		$secret = 'c2161e95173c45b4837ef5b1454fbd65';

		$appId = 100207;
		$name = 'RS Widget Demo';
		
		$appService->createApp($name, $apiKey, $secret, $props, $appId);
		
		
		$props = array();		
		$props['callback_url'] = "$socialUrl/gadgets/files/tests/compliancetests.xml";
		$props['canvas_url'] = 'compliancetests';		
		$props['isdefault'] = 0;		
		$props['desktop'] = 0;
		$props['developer_mode'] = 0;
		$props['mobile'] = 0;
		$props['deployed'] = 0;
		$props['canvas_type'] = 2; 
		$props['icon_url'] = "$webUrl/images/comply.gif";
		$props['description'] = 'OpenSocial 0.7 Compliance Tests. Determines if your OpenSocial container conforms to the OS specification.';
		$props['default_column'] = 1;
	
		$apiKey = 'd9f41885c9c34bd53364cf1d611efecd';
		$secret = 'f12c83ba65d7f8cb1d11efce1631eaad';

		$appId = 100208;
		$name = 'OS Compliance Tests';
		
		$appService->createApp($name, $apiKey, $secret, $props, $appId);
		
		
		$props = array();		
		$props['callback_url'] = "$socialUrl/gadgets/files/tests/ringsidetests.xml";
		$props['canvas_url'] = 'ringsidetests';		
		$props['isdefault'] = 0;		
		$props['desktop'] = 0;
		$props['developer_mode'] = 0;
		$props['mobile'] = 0;
		$props['deployed'] = 0;
		$props['canvas_type'] = 2; 
		$props['icon_url'] = "$webUrl/images/ringside.gif";
		$props['description'] = 'Runs functional tests on OpenSocial\'s mapping to Ringside data.';
		$props['default_column'] = 1;
	
		$apiKey = '1882266fc526e79fbd51438f2fb17183';
		$secret = 'ccbfc51f8c517aaa91a2de4917c29713';

		$appId = 100214;
		$name = 'Ringside Tests';
		
		$appService->createApp($name, $apiKey, $secret, $props, $appId);
	}
}

?>