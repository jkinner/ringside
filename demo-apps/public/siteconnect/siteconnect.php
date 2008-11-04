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
require_once('ringside/api/clients/SiteConnectClient.php');
require_once('LocalSettings.php');

/*
 * Create the Site Connect client. This client enables us to create a Ringside session on
 * behalf of a user on this web site.
 */
$siteconnect = new SiteConnectClient('example.com','6f821b47b2e32fd12ec4f73da6d46de2');

if ( isset($_REQUEST['uid']) || ! isset($_SESSION['ringside_session']) || empty($_SESSION['ringside_session']) ) {
    $ringside_session = $siteconnect->createSession(isset($_REQUEST['uid'])?$_REQUEST['uid']:100000);
    $_SESSION['ringside_session'] = $ringside_session;
}
?>