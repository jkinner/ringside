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

require_once 'ringside/social/dsl/RingsideSocialDslFlavorContext.php';
require_once 'ringside/social/session/RingsideSocialSession.php';
require_once 'ringside/social/dsl/Application.php';
/**
 * The applications represents the interaction between parser/renderer/system.
 * It allows the renderer to ask questions about 
 * * the currently logged in user. 
 * * which client library to use
 * * what is currently being rendered (profile, page, ... )
 * 
 * The ugliness in all this is the client, as this assumes that all of the required APIs are there. 
 * A mock client library is packaged with OpenFBML for testing purpose, this becomes more relevant
 * when plugged into OpenFB. 
 */
class MockApplication  {

	public $flavor_context = null;
   public $client = null;
   public $uid = null;
   public $rendering = null;
   public $applicationId = null;
   
   public function __construct() {
   	$this->flavor_context = new RingsideSocialDslFlavorContext();
   }
   
   public function getClient() {
      return $this->client;
   }
      
   public function getApplicationId() {
      return $this->applicationId;
   }
   
   public function getCurrentUser() {
      return $this->uid;
   }
   
   public function whatAreWeRendering() {
      return $this->rendering;
   }
   
   public function getSocialSession() {
      
   }
   
   public function setApplicationId( $aid ) {
      
   }
   
   public function getProfile() {}
   public function getPage() {}
   public function getEvent() {}
   public function getProfileLink($uid, $text) {}
   public function getFlavorContext() {
   	return $this->flavor_context;     
   }
}

?>
