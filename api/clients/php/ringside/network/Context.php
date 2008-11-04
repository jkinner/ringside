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

require_once('ringside/network/Configuration.php');
/**
 * The context passed from a social network, enabling the application to access the features
 * of one or more social network servers. 
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
class NetworkContext
{
	private $default_channel;
	
	private $deployed_network_config;
	private $host_network_config;
	
	public function __construct($deployed_api_key, $deployed_secret)
	{
		/*
		 * Read the basic context configuration; defer configuring the application's API key
		 * and secret until Channel configuration
		 */
		$source = null;
		if ( isset($_POST[$name]) )
		{
			$source = &$_POST;
		}
		else if ( isset($_GET[$name]) )
		{
			$source = &$_GET;
		}
		
		$context_params = self::readContext($source);
		$signer = new NetworkFacebookSigner();
		if ( isset($source['fb_sig']) && $signer->sign($context_params, $deployed_secret) == $source['fb_sig'] ) {
			
		}
	}
	
	public static function readContext($source, $name = 'fb_sig')
	{
		$context_config = array();
		
		if ( self::validateSignature($source, $name) )
		{
			$name_prefix = $name.'_';
			$name_prefix_length = strlen($name_prefix);
			foreach ( $source as $key => $value )
			{
				if ( 0 === strpos($key, $name_prefix) )
				{
					 $context_key = substr($key, $name_prefix_length + 1);
					 $context_config[$context_key] = $value;
				}
			}
		}
		
		return $context_config;
	}
}
?>