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

require_once( 'ringside/social/api/RingsideSocialApiRender.php' );
require_once( 'ringside/social/session/RingsideSocialSession.php' );
require_once( 'ringside/social/client/RingsideSocialClientInterface.php' );

class RingsideSocialClientLocal  implements RingsideSocialClientInterface {

   private $networkKey;
   private $networkSecret;
   private $networkSession;
   private $iFrame = null;
   private $redirect = null;
   private $error = null;
   private $raw = false;
   private $status = 200;
   
   public function __construct( $networkKey, $networkSecret, &$networkSessionKey = null) {
      $this->networkKey = $networkKey;
      $this->networkSecret = $networkSecret;
      $this->networkSession = new RingsideSocialSession( $networkSessionKey );
   }
   
   public function render( $flavor, $appId, $application, $path ) {

      if ( $_SERVER['REQUEST_METHOD'] =='POST' ) {
         $params = &$_POST;
      } else {
         $params = &$_GET;
      }

      $render = new RingsideSocialApiRender( $appId, $application, $flavor, $path, $params );
      $response = $render->execute( $this );
      
      // TODO: Why are we doing this instead of setting thee inside $render->execute()?
      $this->iFrame = isset( $response['iframe'] ) ? $response['iframe'] : null; 
      $this->redirect = isset( $response['redirect'] ) ? $response['redirect'] : null;
      $this->error = isset( $response['error'] ) ? $response['error'] : null;
      $this->raw = isset( $response['raw']) ? $response['raw'] : false;
      $this->status = $response['status'];
      
      if ( isset ( $response['content']) ) {
         return $response['content']; 
      } else {
         return '';
      }
   }
   
   public function authorize( $uid ) {
      
//      $render = new RingsideSocialApiAuthorize( $network, $uid );
//      $response = $render->execute();
//      
//      $networkSession = isset( $response['session'] ) ? $response['session'] : null;
         echo "$uid you have been authorized!";
        $this->networkSession->setUserId( $uid );
         
   }
   
   public function inSession() {
      if ( $this->networkSession->getUserId() == null || ($this->networkSession->isLoggedIn() === false ) ){
         return false;
      } else {
         return true;
      }
   }
   
   public function getError( ) {
      return $this->error;
   }
   
   public function getIFrame( ) {
     return $this->iFrame;
   }
   
   public function getRedirect( ) {
      return $this->redirect; 
   }

   public function getCurrentUser( ) {
        return $this->networkSession->getUserId( );
   }
     
   public function getCurrentNetwork( ) {
        return $this->networkSession->getNetwork( );
   }
   
   public function getNetworkSession() {
      return $this->networkSession;
   }
   
   public function getNetworkSessionKey() {
      return $this->networkSession->getSessionKey();
   }
   
   public function isRaw() {
   	return $this->raw;
   }
   
   public function clearSession() {
      if ( $this->networkSession != null ) {
         $this->networkSession->clearSession();
      }
   }
   

}

?>
