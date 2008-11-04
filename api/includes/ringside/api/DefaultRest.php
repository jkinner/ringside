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

require_once( "ringside/api/AbstractRest.php" );
require_once( "ringside/api/ServiceFactory.php" );

abstract class Api_DefaultRest extends Api_AbstractRest { 
   
   /**
    * Load the session from the session key.
    */
   function loadSession() {
      
      $session_key = $this->getContext()->getSessionKey();
      
      if ( $session_key == null ) {
//         error_log("Session not found for API request. Context follows:");
//         error_log(var_export($this->getContext(), true));
//          throw new OpenFBAPIException( "Incorrect signature, session not found. ", FB_ERROR_CODE_INCORRECT_SIGNATURE );
      } else {

          /*
           * Sesssion keys can be used by multiple process, some just strip out the session key we 
           * use and make that their session id.  Hence problems on single box (yikes).
           * Convert session key to session id.
           */
          $key = str_ireplace( "-ringside", "", $session_key ); 
          $key = str_ireplace( "-", "0", $key );         
    
          $this->startSession( $key );
      }
   }

   /**
    * Implement delegation of requests.
    * TODO break apart the 'deployed networks' application key and the 'Social Network' application key.
    */
   function delegateRequest() 
   { 
      // If we are setup to use the remote trust capabilities.
      if ( RingsideApiConfig::$use_facebook_trust )
      {
         $api_key = $this->getContext()->getApiKey();
         $call_id = $this->getContext()->getCallId();
         $sessionApiKey = $this->getSessionValue( self::SESSION_API_KEY );
            
         // TODO: Use trusted authN providers to establish session key; need better security delegation
         error_log("Attempting Facebook authN delegation");

         require_once( "ringside/api/facebook/AuthCreateToken.php" );
         require_once( "ringside/api/facebook/AuthApproveToken.php" );
         require_once( "ringside/api/facebook/AuthGetSession.php" );
         require_once( "facebook/facebook.php" );

//         error_log("Validating API key $api_key");
         /** Get the secret key for given API Key */
         /** TODO It is at this point we are tied to a SINGULAR API/SECRET key. */
         $secret_key = $this->validateApiKey( $api_key );

//            error_log( "API Key is ". $api_key ." and secret is " . $secret_key);
//            error_log("Session key provided by client is $session_key");

         if ( $secret_key )
         {
            try
            {
               // TODO: Configure trust relationships on per app basis?
               // Delegate session creation to Facebook; then verify session authenticity by retrieving uid
               $fb = new Facebook($api_key, $secret_key);
               // TODO: Need to map Ringside user to Facebook user
               $fb->set_user($user, $session_key);
               $fb->api_client->api_key = $api_key;
               $fb->api_client->session_key = $session_key;
               $fb->api_client->last_call_id = $call_id;

               // Get the remote logged in user.
               $logged_in_user = $fb->api_client->call_method("facebook.users.getLoggedInUser", array());
               if ( $logged_in_user ) 
               {
                  // Interesting point were we sync the Social Network session with the one on this server
                  $this->setSessionValue( self::SESSION_ID, $session_key );
                  $this->setSessionValue( self::SESSION_CALL_ID, 0 );
                  
                  // TODO In the original code logged_in_user was passed into constructor, but now it's set in session.
                  $this->setSessionValue( self::SESSION_USER_ID, $logged_in_user );
                  
                  // TODO in original code api_key was passed in, however since it's already in context, no need.
                  // TODO note everything is passed by reference, not sure if this is right.
                  // TODO this should become a BO call.
                  $forcedAuth = new AuthCreateAppSession();
                  $forcedAuth->_setContext( $this->getContext() );
                  $forcedAuth->_setSession( $this->getSession() );
                  $forcedAuth->_setServer( $this->getServer() );
                  $forcedAuth->validateRequest();
                  $forcedAuthResponse = $forcedAuth->execute();
//                  error_log(var_export($authGetSessionResponse, true));
                  if ( !$authGetSessionResponse['session_key'] ) 
                  {
                     throw new OpenFBAPIException("Unable to get Ringside session for Facebook delegation");
                  }
//                  error_log("Started Ringside session using key ".$authGetSessionResponse['session_key']);
                  error_log("Authenticated via Facebook trust to Facebook uid $logged_in_user");

                  // TODO: Map to Ringside PID here!
//                     $this->validateCallId ( $call_id );
//                     error_log("Validated Facebook-authenticated call_id");
                     return;
               } 
               else 
               {
                  error_log("Failed to authenticate via Facebook trust");
               }
            } 
            catch ( FacebookRestClientException $e ) 
            {
               error_log("Facebook trust authentication failed: ".$e->getMessage());
               error_log($e->getTraceAsString());
            }
         } 
         else 
         {
            error_log("Application with API key " + $api_key + " is not registered for Facebook trust");
         }
      }
   }

   /**
    * Expire a session if required.
    */
   function validateSession()
   {
       if ( $this->getContext()->getSessionKey() != null ) {
          $api_key = $this->getContext()->getApiKey();
          $sessionApiKey = $this->getSessionValue( self::SESSION_API_KEY );
           
          if ( empty( $sessionApiKey ) || $sessionApiKey != $api_key ) 
          {
             $bad_api_key = empty( $sessionApiKey ) ? 'not set' : $sessionApiKey ;
             session_unset();
             throw new OpenFBAPIException( "Api keys do not match, token deleted ($api_key != $bad_api_key)", FB_ERROR_CODE_API_KEY_NOT_ASSOCIATED_WITH_APP );
          }
                  
          $expires = $this->getSessionValue('expires');
          if ( empty($expires) || ( $expires != 'never' && $expires < time() ) )
          {
             session_unset();
             throw new OpenFBAPIException( "Token has expired.", FB_ERROR_CODE_UNKNOWN_ERROR );
          }
       }
   }
    
   /**
    * Validate the API Key ([TODO] and load basic APP data?).
    */
   function validateApiKey( ) {
      $api_key = $this->getContext()->getApiKey();
      
      if ( !isset($api_key) || empty($api_key) ) {
         throw new OpenFBAPIException( 'Incorrect Signature, Missing API Key.', FB_ERROR_CODE_INCORRECT_SIGNATURE );
      }
      
      // Validate API KEY and load APP DATA.
      // Should be cache type data, huh?
//      error_log("Loading $api_key application on network ".$this->getNetworkId());

      $appService = Api_ServiceFactory::create('AppService');      
      $keyService = Api_ServiceFactory::create('KeyService');
      $ksids = $keyService->getIds($api_key);
      
      if (($ksids === false) || (count($ksids) == 0)) {
         throw new OpenFBAPIException( 'The api key submitted is not associated with any known application.', FB_ERROR_CODE_API_KEY_NOT_ASSOCIATED_WITH_APP );
      } else {
      	 $appId = $ksids['entity_id'];
      	 $domainId = $ksids['domain_id'];
      	 $kset = $keyService->getKeyset($appId, $domainId);
      	 if (($kset === false) || ($kset == NULL)) {
      	 	throw new OpeFBAPIException("No keyset found for api_key=$api_key, appId=$appId, domainId=$domainId");
      	 }
      	 $secret_key = $kset['secret'];    
         $this->getContext()->setPrivateKey( $secret_key );
         return $secret_key;
      }
   }

   /**
    * Validate the SIGNATURE by build MD5 checksum.
    */
   function validateSig() {
      $sig = $this->getContext()->getSig();
      $request = $this->getContext()->getInitialRequest();
      $secret = $this->getContext()->getPrivateKey();
      
      if ( !isset($sig) || empty($sig) ) {
         throw new OpenFBAPIException( 'Incorrect Signature, Missing SIG.', FB_ERROR_CODE_INCORRECT_SIGNATURE );
      }

      ksort($request);
       
      $str='';
      foreach ($request as $k=>$v) {
         if ( $k != 'sig' ){
            $str .= "$k=$v";            
         }
      }
      $str .= $secret;
      $md5sig = md5($str);
      
      if ( $md5sig != $sig ) {
//         error_log("str='$str', md5sig='$md5sig', sig='$sig'");
         throw new OpenFBAPIException( 'Incorrect Signature, SIG does not match', FB_ERROR_CODE_INCORRECT_SIGNATURE );
      }
   }
   
   /**
    * Validate the version matches.
    */
   function validateVersion(  ) {
      $version = $this->getContext()->getVersion();
      
      if ( !isset($version) || $version != '1.0') {
         throw new OpenFBAPIException( 'Incorrect Signature, Wrong Version number.', FB_ERROR_CODE_INCORRECT_SIGNATURE );
      }
   }

   /**
    * Validate the call_is being incremented on each and every call.
    */
   function validateCallId( ) {
       if ( $this->getContext()->getSessionKey() != null ) {
           $call_id = $this->getContext()->getCallId();
          $sessionCallId = $this->getSessionValue( self::SESSION_CALL_ID );
          
          if ( empty($call_id) ) {
             throw new OpenFBAPIException( 'Incorrect Signature, Call Identifier', FB_ERROR_CODE_INCORRECT_SIGNATURE );
          }
    
          if ( $call_id <= $sessionCallId ) {
             error_log( 'Warning: The submitted call_id ('.$call_id.') was not greater than the previous call_id ('.$sessionCallId.') for this session.');
          } else {
             $this->setSessionValue( self::SESSION_CALL_ID , max( $call_id, $sessionCallId ) );
          }
       }
   }   
}