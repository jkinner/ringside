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

require_once( "ringside/api/OpenFBAPIException.php" );
require_once( "ringside/api/AuthRest.php" );
require_once( "ringside/api/bo/App.php");
require_once( "ringside/api/ServiceFactory.php");

/**
 * Auht.createToken API
 * @author Richard Friedman
 */
class AuthCreateToken extends Api_AuthRest  {

   const PARAM_NETWORK_KEY = 'network_key';
   const PARAM_INFINITE = 'infinite';
   
   const RESPONSE_AUTH_TOKEN = 'auth_token';
   
   private $m_apiKey;
   private $m_networkKey;
   private $m_infinite = false;

   /**
    * Get the information from the http request needed to execute Auth.createToken
    */
   public function validateRequest( ) {

      $this->m_apiKey = $this->getContext()->getApiKey();

      // Optional; will always be passed in by a Ringside client
      $this->m_networkKey = $this->getContext()->getNetworkKey();

      $infinite = $this->getApiParam( self::PARAM_INFINITE, '');
      $this->m_infinite = (strcasecmp( $infinite , 'true') ===0 )?true:false;

   }

   /**
    * Execute the auth.createToken method
    */
   public function execute() {
      $response = array();

      //		$response['auth_token'] = $auth_token = session_id();
      // TODO what is this change going to bust?
      // FIXME: This is an odd statment and $auth_token is never used!  Should it be removed?
      $response[self::RESPONSE_AUTH_TOKEN] = $auth_token = $this->getSessionValue(self::SESSION_ID);

      $this->setSessionValue( self::SESSION_API_KEY, $this->m_apiKey );
      if ( $this->m_infinite ) {
//         error_log("auth_token $auth_token will be infinite for ".$this->m_apiKey);
         $this->setSessionValue(self::SESSION_INFINITE, 'true');
      }
      $this->setSessionValue( self::SESSION_EXPIRES, time() + 5 * 60 );
      $this->setSessionValue( self::SESSION_TYPE, self::SESSION_TYPE_VALUE_AUTH );
      if ( $this->getNetworkId() !== null ) {
         $this->setSessionValue( self::SESSION_NETWORK_ID, $this->getNetworkId());
      }

      $appService = Api_ServiceFactory::create('AppService');
      $aid = $appService->getNativeIdByApiKey($this->m_apiKey);
      if (($aid == NULL) || ($aid === false)) {
          error_log('Attempt to create auth token from unknown app: '.$this->m_apiKey);
      	throw new OpenFBAPIException('Invalid api key', FB_ERROR_CODE_API_KEY_NOT_ASSOCIATED_WITH_APP);
      }
      $this->setSessionValue( self::SESSION_APP_ID , $aid );

      return $response;
   }
}

?>
