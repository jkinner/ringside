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

include_once 'include.php';

require_once( 'ringside/web/RingsideWebUtils.php' );
require_once( 'ringside/web/config/RingsideWebConfig.php' );
require_once( 'ringside/web/session/RingsideWebSession.php' );
require_once( 'ringside/social/client/RingsideSocialClientLocal.php' );

$webSession = new RingsideWebSession();
$social = new RingsideSocialClientLocal( RingsideWebConfig::$networkKey, null, $webSession->getSocial() );
if ( $social->inSession() === false ) {

	RingsideWebUtils::redirect(RingsideWebConfig::$webRoot."/index.php");

} else {

   $canvas = "Logout";
   $canvas_content = $social->render( 'canvas', null, 'login', '' );
   
   $social->clearSession();
   
   $webSession->clearSession();
   session_destroy();
   
   // Deleting the social session key is required
   // for rendering to stop identifying a user.
   if(array_key_exists('social_session_key',$_COOKIE)){
   		// Force this cookie to expire
   		setcookie('social_session_key', "",time()-3600);
   }
   
   // You may not always want to go to the home page
   // After logging off, espcially if you are widget
   if(array_key_exists('social_callback',$_REQUEST)){
   		RingsideWebUtils::redirect($_REQUEST['social_callback']);
   } else {
   		RingsideWebUtils::redirect(RingsideWebConfig::$webRoot."/index.php");
   }
}

include('oneapp.inc');

?>