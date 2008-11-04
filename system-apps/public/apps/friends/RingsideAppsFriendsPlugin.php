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
 * A plugin file loads an application into the system.
 * Hence just copy this into the system and goto the Ringside configuration
 * page.  There you can configure and enable the application.
 */

require_once( 'ringside/apps/RingsideAppsCommon.php' );
require_once( 'ringside/apps/RingsideAppsConfig.php' );

class RingsideAppsFriendsPlugin {

	public $api_key = 'fbfriendskey';
	public $secret_key = 'fbfriendssecret';
	public $call_back = '/ringside/apps/friends/index.php';
	public $name = 'friends';
	public $canvas_url = 'friends';
	public $sidenav_url = 'friends/index.php';
	public $isdefault = 1;

	public $canvas_type = RingsideAppsCommon::CANVASTYPE_DSL;
	public $application_type = RingsideAppsCommon::APPTYPE_WEB;
	public $mobile = 0;
	public $deployed = 0;
	public $description = "Allows user to browse their friends.";
	public $default_fbml = null;
	public $tos_url = '';
	public $icon_url = '';
	public $postadd_url = '';
	public $postremove_url = '';
	public $privacy_url = '';
	public $ip_list = '';
	public $about_url = '';

	public function __construct() {
		$this->support_email = RingsideAppsConfig::$supportEmail;
	}

}

?>
