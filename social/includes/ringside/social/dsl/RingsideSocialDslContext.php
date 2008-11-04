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

require_once( 'ringside/social/dsl/RingsideSocialDslFlavorContext.php' );
require_once( 'ringside/social/dsl/Application.php');

/**
 * The handlers need to ask question or do things against both the 
 * social engine which we are running in  or against the API
 * container.   Hence to give a context to the parser/handler RingsideSocialApplication
 * is created. 
 * 
 */
class RingsideSocialDslContext implements Application
{
   private $apiClient = null;
   private $network_session = null;
   private $flavor_context = null;
   private $appId = null;
   
   public function __construct( $apiClient, RingsideSocialSession $network_session, RingsideSocialDslFlavorContext $flavor_context, $appId = null  ) {
   	  $this->network_session = $network_session;
      $this->apiClient = $apiClient;
      $this->appId = $appId;
      $this->flavor_context = $flavor_context;
      if ( ! empty($flavor_context) && $apiClient->getFlavor() ) {
      	$this->flavor_context->startFlavor($apiClient->getFlavor());
      }
   }
   
   public function getClient() {
      return $this->apiClient;
   }
   
   public function getSocialSession() { 
      return $this->network_session;
   }

   public function getApplicationId( ) {
      if ( $this->appId != null ) { 
         return $this->appId;
      } 
      else {
      	 return $this->apiClient->api_key; 
      }
   }
   
   public function getCurrentUser() {
      return $this->network_session->getUserId(); 
   }
    
   public function getProfile() {
      
   }
    
   public function getPage() {

   }
    
   public function getEvent() {

   }
    
   public function getProfileLink($uid, $text) {
   	// TODO: This should use $this->apiClient to construct the correct URL
      $uid = $this->getCurrentUser();
      return "<a href=\"profile.php?uid=$uid\">$text</a>";
   }
   
   public function getFlavorContext() {
   	return $this->flavor_context;
   }
}
?>
