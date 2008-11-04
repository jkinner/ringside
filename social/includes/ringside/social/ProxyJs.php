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
 * Proxies FBJS-style requests from the browser back to the application's Ajax endpoint.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
require_once('ringside/social/RingsideSocialAppContext.php');
require_once('ringside/social/RingsideSocialUtils.php');
require_once('ringside/social/session/RingsideSocialSession.php');
require_once('ringside/social/config/RingsideSocialConfig.php');
require_once('ringside/web/config/RingsideWebConfig.php');

class SocialProxyJs {
	public function execute($api_key, $callback_url, $params) {
		$admin_client = RingsideSocialUtils::getAdminClient();
		// TODO: SECURITY: Possibly security hole. We're signing and giving the signed payload to any URL, just by using the API key, which is public. A 3rd-party could hijack the signed payload and implement an offline brute force attack on the secret key
		$app_props = $admin_client->admin_getAppProperties( "application_id,application_name,api_key,secret_key,callback_url" , null, null, $api_key );

		// From RingsideSocialServerRender:
		// Recreate Session if we have it
		if(array_key_exists('social_session_key', $params))
		{
			$session_key = $params['social_session_key'];
			$network_session = new RingsideSocialSession($session_key);
			$uid = $network_session->getUserId();

			if(null == $uid || strlen($uid) == 0)
			{
				setcookie('social_session_key', $network_session->getSessionKey());
				$uid = $_REQUEST['uid'];
				$network_session->setUserId($uid);
				$network_session->setLoggedIn(true);
			}
		} else if ( isset($_COOKIE['PHPSESSID']) ) {
			// Optimization if user is already logged into web front-end
			$network_session = new RingsideSocialSession($_COOKIE['PHPSESSID']);
			$uid = $network_session->getUserId();
		} else {
			// Not logged in, so login via annonymous user
			$trust = new RingsideSocialApiTrust($request);
			$network_session = $trust->getAnonymousSession();
		}
		
		$ctx = self::buildCallContext($api_key, $network_session);
		$sig_params = $ctx->getParameters($app_props['secret_key']);
		$req_params = array_merge($params, $sig_params);
//		error_log("Ajax Proxy to $callback_url with params:".var_export($req_params, true));
		$result = RingsideSocialUtils::get_request($callback_url, $req_params, $headers );
		
		echo str_replace('+', '&#43;', $result);
	}
	
	/**
	 * Builds a calling context to invoke the application's AJAX endpoint.
	 *
	 * @param array $request the request array
	 * @param RingsideSocialSession $session the social session
	 * @return RingsideSocialAppContext the context
	 */
	private static function buildCallContext( $api_key, RingsideSocialSession $session ) {
		$ctx = new RingsideSocialAppContext();
		$ctx->setApiKey($api_key);
		$ctx->setIsAjax(1);
		$ctx->setFlavor('ajax');
		// TODO: $ctx->setNetworkId();
		$ctx->setSessionKey($session->getApiSessionKey($api_key));
		$ctx->setExpires($session->getExpiry()==null?0:$session->getExpiry());
		$ctx->setNetworkId(RingsideSocialConfig::$apiKey);

		if ( $session->isLoggedIn() ) {
			// We only know these if the user is logged in
			$ctx->setUser($session->getUserId());
			// TODO: Is App Added?
			$ctx->setIsAppAdded(1);
		}
		
		$ctx->setTime(microtime(true));
		return $ctx;
	}
}
?>