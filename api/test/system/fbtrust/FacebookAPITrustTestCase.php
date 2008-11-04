<?php
require_once('simpletest/web_tester.php');
require_once('FacebookTestConfig.php');
require_once('ringside/test/RingsideWebTestUtils.php');

class FacebookAPITrustTestCase extends WebTestCase {
	var $old_include_path;
	
	public function setUp() {
		parent::setUp();
		$this->old_include_path = set_include_path('../conf'.PATH_SEPARATOR.get_include_path());
	}
	
	public function tearDown() {
		parent::tearDown();
		set_include_path($this->old_include_path);
	}
	
	public function testItemsSetInfo() {
		// The configuration file MUST be configured to show a Ringside-enabled application canvas
		// that is configured to send API requests to the local Ringside server.
//		error_log("Starting testItemsSetInfo");
		include('FacebookTestConfig.php');
//		error_log("testItemsSetInfo: configured");
		$this->restart();
		// Apparently, Facebook checks user agent strings. Pretend to be Safari on an Intel Mac.
		$this->getBrowser()->addHeader('User-Agent: Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en-us) AppleWebKit/523.15.1 (KHTML, like Gecko) Version/3.0.4 Safari/523.15');
		$this->get($local_callback_url);
//		echo "Accessing $local_callback_url\n";
		// Login will need to happen
		$this->assertResponse(array(200));
//		error_log("testItemsSetInfo: at login page");
//		echo $this->getBrowser()->getContent();
		$this->assertFieldByName('next');
		$this->assertFieldByName('api_key', $facebook_api_key);
		$this->assertFieldByName('email');
		$this->assertFieldByName('pass');
		$this->setMaximumRedirects(0);
		$login_form_post = 		array(
			'next'		=>		$local_callback_url,
			'version'	=>		'1.0',
			'api_key'	=>		$facebook_api_key,
			'email'		=>		$facebook_email,
			'pass'		=>		$facebook_password,
			'login'		=>		'Login'
		);
//		var_dump($login_form_post);
//		error_log("testItemsSetInfo: logging in");
		$this->post('https://login.facebook.com/login.php',$login_form_post);
		$this->assertResponse(array(302));
//		error_log("testItemsSetInfo: logged in");
		$headers = $this->getBrowser()->getHeaders();
		// Skip the HTTP protocol response line, which is included by default
		$headers = preg_replace(',^HTTP/[0-9.]*.*$,', '', $headers);
		// This appears to be required by the browser; removing it causes the test to terminate
		$content = $this->getBrowser()->getContent();
		$realheaders = RingsideWebTestUtils::parse_headers($headers);
		$location = $realheaders['location'][0];
		
		$this->assertTrue(preg_match(',^'.str_replace('.', '\.', $local_callback_url).',', $location), "Redirect from Facebook did not redirect back to callback URL. Ensure that the test user has the application added on Facebook.");

//		echo "Following redirect to ".$realheaders['location'][0]."\n";
//		error_log("testItemsSetInfo: following redirect to $location");
		$this->get($location);
		$content = $this->getBrowser()->getContent();
//		error_log("testItemsSetInfo: finished redirect to $location");
		$this->assertResponse(array(200));
		
		$final_content = preg_replace(',<br */?>,', "\n", $this->getBrowser()->getContent());
//		error_log("testItemsSetInfo: have final page\n$final_content");
//		echo $this->getBrowser()->getContent()."\n";
		// Now facebook will redirect to the local server; we make sure the result is correct
		$this->assertWantedText("Test Passed", "Remote test reported:\n$final_content");
	}
}
?>