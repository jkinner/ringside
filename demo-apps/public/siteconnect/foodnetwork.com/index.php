<?php
 /*
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
  */

/**
 * Document this file.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
require_once('ringside/api/clients/SiteConnectClient.php');
require_once('LocalSettings.php');

/*
 * Create the Site Connect client. This client enables us to create a Ringside session on
 * behalf of a user on this web site.
 */
$siteconnect = new SiteConnectClient('foodnetwork.com','2ce1757c7911627314a12956e21aa92b');

if ( isset($_REQUEST['uid']) || ! isset($_SESSION['ringside_session']) || empty($_SESSION['ringside_session']) ) {
    $ringside_session = $siteconnect->createSession(isset($_REQUEST['uid'])?$_REQUEST['uid']:10906599);
    $_SESSION['ringside_session'] = $ringside_session;
}

?>
<span style='font-size: 30pt; valign: middle; align: center;'><img src='/demo/siteconnect/foodnetwork.com/ajax-loader.gif'>Loading</span>
<script>document.location = 'http://social.example.com:8888/social/render.php?method=app&api_key=aac9dfff0b7595e0d9141619236fdf7a&social_session_key=<?php echo $_SESSION['ringside_session']['session_id'] ?>';</script>
