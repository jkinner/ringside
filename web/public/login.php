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

/**
 * Login.php is the end point for the UI of login.   
 * There are a set parameters which the login application can deal with, however, since
 * we check for session at this point there is an override which needs support here. 
 * 
 * skipcookie - if the session is created walk through the process all over again, but don't log the user out. 
 * popup - don't select oneapp as the template, but choose skinless template by default
 */

$skipCookie = isset ( $_REQUEST['skipcookie']) ? true : false;
$popUp = isset ( $_REQUEST['popup']) ? true : false;

$webSession = new RingsideWebSession();
$social = new RingsideSocialClientLocal( RingsideWebConfig::$networkKey, null, $webSession->getSocial() );

if ( $skipCookie === true ) { 
   $canvas_content = $social->render( 'canvas', null, 'login', '' );
   if ( $social->getRedirect() ) { 
      RingsideWebUtils::redirect($social->getRedirect());
      return;
   }
   
} else if ( $social->inSession() === false || ! $social->getCurrentUser() ) {

   $canvas_content = $social->render( 'canvas', null, 'login', '' );
   if ( $social->inSession() !== false ) {
      $webSession->setSocial( $social->getNetworkSessionKey() );
      RingsideWebUtils::redirect($social->getRedirect());
      return;
   }

} else {
//	error_log("Already logged in as ".$social->getCurrentUser().' on '.$social->getCurrentNetwork());
   $redirect_url = RingsideWebConfig::$webRoot.'/index.php';
	if ( isset($_GET['next']) ) {
		$redirect_url = $_GET['next'];
	} else if ( isset($_POST['next'] ) ) {
		$redirect_url = $_POST['next'];
	}
	RingsideWebUtils::redirect($redirect_url);
	return;
}

if ( $popUp === true ) { 
   include ( 'popup.inc' );
} else {
//   $left = array( 'welcome', 'advert' );
   include('oneapp.inc');
}
?>
