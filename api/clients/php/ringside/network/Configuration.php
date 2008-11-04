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

class NetworkConfiguration {
	private $network_key;
	private $restserver_url;
	private $login_url;
	private $web_url;
	private $canvas_url;
	
//	public function __construct(
//		$network_key,
//		$restserver_url,
//		$login_url,
//		$web_url,
//		$canvas_url
//	) {
//		$this->network_key = $network_key;
//		$this->restserver_url = $restserver_url;
//		$this->login_url = $login_url;
//		$this->web_url = $web_url;
//		$this->canvas_url = $canvas_url;
//	}
	
	private static function returnIfSet($params, $name, $default = null) {
		return isset($params[$name])?$params[$name]:$default;
	}
	
	public static function create($context_config, $prefix = '') {
		$realprefix = strlen($prefix)>0?$prefix.'_':$prefix;
		$config = new NetworkConfiguration();
		$this->network_key = self::returnIfSet($context_config, $realprefix.'network_key');
		$this->restserver_url = self::returnIfSet($context_config, $realprefix.'restserver_url');
		$this->login_url = self::returnIfSet($context_config, $realprefix.'login_url');
		$this->web_url = self::returnIfSet($context_config, $realprefix.'web_url');
		
	}
}
?>