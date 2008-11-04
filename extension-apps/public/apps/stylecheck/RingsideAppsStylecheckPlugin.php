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

class RingsideAppsStylecheckPlugin {
   
   public $api_key = 'stylecheck-api-key';
   public $secret_key = 'stylecheck-secret-key';
   public $call_back = 'stylecheck/index.php';
   public $name = 'Style Check';
   public $canvas_url = 'stylecheck';
   public $sidenav_url = '';
   public $isdefault = 1;
   
   public $canvas_type = 1;
   public $support_email = '';
   public $application_type = 1;
   public $mobile = 0;
   public $deployed = 0;
   public $description = "View tags and style checks";
   public $default_fbml = null;
   public $tos_url = '';
   public $icon_url = '';
   public $postadd_url = '';
   public $postremove_url = '';
   public $privacy_url = '';
   public $ip_list = '';
   public $about_url = '';
   
}

?>
