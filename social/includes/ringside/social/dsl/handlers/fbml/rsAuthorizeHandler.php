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

/**
 * The authorize handler is used to handle login situation.
 * It allows the system to be notified that there has been a valid or trusted authentication
 * and optionally can handle redirects.
 *
 * 1. The SOCIAL SESSION is approved as UID is required.
 * 2. The other mechanism is to handle redirects.
 * in this case apikey must be specified
 *
 * @author Richard Friedman
 * @tagName rs:authorize
 * @tagRequired int uid User id that has been authenticated
 * @tagOptional string apikey The API KEY of the application to redirect to.
 * @tagOptional boolean canvas true or false indicating if the redirect should go to the canvas page or the callback url
 * @tagOptional string next the string to be appended to the
 * @tagOptional boolean infinite true or false indicating if this is an infinite session
 * @return NOTHING or a redirect.
 */

class rsAuthorizeHandler {

   // Default expire login after one day
   const DEFAULT_EXPIRY_INTERVAL = 86400;

   private $redirect;

   function doStartTag(Application $application, &$parentHandler, $args ) {

      $uid = isset( $args['uid'] ) ? $args['uid'] : null;
      $infinite = isset( $args['infinite'] ) ? (strcasecmp($args['infinite'], 'true') === 0) : false;
      if ( empty( $uid ) ) {
         echo 'RUNTIME ERROR: rs:authorize Required attribute "uid" not found in node rs:authorize';
         return false;
      }

      // Trust this user to this application, if there is an auth token
      // need to approve it.  Else we need to get the auth token.
      $apikey = isset( $args['apikey'] ) ? $args['apikey'] : null;
      $authtoken = isset( $args['authtoken'] ) ? $args['authtoken'] : null;

      // if we get both apikey and authtoken
      if ( $authtoken !== null && $apikey !== null ) {

         $result = $this->handleDesktopTrust( $application, $uid, $infinite, $authtoken );
         if ( $result === true ) {
            return 'redirect';
         }

      } else if ( $apikey !== null ) {

         $trust = isset( $args['trust'] ) ? (strcasecmp($args['trust'], 'true') === 0) : false;
         $canvas = isset( $args['canvas'] ) ? (strcasecmp($args['canvas'], 'true') === 0) : false;
         $next = isset( $args['next'] ) ? $args['next'] : '';
          
         $result = $this->handleWebAppTrust( $apikey, $application, $uid, $infinite, $canvas, $trust, $next );
         if ( $result === true ) {
            return 'redirect';
         }
         
      } else {
         // Case: Just a normal web login.
         $this->trustUser( $application, $uid, $infinite );
      }

      return false;
   }

   function doBody( $application, &$parentHandler, $args, $body ) {
      return $this->redirect;
   }

   function doEndTag( $application, &$parentHandler, $args ) {
       
   }
    
   /**
    * handle authorization for desktop applications. 
    *
    * @param Application $application
    * @param int $uid
    * @param boolean $infinite
    * @param string $authtoken
    * @return boolean
    */
   function handleDesktopTrust( $application, $uid, $infinite, $authtoken ) {

      $this->trustUser( $application, $uid, $infinite );
      try {
         // Get a client which is represents this SOCIAL engine to API relationship
         $apiSessionKey = RingsideSocialUtils::getApiSessionKey( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey, $application->getSocialSession() );
         $apiClientSocial = new RingsideApiClientsRest( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey, $apiSessionKey );

         // Get information about a given application.
         $result = $apiClientSocial->admin_getAppProperties( "application_id,secret_key", null, null, $apikey  );
         $secret =  isset( $result['secret_key'] ) ? $result['secret_key'] : "";
          
         // get the clients applications and create
         $appClient = new RingsideApiClientsRest( $apikey, $secret );
         $token = $appClient->auth_createToken( $infinite );
         $appClient->auth_approveToken( $uid );
          
         //            $this->redirect = "desktopapp.php?api_key=$apikey&success";
         return true;
      } catch (Exception $e){
         $msg = urlencode( $e->getMessage() );
         $this->removeTrust( $application, $uid );
         //            $this->redirect = "desktopapp.php?api_key=$apikey&fail&error=$msg";
         //            return 'redirect';
         
         return false;
      }

   }

   /**
    * Handle the trust situation for a web application, this
    * will force redirects to canvas or callback url as specified. 
    *
    * @param Application $application
    * @param int $uid
    * @param boolean $infinite
    * @param boolean $canvas
    * @param string next
    * @return boolean success failure
    */
   function handleWebAppTrust( $apikey, $application, $uid, $infinite, $canvas, $trust, $next ) {

      $this->trustUser( $application, $uid, $infinite );

      try {

         // Get a client which is represents this SOCIAL engine to API relationship
         $apiSessionKey = RingsideSocialUtils::getApiSessionKey( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey, $application->getSocialSession() );
         $apiClientSocial = new RingsideApiClientsRest( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey, $apiSessionKey, null, RingsideSocialConfig::$apiKey );

         // Get information about a given application. 
         $result = $apiClientSocial->admin_getAppProperties( "application_id,secret_key,canvas_url,callback_url", null, null, $apikey  );
         $canvas_url = isset( $result['canvas_url'] ) ? $result['canvas_url'] : "";
         $callback_url = isset( $result['callback_url'] ) ? $result['callback_url'] : "";
         $secret =  isset( $result['secret_key'] ) ? $result['secret_key'] : "";

         if ( $canvas === true ) {
            $trust_info = $apiClientSocial->admin_getTrustInfo( array(RingsideSocialConfig::$apiKey) );
            $canvas_root = $trust_info[0]['trust_canvas_url'];
            $this->redirect =  $canvas_root . '/' .$canvas_url . $next;
         } else {
            
            // get the clients applications and create 
            $appClient = new RingsideApiClientsRest( $apikey, $secret, null, null, RingsideSocialConfig::$apiKey );
            $token = $appClient->auth_createToken( $infinite );
            $appClient->auth_approveToken( $uid );
            
            if ( strpos($next, "?" ) === false ) {
               $next = $next . "?";
            } else if ( strpos( $next, "&") !== false ) {
               $next = $next . "&";
            }
             
            if ( $trust === true ) { 
               $redir  = $next  . "auth_token=" . $token ;
            } else if ( strpos( $next, $callback_url ) === 0 ) {
               $redir  = $next  . "auth_token=" . $token ;
            } else {
               $redir  = $callback_url . $next  . "auth_token=" . $token ;
            }
            $this->redirect = $redir;
         }
         
         return true;

      } catch ( Exception $e ) {
         error_log( $e->getMessage() );
         $this->removeTrust( $application, $uid );
         
         return false;
      }
   }
    
   /**
    * Trust a user within the social layer.  Once trusted the user
    * and application sessions can flow.
    *
    * @param Application $application
    * @param int $uid
    * @param boolean $infinite
    */
   function trustUser( $application, $uid , $infinite ) {
      // TODO this should not be in the handler.
      // we need to tell SOCIAL to trust this user.
      $socialSession = $application->getSocialSession();
      if ( $socialSession != null ) {
         $socialSession->setUserId( $uid );
         $socialSession->setLoggedIn( true );
         // TODO: This is really a Web-level decision; after the user is logged in, this MUST NOT change
         $socialSession->setNetwork( RingsideSocialConfig::$apiKey );
         // Expire in one day, unless infinite
         $expiry = time() + self::DEFAULT_EXPIRY_INTERVAL;
         if ( $infinite ) {
            $expiry = null;
         }
         $socialSession->setExpiry($expiry);
      }

   }
    
   /**
    * A clean quick up if you gave trust but need to remove it quickly.
    *
    * @param Application $application
    * @param int  $uid
    */
   function removeTrust( $application, $uid ) {
      $socialSession = $application->getSocialSession();
      if ( $socialSession != null ) {
         $socialSession->setUserId( null );
         $socialSession->setLoggedIn( false );
      }

   }
   
   function isEmpty()
   {
   		return true;
   }
   
	function getType()
   	{
   		return 'inline';   	
   	}
}
?>
