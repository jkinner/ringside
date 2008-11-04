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

/**
 * Document this file.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */

abstract class NetworkChannel
{
	public static $network_classes = array();
	
	private $network_key;
	private $restserver_url;
	private $login_url;
	private $app_url;

	private $channel_context;
	
	private $api_key;
	private $secret;
	private $session;
	private $user;
	private $expires;
	
	public function __construct($network_key, $restserver_url, $login_url, $app_url)
	{
		$this->network_key = $network_key;
		$this->restserver_url = $restserver_url;
		$this->login_url = $login_url;
		$this->app_url = $app_url;
	}
	
	public static function create($channel_name, $params)
	{
		$class = self::$network_classes[$channel_name];
		return new $class($params['key'], $params['restserver_url'], $params['login_url'], $params['canvas_url']);
	}
	
	public function setApplication($api_key, $session)
	{
		$this->api_key = $api_key;
		$this->session = $session;
	}
	
	public function setUser($user, $session, $expires)
	{
		$this->user = $user;
		$this->session = $session;
		$this->expires = $expires;
	}
	
	public static function createUserCookies(NetworkSigner $signer, $network_key = null)
	{
		if ( isset($this->api_key) )
		{
			$cookies_to_set = array();
	      $cookies = array();
	      $cookies['user'] = $user;
	      $cookies['session_key'] = $session_key;
	      $sig = $signer->sign($cookies, $this->secret);
	      foreach ($cookies as $name => $val)
	      {
	      	$cookies_to_set[($network_key !== null?$network_key:'').$this->api_key . '_' . $name] = $val;
	      }
	      $cookies_to_set[($network_key !== null?$network_key:'').$this->api_key] = $sig;
		}
	}
	
	abstract protected function callMethod($name, $params);
}
?>