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

// For Oneapp.inc
$canvas = "Setup";
$top = "empty";
$left = array( 'welcome', 'advert' );

$errorReporting = error_reporting( E_ERROR );
$hasLocalSettings = include( "LocalSettings.php" );
error_reporting( $errorReporting );
if ( $hasLocalSettings === false ) {
   $canvas_content = <<<heredoc
   <h1>To Start this process</h1>
   
   In the directory  you unzipped/untarred this file please <br />
   Copy LocalSettings.php.example to LocalSettings.php and modify appropriately <br />
   
   <hr />
heredoc;
   
   echo $canvas_content;
   return;
} 

require_once( 'ringside/web/RingsideWebUtils.php' );
require_once( 'ringside/web/config/RingsideWebConfig.php' );
require_once( 'ringside/web/session/RingsideWebSession.php' );
require_once( 'ringside/social/client/RingsideSocialClientLocal.php' );

/**
 * The setup page is specifically for rendering the setup process.
 * This is aimed at being a starting point once you have layed out
 * the distribution.   First link to hit is not index.php but
 * rather /setup.php.    This follows the same syntax as everything,
 * everything is an application!   However in the case of setup
 * and probably some other places the application is rendered with
 * a modified render call, as the DB is not setup.  If the DB is setup
 * then the application will be called normal.
 */

$webSession = new RingsideWebSession();
$social = new RingsideSocialClientLocal( RingsideWebConfig::$networkKey, null, $webSession->getSocial() );

// Clear any hint of a session.
$social->clearSession();
$webSession->clearSession();
session_destroy();

$pathInfo = isset( $_SERVER['PATH_INFO'] ) ? $_SERVER['PATH_INFO'] : '';
if ( empty( $pathInfo ) || strlen( trim($pathInfo)) <  2 ) {
   $pathInfo = '';
} else {
   $pathInfo = ltrim( $pathInfo, "/" );
}
$canvas_content = $social->render( 'canvas', null, 'setup', $pathInfo );
include('oneapp.inc');

?>
