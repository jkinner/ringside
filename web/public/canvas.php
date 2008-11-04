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
 * Canvas page is the controller for displaying most applications. 
 * The standard top and sidebars are used. 
 * The application being invoked is picked from the path info. 
 */

$webSession = new RingsideWebSession();
$social = new RingsideSocialClientLocal( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey, $webSession->getSocial() );
$inSession = $social->inSession();

$pathInfo = isset( $_SERVER['PATH_INFO'] ) ? $_SERVER['PATH_INFO'] : '' ;
if ( empty( $pathInfo ) || strlen( trim($pathInfo)) <  2 ) {
   $canvas = 'index.php';
   $pathInfo = '';
} else {
   $path_parts = explode( '/', trim($pathInfo), 3 );
   $canvas = $path_parts[1];
   if ( isset ($path_parts[2]) ) {
      $pathInfo = $path_parts[2];
   }  else {
      RingsideWebUtils::redirect(ltrim($pathInfo, '/').'/'.($_SERVER['QUERY_STRING']?'?'.$_SERVER['QUERY_STRING']:''));
      return;
   }

   $pathInfo = ltrim( $pathInfo, "/" );
}

if ( $inSession === false ) {
   $left = array(  );
} else {    
   $left = array(  );
}

$top = 'menu';

// This is the same list as is used inside $social->render, below. Hopefully this will be cached soon so there will only be one actual round-trip to the API server.
$canvas_content = $social->render( 'canvas', null, $canvas, $pathInfo );
$error = $social->getError();
$iframe = $social->getIFrame();
if ( $social->getRedirect() != null ) {
   RingsideWebUtils::redirect($social->getRedirect());
} else {
   // Social pages get RAW others get TEMPLATE
   if ( $social->isRaw() ) {
      echo $canvas_content;
   } else {
      include('oneapp.inc');
   }
}
 

?>