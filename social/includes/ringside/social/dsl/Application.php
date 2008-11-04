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
 * The type of rendering pages. 
 */
define( "OPENFBML_RENDER_WIDE" , 0  );
define( "OPENFBML_RENDER_PROFILE", 1 );
define( "OPENFBML_RENDER_PAGE", 2 );

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
interface Application {

   public function getClient();
   public function getSocialSession();
//   public function setApplicationId( $aid );
   public function getApplicationId( );
   public function getCurrentUser();
   public function getProfile();
   public function getPage();    
   public function getEvent();
   public function getProfileLink($uid, $text);   
}

?>
