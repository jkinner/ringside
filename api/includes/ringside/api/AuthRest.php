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

require_once ( 'ringside/api/DefaultRest.php' );

abstract class Api_AuthRest extends Api_DefaultRest { 

   private $newSession = false;
   
   /**
    * Load the session from the session key.
    */
   function loadSession() 
   {

      $auth_key = $this->getContext()->getAuthToken();
      $api_key = $this->getContext()->getApiKey();
      
      if ( $auth_key == null ) 
      {
         // A new session for authentication.
         $this->startSession();
         $this->setSessionValue( self::SESSION_ID, session_id() );
         $this->setSessionValue( self::SESSION_API_KEY, $api_key );
         $this->newSession = true;
      } 
      else if ( empty($auth_key) )
      {
         // If the AUTH_KEY is passed in but empty, bad state.
         throw new OpenFBAPIException( "Incorrect signature, authorization token not found. ", FB_ERROR_CODE_INCORRECT_SIGNATURE );
      } 
      else 
      {
         // Existing AUTH SESSION driven from AUTH_KEY
         $this->startSession( $auth_key );
      }      
      
   }

   /**
    * Override default behavior.
    * Currently for AUTH delegation not supported.
    */
   function delegateRequest() {
      
   }

   /**
    * Validate session setup correctly or is valid.
    */
   function validateSession() 
   {
      
      $session = $this->getSession();
      if ( empty ( $session  ) )
      {
         session_unset();
         throw new OpenFBAPIException( "Auth token ($auth_key) is not a known auth token." );
      }
       
      $api_key = $this->getContext()->getApiKey();
      $sessionApiKey = $this->getSessionValue( self::SESSION_API_KEY );
      if ( empty( $sessionApiKey ) || $sessionApiKey != $api_key )
      {
         $bad_api_key = empty($sessionApiKey) ? 'not set' : $sessionApiKey;
         session_unset();
         throw new OpenFBAPIException( "Api key's do not match, token deleted $api_key != $bad_api_key", FB_ERROR_CODE_API_KEY_NOT_ASSOCIATED_WITH_APP );

      }
       
      if ( $this->newSession === false )
      {
         $expires = $this->getSessionValue( self::SESSION_EXPIRES );
         if ( empty( $expires ) || $expires < time() )
         {
            session_unset();
            throw new OpenFBAPIException( "Token has expired.", FB_ERROR_CODE_UNKNOWN_ERROR );

         }
      }
   }
   
   /**
    * Auth methods do not use callId validation.
    */
   function validateCallId() { 
      
   }
   
}