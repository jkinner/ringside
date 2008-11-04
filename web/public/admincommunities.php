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

require_once( 'ringside/web/config/RingsideWebConfig.php' );
require_once( 'ringside/web/session/RingsideWebSession.php' );
require_once( 'ringside/social/client/RingsideSocialClientLocal.php' );

/**
 * The index page will determine the current status of the social session.
 * It then can decide how to render the page.
 * Each page is nothing more than a group of applications.
 * The web page then calls to the social engine to load said page.
 */

/*
 * The WEB container will use session to maintain relationship with user.
 * The web will typically persist
 * social = The social session key
 * uid = the current users session.
 *
 */

$webSession = new RingsideWebSession();
$social = new RingsideSocialClientLocal( RingsideWebConfig::$networkKey, null, $webSession->getSocial() );
$inSession = $social->inSession();

/*
 * Two layout options we have as the default setup.
 * Option A. No current user.
 * Left : Login + Advert
 * Canvas : Welcome
 * Menu : blank
 * Option B. Has current user.
 * Left : Apps
 * Canvas :  Welcome
 * Top : blank
 */
if ( $inSession === false ) {
	//   $left = array( 'welcome', 'advert' );
	$top = 'adminMenu';
	$canvas = 'adminCommunities';

} else {
    $top = 'adminMenu';
    $canvas = 'adminCommunities';
}

include("oneapp.inc");
?>

