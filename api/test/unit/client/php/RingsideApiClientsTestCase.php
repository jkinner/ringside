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

require_once('ringside/api/clients/RingsideApiClients.php');

/**
 * Basic testing of the Ringside API Client core class.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */

class RedirectingClient extends RingsideApiClients {
	public $redirect = null;

	public function __construct($api_key, $secret, $webUrl = null, $serverUrl = null, $socialUrl = null, $client = null ) {
    parent::__construct($api_key, $secret, $webUrl, $serverUrl, $socialUrl, $client);
  }
	
	public function redirect($url) {
		$this->redirect = $url;
	}
}

class RingsideApiClientsTestCase extends PHPUnit_Framework_TestCase {
	var $old_get;
	var $old_post;
	var $old_request;
	var $old_server;
	
	public function setUp() {
		$this->old_get = &$_GET;
		$this->old_post = &$_POST;
		$this->old_request = &$_REQUEST;
		$this->old_server = &$_SERVER;
	}
	
	public function tearDown() {
		$_GET = &$this->old_get;
		$_POST = &$this->old_post;
		$_REQUEST = &$this->old_request;
		$_SERVER = &$this->old_server;
	}
	
	public function testNetworkIds() {
		$params = array(
			'session_key'	=>	'session-key',
			'user'			=> '10000',
			'nid'			=> '2',
			'nuser'			=> '20000',	
			'in_iframe'	=> 0,
			'in_canvas'	=> 1,
			'time'			=> time(),
			'added'			=>	1,
			'api_key'		=>	'api-key'
		);
		$_GET = &$params;
		$_GET['fb_sig'] = Facebook::generate_sig($_GET, 'secret');
		foreach ( $_GET as $key => $value ) {
			if ( strncmp($key, 'fb_sig', 6) === 0 ) continue;
			$_GET['fb_sig_'.$key] = $value;
			unset($_GET[$key]);
		}
		$ringside = new RingsideApiClients('api-key', 'secret', 'http://localhost/web/url', 'http://localhost/server/url', 'http://localhost/social/url');
		$this->assertEquals('10000', $ringside->get_loggedin_user());
		$this->assertEquals('2', $ringside->get_network_id());
		$this->assertEquals('20000', $ringside->get_network_user());
	}
	
	public function testNetworkRedirect() {
	    $_SERVER['HTTP_HOST'] = 'localhost';
	    $_SERVER['REQUEST_URI'] = '/web/url';
	    $params = array(
	      'soc_session_key' => '',
			'session_key'	=>	'session-key',
			'user'			=> '10000',
			'in_iframe'	=> 0,
			'in_canvas'	=> 1,
			'time'			=> time(),
			'added'			=>	1,
			'api_key'		=>	'api-key'
		);
		$_GET = &$params;
		$_GET['fb_sig'] = Facebook::generate_sig($_GET, 'secret');
		foreach ( $_GET as $key => $value ) {
			if ( strncmp($key, 'fb_sig', 6) === 0 ) continue;
			$_GET['fb_sig_'.$key] = $value;
			unset($_GET[$key]);
		}
		$ringside = new RedirectingClient('api-key', 'secret', 'http://localhost/web/url', 'http://localhost/server/url', 'http://localhost/social/url');
		$this->assertEquals('10000', $ringside->get_loggedin_user());
		$this->assertNull($ringside->get_network_id());
		$this->assertNull($ringside->get_network_user());
		$ringside->require_network_login();
		// Confirm that the client forced a redirect; we don't care what PHP thinks the current URL is
		$this->assertEquals('http://localhost/social/url/map.php?v=1.0&method=map&api_key=api-key&snid=&sid=10000&social_session_key=&session_key=session-key&next='.urlencode(Facebook::current_url()).'&canvas', $ringside->redirect);
	}
}
?>