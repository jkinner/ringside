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
 * Provides the social context for an application. In a Ringside-enabled application,
 * the social context includes:
 * <br>
 * <ul>
 *  <li>A connection to the Host network</li>
 *  <li>A connection to the Deployed network</li>
 *  <li>Optionally, connections to other networks</li>
 *  <li>Pre-determined social properties for the user</li>
 * </ul>
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */

require_once('ringside/application/ParameterReader.php');
require_once('ringside/network/Channel.php');
require_once('ringside/network/FacebookSigner.php');
require_once('ringside/network/FacebookChannel.php');

class ApplicationContext {
	private $networks = array();
	private $deployed_network_id;
	private $host_network_id;
	private $signer;
	private $timeout;
	private $context_params;
	private $network_params;
	
	public function __construct($deployed_api_key, $deployed_secret)
	{
		$timeout = 48*3600;
		
		/*
		 * Read the basic context configuration; defer configuring the application's API key
		 * and secret until Channel configuration
		 */
		$source = array();
		if ( isset($_POST['fb_sig']) ) {
			$source = $_POST;
		} else if ( isset($_GET['fb_sig']) ) {
			$source = $_GET;
		} else if ( ! empty($_COOKIE) ) {
			// Check the API-specific cookies
			$cookie_reader = new ContextParameterReader($_COOKIE, $deployed_api_key);
			$source = $cookie_reader->getContext();
		}
		
		$reader = new ContextParameterReader($source, 'fb_sig');
		
		// To be compatible with Facebook, we sign based on all fb_sig parameters BEFORE any other sub-context claims them
		$signingContext = $reader->getContext();
		// Then, we read the context for each network
		$this->network_params = $this->readNetworkParameters($reader, $source);
		// Then, we keep the remaining parameters handy (these have already been folded into the host network, too)
		$this->context_params = $mainContext = $reader->getContext();
		
		// If there are NO context parameters, then this must be an authentication invocation, meaning we should
		// go get a session.
		if ( sizeof($mainContext) > 0 )
		{
			// Load the configuration(s) for the deployed and host networks; this is configuration provided by the Ringside server
			$this->host_network_id = isset($mainContext['host'])?$mainContext['host']:null;
			$this->deployed_network_id = isset($mainContext['deployed'])?$mainContext['deployed']:null;
			
			// Verify the signature, if it exists
			$this->signer = new NetworkFacebookSigner();
			
			// Always try to initialize these two channels
			$deployed_channel = isset($this->deployed_network_id)?($this->networks[$this->deployed_network_id] = NetworkChannel::create(NetworkFacebookChannel::CHANNEL_NAME, $this->network_params[$this->deployed_network_id])):null;
			$host_channel = isset($this->host_network_id)?($this->networks[$this->host_network_id] = NetworkChannel::create(NetworkFacebookChannel::CHANNEL_NAME, $this->network_params[$this->host_network_id])):null;
			
			if ( isset($source['fb_sig']) && self::verifySignature($this->signer, $signingContext, $deployed_secret, $source['fb_sig']) ) {
				if ( isset($this->context_params['time']) && 0 !== $timeout && time() - $this->context_params['time'] > $timeout ) {
					// There is a timeout, and it has passed; so, the session has expired
					return;
				}
	
				// The request is valid; now set all the configuration and session information for the 2 main channels
				self::initializeChannelSession($deployed_channel, $this->network_params[$this->deployed_network_id]);
				self::initializeChannelSession($host_channel, $this->network_params[$this->host_network_id]);
			} else {
				// TODO: Security audit
				error_log('Invalid signature when processing request from '.$_SERVER['REMOTE_ADDR'].' ('.$_SERVER['REMOTE_HOST'].')');
			}
		} else if ( isset($source['auth_token']) ) {
			// This is an authentication request. The application needs to establish the session via API calls.
			
		} else {
			// TODO: Operations alert
			error_log("Problem loading application context");
			error_log("Passed parameters are:");
			error_log(var_export($_REQUEST, true));
		}
	}

	protected static function initializeChannelSession(NetworkChannel $channel, $params, $backup_params = null)
	{
		$user			= 	isset($params['user'])		?	$params['user']		:
							( $backup_params !== null && isset($backup_params['user'])		?	$backup_params['user']		:	null );
		$session		= 	isset($params['session'])	?	$params['session']	:
							( $backup_params !== null && isset($backup_params['session'])	?	$backup_params['session']		:	null );
		$expires		= 	isset($params['expires'])	?	$params['expires']	:
							( $backup_params !== null && isset($backup_params['expires'])	?	$backup_params['expires']		:	null );
		$channel->setUser($user, $session, $expires);
	}
	
	/**
	 * Reads configurations of multiple networks from $_POST or $_GET ($_POST preferred). These
	 * parameters are used to configure channels for each network. The resulting array contains
	 * the keys 'host' and 'deployed' for the host and deployed networks, respectively. It also may
	 * contain additional network configurations by ID. 
	 * <br />
	 * The main context (fb_sig) will have the following parameters:<br />
	 * <ul>
	 *   <li>in_canvas</li>
	 *   <li>request_method</li>
	 *   <li>locale</li>
	 *   <li>position_fix</li>
	 *   <li>time</li>
	 *   <li>added</li>
	 *   <li>profile_update_time</li>
	 *   <li>friends</li>
	 *   <li>is_ajax</li>
	 *   <li>flavor</li>
	 *   <li>host</li>
	 *   <li>deployed</li>
	 *   <li>networks</li>
	 * </ul>
	 * 
	 * Every context (including the main context) will have the following parameters:<br />
	 * <ul>
	 *   <li>user</li>
	 *   <li>session_key</li>
	 *   <li>expires</li>
	 *   <li>secret (optional)</li>
	 *   <li>api_key</li>
	 *   <li>rest_url</li>
	 *   <li>login_url</li>
	 *   <li>canvas_url</li>
	 * </ul>
	 * 
	 * @return array having the keys 'host', 'deployed', and one for each network key
	 * 				  configured for this application.
	 */
	protected function readNetworkParameters(ContextParameterReader $reader, array $source) {
		$allNetworkParameters = array();
		$mainContextName = $reader->getMainContextName(); 
		
		$networks = array();
		
		error_log("Processing source: ".var_export($source, true));
		if ( isset($source[$mainContextName.'_networks']) ) {
			$networks = explode(',', $source[$mainContextName.'_networks']);

			error_log("Reading network parameters for ".sizeof($networks)." networks");
			foreach ( $networks as $network ) {
				$networkParameters = $reader->getContext($mainContextName.'_'.$network);
				$allNetworkParameters[$network] = $networkParameters;
			}
		}
		
		// Now read the main context, putting those parameters into the host network context
		$remainderContext = $reader->getContext();
		if ( isset($remainderContext['host']) ) {
			$hostNetwork = $remainderContext['host'];
			// Make sure host network ID is actually configured
			if ( isset($allNetworkParameters[$hostNetwork]) ) {
				// Merge the main context into the host network, but prefer explicit parameters over "default" values
				$allNetworkParameters[$hostNetwork] = array_merge($remainderContext, $allNetworkParameters[$hostNetwork]);
			} else {
				$allNetworkParameters[$hostNetwork] = $remainderContext;
			}
		}
		
		return $allNetworkParameters;
	}
	
	public static function verifySignature(NetworkSigner $signer, $params, $secret, $signature)
	{
		$signer->sign($params, $secret) == $signature;
	}
	
	public function getHostChannel()
	{
		return $this->networks[$this->host_network_id];
	}
	
	public function getDeployedChannel()
	{
		return $this->networks[$this->deployed_network_id];
	}
	
	public function getChannel($nid)
	{
		$network = null;
		if ( ! isset($this->networks[$nid]) )
		{
			$network_info = array();
			if ( isset($this->network_params[$nid]) ) {
				// We already have the configuration
				$network_info = $this->network_params;
			} else {
				// We do NOT already have the network config; go retrieve it
				$deployed_network = $this->getDeployedNetwork();
				$client = new NetworkRestClient($deployed_network);
				$network_info = $client->admin_getNetwork($nid);
			}

			if ( isset($network_info[$nid]) )
			{
				$this->networks[$nid] = $network = new NetworkChannel($nid, $network_info['restserver_url'], $network_info['login_url'], $network_info['canvas_url']);
			}
		}
		else
		{
			$network = $this->networks[$nid];
		}
		
		return $network;
	}
	
	public function isInCanvas()
	{
		return isset($this->context_params['in_canvas'])?0!=$this->context_params['in_canvas']:false;
	}
	
	public function isInIframe()
	{
		return isset($this->context_params['in_iframe'])?0!=$this->context_params['in_iframe']:false;
	}
	
	public function isAjax()
	{
		return isset($this->context_params['is_ajax'])?0!=$this->context_params['is_ajax']:false;
	}
}
?>