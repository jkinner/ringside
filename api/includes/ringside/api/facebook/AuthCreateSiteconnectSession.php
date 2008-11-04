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

//require_once( "ringside/api/OpenFBAPIException.php" );
//require_once( "ringside/api/AuthRest.php" );
//require_once( "ringside/api/bo/App.php");
require_once( 'ringside/social/session/RingsideSocialSession.php' );
require_once( 'ringside/social/RingsideSocialUtils.php' );

/**
 * Create Site Connect Session
 * @author Richard Friedman
 */
class AuthCreateSiteconnectSession extends Api_AuthRest  {

    const PARAM_NETWORK_KEY = 'network_key';
    const RESPONSE_AUTH_TOKEN = 'auth_token';
    const RESPONSE_SOCIAL_SESSION = 'social_session';
     
    private $m_apiKey;
    private $m_networkKey;
    private $m_infinite = false;
    private $user_network_key;
    private $uid;

    public function validateRequest( ) {

        $this->m_apiKey = $this->getContext()->getApiKey();

        // Optional; will always be passed in by a Ringside client
        $this->m_networkKey = $this->getContext()->getNetworkKey();

        $this->user_network_key = $this->getRequiredApiParam( 'user_network_key');
        $this->uid = $this->getRequiredApiParam( 'uid');

    }

    public function execute() {
        // TODO: This ONLY will work if API and Social tiers are co-located!
        $response = array();
        // Finish the API session, because we need to start a social session
        session_regenerate_id(true);
        $_SESSION = array();
        $network_session = new RingsideSocialSession();
        $rest = RingsideSocialUtils::getAdminClient();
        $session_key = $rest->auth_createAppSession($this->uid, RingsideSocialConfig::$apiKey, false);
        $network_session->addApiSessionKey(RingsideSocialConfig::$apiKey, $session_key);
        $network_session->setNetwork($this->user_network_key);
        //$network_session->addApiSessionKey($apiKey, $session_key);
        $network_session->setUserId($this->uid );
        // TODO: Do user identity mapping right now
        //$network_session->setPrincipalId($pid);
        //$network_session->setTrust($trust_key);
        //$network_session->setCallbackUrl($social_callback);
        $network_session->setLoggedIn(true);
        $response[self::RESPONSE_SOCIAL_SESSION]['session_id']=$network_session->getSessionKey();
        $response[self::RESPONSE_SOCIAL_SESSION]['initial_expiry']=$network_session->getExpiry();
        session_write_close();
        return $response;
    }
     
}

?>
