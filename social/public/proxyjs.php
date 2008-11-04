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
 * This is a proxy for FBJS Ajax.post invocations.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
require_once('LocalSettings.php');
require_once('ringside/social/ProxyJs.php');

$server = new SocialProxyJs();

$api_key;
$request_url;

error_log($_SERVER['PATH_INFO']);
error_log($_SERVER['QUERY_STRING']);
if ( isset($_SERVER['PATH_INFO']) && isset($_SERVER['QUERY_STRING']) ) {
	$path_info_parts = split('/', $_SERVER['PATH_INFO'], 3);
	$api_key = substr($_SERVER['PATH_INFO'], 1);
	$rest = $_SERVER['QUERY_STRING'];

	if ( preg_match(',^http://[^/]*,', $rest) ) {
		$http_parts = split(',\?,', $rest, 2);
		$params = $_POST;
		if ( isset($http_parts[1]) && $http_parts[1]!='' ) {
			$params = array();
			parse_str($http_parts[1], $params);
		}
		$server->execute($api_key, $http_parts[0], $params);
		return;
	} else {
		error_log("JavaScript Proxy: Could not route to $rest using API key $api_key");
	}
} else {
	error_log("JavaScript Proxy: No PATH_INFO during JavaScript proxy");
}

header("HTTP/1.1 404 Not Found");
header("Connection: close");
?>