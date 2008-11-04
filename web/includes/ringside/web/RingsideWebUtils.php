<?php
/*******************************************************************************
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
 ******************************************************************************/
class RingsideWebUtils {
	/**
	 * Redirects the browser to the specified absolute or relative URL, then TERMINATES THE SCRIPT.
	 *
	 * @param string $url the absolute or relative URL for the redirection.
	 */
	public static function redirect($url) {
//		error_log("Redirecting to (possibly relative) url $url");
		$baseurl = 'http://'.$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']&&$_SERVER['SERVER_PORT']!=80?':'.$_SERVER['SERVER_PORT']:'').$_SERVER['REQUEST_URI'].($_SERVER['QUERY_STRING']?'?'.$_SERVER['QUERY_STRING']:'');
		$redirUrl = RingsideWebUtils::parseRelative($baseurl, $url);
//		error_log("Redirecting to ".$redirUrl );
		header( "Cache-control: private");
		header( "Location: " . $redirUrl );
		exit();
	}

	/**
	 * Help incoming relative urls get redirected to the correct location.
	 *
	 * @param string $absolute
	 * @param string $relative
	 * @return the absolute url.
	 */
	public static function parseRelative($absolute, $relative) {
//		error_log("Parsing (possibly relative) url $relative against base URL $absolute");
		
		// If there is a scheme set as part of this operation then 
		// we already have an absolute url
		$p = parse_url($relative);
		if( isset( $p["scheme"] ) && $p["scheme"] ) { 
		   return $relative;
		}

		// The variables to come out of this include
		// scheme
		// host
		// path
		extract(parse_url($absolute));

		// if path does not end with /
		if ( $path{strlen($path)-1} != '/' ) {
			$path = dirname($path);
			$path = str_replace( '\\', "/", $path );
		}

		// if relative starts with / 
		if( $relative{0} == '/' ) {
			$cparts = array_filter(explode("/", $relative));
		} else {
			$aparts = array_filter(explode("/", $path));
			$rparts = array_filter(explode("/", $relative));
			$cparts = array_merge($aparts, $rparts);
			foreach($cparts as $i => $part) {
				if($part == '.') {
					$cparts[$i] = null;
				}
				if($part == '..') {
					$cparts[$i - 1] = null;
					$cparts[$i] = null;
				}
			}
			$cparts = array_filter($cparts);
		}
		$path = implode("/", $cparts);
		
		$url = "";
		
		// since scheme is set from absolute, it should be set. 
		if($scheme) {
			$url = "$scheme://";
		}

		// pass thru u/p if there. 
		if( isset($user) && $user ) {
			$url .= "$user";
			if($pass) {
				$url .= ":$pass";
			}
			$url .= "@";
		}
		
		// host port
		if($host) {
			$url .= "$host";
			if ( isset( $port)&& $port) {
				$url .= ":$port";
			}
			$url .= '/';
		}
		
		// attach path
		$url .= $path;
		
		// Maintain trailing slashes; they could be important.
		if ( $relative[strlen($relative)-1] == '/' && $url[strlen($url)-1] != '/' ) {
			$url .= '/';
		} 
		
//		error_log("Resolved url is [$url]");
		return $url;
	}

}
?>
