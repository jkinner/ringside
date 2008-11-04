<?php
 /*
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
  */

require_once('simpletest/web_tester.php');
require_once('SuiteTestUtils.php');
require_once('RingsideWebTestConfig.php');
require_once('ringside/web/config/RingsideWebConfig.php');
require_once('ringside/social/config/RingsideSocialConfig.php');

/**
 * Tests identity mapping through the social renderer, to the app, back to the client.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
class IdentityMapTestCase extends WebTestCase {
	private function getAppUrl() {
		return RingsideWebTestConfig::$server.RingsideWebTestConfig::$webRoot.'/canvas.php/idmap/';
	}
	
	private function cleanDb()
	{
		
		$sqlDelete = "
		DELETE FROM users_app where app_id = 90000;
		DELETE FROM app where id = 90000;
		DELETE FROM users WHERE id > 200000 && id < 201000; 
		DELETE FROM principal_map where principal_id > 50000 && principal_id < 51000;
		DELETE FROM principal where principal_id > 50000 && principal_id < 51000;
		";

		mysql_upload_string($sqlDelete);
	}
	
	public function setUp()
	{
		$url = $this->getAppUrl();
		$serverUrl = RingsideWebTestConfig::$server;
		$this->cleanDb();
		mysql_upload_string("
			insert into users (id, username, password) values (200001, 'mytest@goringside.net', '".sha1('ringside')."');
			insert into users (id, username, password) values (200002, 'mytest2@goringside.net', '".sha1('ringside')."');
			insert into principal (principal_id, user_name) values (50001, 'mytest@goringside.net');
			insert into principal_map (principal_id, uid, trust_key, network_key, user_name)
			values (50001, 300001, 'facebook', '1234', 'mytest@goringside.net');
			insert into principal_map (principal_id, uid, trust_key, network_key, user_name)
			values (50001, 200001, 'ringside-web', '".RingsideSocialConfig::$apiKey."', 'mytest@goringside.net');
			insert into principal (principal_id, user_name) values (50002, 'mytest2@goringside.net');
			insert into principal_map (principal_id, uid, trust_key, network_key, user_name)
			values (50002, 300002, 'facebook', '1234', 'mytest2@goringside.net');
			insert into principal_map (principal_id, uid, trust_key, network_key, user_name)
			values (50002, 200002, 'ringside-web', '".RingsideSocialConfig::$apiKey."', 'mytest2@goringside.net');
			-- Deploy the application to all test users
			INSERT INTO app (id, callback_url, api_key, secret_key, name, canvas_url, sidenav_url, isdefault, support_email, canvas_type, application_type, mobile, deployed, description, default_fbml, tos_url, icon_url, postadd_url, postremove_url, privacy_url, ip_list, about_url) VALUES (90000, '$serverUrl/idmapapp/', 'idmapapp', 'idmapappsecret', 'Identity Mapping Application', 'idmap', 'idmap', 0, null, 1, 'WEB', 0, 1, null, null, null, null, null, null, null, null, null);
			INSERT INTO users_app (app_id, user_id, allows_status_update, allows_create_listing, allows_photo_upload, fbml, auth_information, auth_profile, auth_leftnav, auth_newsfeeds, enabled, modified, created) SELECT 90000, id, 1, 1, 1, null, 1, 1, 1, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP from users WHERE users.id > 200001 && users.id < 201000;
			");
	}

	public function tearDown()
	{
		$this->cleanDb();
	}
	
	public function testIdMapNoNetworkKey() {
		$url = $this->getAppUrl().'add_data.php';
		$loginform = array(
			'email'	=>	'mytest@goringside.net',
			'p'		=>	'ringside',
			'next'	=> $url 
		);
		$this->restart();
		// No longer allow redirects, since we should be logged in properly
		// I need to be logged in, now
		$this->post(RingsideWebTestConfig::$server.RingsideWebConfig::$webRoot.'/login.php', $loginform);
		$this->assertCookie('PHPSESSID');
		// Because we are using the social container's key, this will perform a network context mapping to the principal ID
		$this->assertText('Result{50001}');
		$this->get($url);
		$this->assertText('Result{50001}');
	}

	public function testReadWithNetworkKey() {
		$url = $this->getAppUrl().'add_data.php';
		$loginform = array(
			'email'	=>	'mytest@goringside.net',
			'p'		=>	'ringside',
			'next'	=> $url 
		);
		$this->restart();
		// No longer allow redirects, since we should be logged in properly
		// I need to be logged in, now
		echo "Accessing URL $url\n";
		// Since this test uses the network context of the social container, this will map to the principal ID
		$this->post(RingsideWebTestConfig::$server.RingsideWebConfig::$webRoot.'/login.php', $loginform);
		$this->assertCookie('PHPSESSID');
		$this->assertText('Result{50001}');
		
		// Now we switch up to read using the network ID
		$url = $this->getAppUrl().'get_data.php';
		if ( strpos($url, '?') !== false ) {
			$url .= '&nid=1234';
		} else {
			$url .= '?nid=1234';
		}
		
		// And this will map from the principal ID to the '1234' network's subject ID
		$this->get($url);
		$this->assertText('Result{300001}');

		// Now we map the stored UID to the current network, which should result in the local network's uid
		$url = $this->getAppUrl().'get_data.php';
		$this->get($url);
		if (! $this->assertText('Result{200001}') ) {
			error_log($this->getBrowser()->getContent());
		}
	}
}
?>