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
require_once( 'ringside/social/api/RingsideSocialApiBase.php' );
require_once( 'ringside/social/RingsideSocialUtils.php' );
require_once( 'ringside/social/config/RingsideSocialConfig.php' );
require_once( 'ringside/api/clients/RingsideApiClientsRest.php' );
require_once( 'ringside/social/session/RingsideSocialSession.php' );
require_once( 'ringside/social/db/RingsideSocialDbSession.php' );

class RingsideSocialApiTrust extends RingsideSocialApiBase
{
	public function __construct( &$params )
	{
		parent::__construct( $params );
	}

	public function getAppProperties()
	{
		$api_key = $this->getParam('api_key');
		$canvas_url = $this->getParam('canvas_url');

		$result = null;
		if($api_key)
		{
			$result = $this->getAppPropertiesByApiKey($api_key);
		}else if($canvas_url)
		{
			$result = $this->getAppPropertiesByCanvasUrl($canvas_url);
		}
		// login to ringside api container
			
		if($result)
		{
			return $result;
		}
		return null;
	}

	/**
	 * TODO: This only works for facebook style authentication.  For other types we need to use trust_auth_class
	 * look it up and then call it to authorize us.
	 *
	 * @param unknown_type $trust_key
	 * @return unknown
	 */
	public function getAuthUrl($trust_key)
	{
		// TODO: Mark, need to add facebook trust key, right now test is not working because of it.
		if(!isset($trust_key))
		{
			$trust_key = RingsideSocialUtils::DEFAULT_TRUST_KEY;
		}
		$info = RingsideSocialDbSession::getTrustAuthority($trust_key);
		// trust_key, trust_name, trust_auth_class, trust_auth_url
		if(isset($info))
		{
			$url = $info['trust_auth_url'];
			if(isset($url))
			{
				return $url;
			}
		}

		// If all else fails return null, this will probably result in localhost being used
		return null;
		//return "api.facebook.com/restserver.php";
	}
	/**
	 * TODO: Should cache this information and expire same time as session instead of looking it up each time
	 *
	 * @param unknown_type $api_key
	 * @return unknown
	 */
	public function getAppPropertiesByApiKey($api_key)
	{
		$apiClientSocial = $this->getRingsideRestClient(RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey, null, null, RingsideSocialConfig::$uid);

		if($apiClientSocial)
		{
			// Get the application properties
			$result = $apiClientSocial->admin_getAppProperties( "application_id,application_name,api_key,secret_key,callback_url" , null, null, $api_key );
			return $result;
		}
		return null;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $canvas_url
	 * @return unknown
	 */
	public function getAppPropertiesByCanvasUrl($canvas_url)
	{
		$apiClientSocial = $this->getRingsideRestClient(RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey, null, null, RingsideSocialConfig::$uid);

		if($apiClientSocial)
		{
			// Get the application properties
			$result = $apiClientSocial->admin_getAppProperties( "application_id,application_name,api_key,secret_key,callback_url" , null, $canvas_url, null );
			return $result;
		}
		return null;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $apiKey
	 * @param unknown_type $secretKey
	 * @param unknown_type $session
	 * @param unknown_type $url
	 * @param unknown_type $uid
	 * @return unknown
	 */
	public function getRingsideRestClient($apiKey, $secretKey, $session, $url, $uid)
	{
		$apiClientSocial = new RingsideApiClientsRest($apiKey, $secretKey, null, null);
		$authToken = $apiClientSocial->auth_createToken();
		$res = $apiClientSocial->auth_approveToken($uid);
		$result = $apiClientSocial->auth_getSession($authToken);

		if($res["result"] == '1')
		return $apiClientSocial;
		return null;
	}

	public function getNetworkSession($apiKey, $secretKey, $session, $trust_key)
	{
		$secret = '';
		if(!isset($secretKey) || strlen($secretKey) == 0)
		{
			$props = $this->getAppPropertiesByApiKey($apiKey);
			$secret = $props['secret_key'];
		}

		$url = $this->getAuthUrl($trust_key);

		$ringsideClient = new RingsideApiClientsRest($apiKey, $secret, $session, $url);
		// Make sure the user is logged in and get the UID

		$userid = $ringsideClient->users_getLoggedInUser();
		if(!isset($userid) || strlen($userid) == 0)
		{
			throw new Exception("User is not logged in, invalid session: $session or api key: $apiKey");
		}

		$network_session = new RingsideSocialSession();
		$network_session->setUserId($userid);
		$network_session->setLoggedIn(true);

		return $network_session;
	}

	/**
	 * Gets a social session for anon user
	 *
	 * @return unknown
	 */
	public function getAnonymousSession()
	{
		$uid = 2;
		$network_session = new RingsideSocialSession($session);
		$network_session->setUserId($uid);
		$network_session->setLoggedIn(true);

		return $network_session;
	}

	/** 
	int  fb_sig_user  The uid of the person who is uninstalling your application (e.g. 609143784)  
	string  fb_sig_session_key  The session_key originally given to your application for the user who is uninstalling the application. This parameter did not appear in the latest requests.  
	string  fb_sig_api_key  The api_key of your application that is being uninstalled.  
	int  fb_sig_added  n/a  
	string  fb_sig  
	 */
	private function getContext(FacebookRestClient $client, RingsideSocialSession $network_session)
	{
		$api_key = $client->api_key;
		$session_key = $client->session_key;
		$uid = $client->users_getLoggedInUser();
		$social_session_key = $network_session->getSessionKey();
		
		$names = $client->users_getInfo( $uid, "first_name" );
		$name = $names[0];
		$user_name = trim( $name['first_name'] );

		return "fb_sig_user=$uid&user_name=$user_name&fb_sig_api_key=$api_key&fb_sig_session_key=$session_key&social_session_key=$social_session_key";
	}

	/**
	 * Authorize the user against the api_key, app_id, or canvas_url
	 *
	 * This produces a SocialSession Object.
	 *
	 * Possible Params:
	 * network_key
	 * trust_key
	 * api_key
	 * canvas_url
	 * auth_token
	 * social_callback
	 */
	public function authorize()
	{
		$network_session = null;
		$network_key = $this->getParam('network_key');
		$auth_token = $this->getParam('auth_token');
		$social_callback = $this->getParam('social_callback');
		$api_key = $this->getParam('api_key');
		$canvas_url = $this->getParam('canvas_url');
		$user_name = $this->getParam('user_name');

		$trust_key = $this->getParam('trust_key');
		if(!isset($trust_key))
		{
			$trust_key = $socialApiKey;
		}

		$result = $this->getAppProperties();
			
		if($result)
		{
			$callback = isset( $result['callback_url'] )? $result['callback_url'] : '';
			$apiKey = isset( $result['api_key'] )? $result['api_key'] : '';
			$apiSecret = isset( $result['secret_key'] )? $result['secret_key'] : '';

			if(!isset($social_callback))
			{
				$social_callback = $callback;
			}
			try {
				if(isset($apiKey) && isset($apiSecret))
				{
					$auth_url = $this->getAuthUrl($trust_key);

					$fb = new RingsideApiClients($apiKey, $apiSecret, null, $auth_url);
					//public function __construct($api_key, $secret, $session_key = null, $url = null) {
					$result = $fb->do_get_session($auth_token);

					$session_key = $fb->api_client->session_key;
					$uid = $fb->api_client->users_getLoggedInUser();
						
					$pids = $fb->api_client->users_mapToSubject(array($uid), $network_key, $result['application_id']); 
//					RingsideSocialDbPrincipal::getPrincipalForSubject($uid, $network_key, $user_name, $trust_key);

					//if ( isset($pids) ) {
						// getPrincipalForSubject accepts and returns multiple IDs
						$pid = 0;
						if ( isset($pids) ) {
							$pid = $pids[0];
						}

						// bool setcookie ( string $name [, string $value [, int $expire [, string $path [, string $domain [, bool $secure [, bool $httponly ]]]]]] )
						$network_session = new RingsideSocialSession();
						$network_session->setNetwork($network_key);
						$network_session->addApiSessionKey($apiKey, $session_key);
						$network_session->setUserId($uid);
						$network_session->setPrincipalId($pid);
						$network_session->setTrust($trust_key);
						$network_session->setCallbackUrl($social_callback);
						$network_session->setLoggedIn(true);
						
						$context = $this->getContext($fb->api_client, $network_session);
						if(strrpos($social_callback,'?')==0){
							return $social_callback.'?'.$context;
						} else {
							return $social_callback.'&'.$context;
						}
					//} else {
					//	$this->error = "Unable to set Principle!";
					//}
				}
			} catch ( Exception $exception ) {
				error_log( "Exception : " . $exception->getMessage() ."\n". $exception->getTraceAsString());
				$this->error = "Exception : " . $exception->getMessage() ."\n". $exception->getTraceAsString();
			}
		}
		if(!isset($network_session))
		{
			error_log("Application with api_key: $api_key or canvas_url: $canvas_url not found!  Creating session and redirecting to $social_callback!");
			$network_session = new RingsideSocialSession(null);
			$network_session->setNetwork($network_key);
			$network_session->setTrust($trust_key);
			$network_session->setCallbackUrl($social_callback);
			if(strrpos($social_callback,'?')==0){
				return $social_callback."?social_session_key=".$network_session->getSessionKey();
			} else {
				return $social_callback."?social_session_key=".$network_session->getSessionKey();
			}
		}
	}
}
?>
