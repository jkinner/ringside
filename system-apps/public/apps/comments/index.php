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

include_once( 'CommentsUtils.php' );
require_once( 'ringside/web/config/RingsideWebConfig.php' );
require_once( 'ringside/api/clients/RingsideApiClients.php');

$ringside = new RingsideApiClients( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey );
$ringside->require_login();
$uid = $ringside->get_loggedin_user();

$utils = new CommentsUtils();

if( $utils->hasAddParams() ) {
   $callbackurl = isset( $_GET['callbackurl'] ) ? $_GET['callbackurl'] : '' ;
	$returnurl = $_SERVER['HTTP_REFERER'];
	
	if( !$utils->isAuthorized( ) ) {
	   echo "<fb:redirect url='$returnurl' />";
	}
	else {
		$result = $ringside->api_client->comments_add( $_GET['xid'], $_GET['text'], $_GET['aid']  );
		if( !$utils->selfReferred() && !empty( $callbackurl ) ) {
		   echo "<fb:redirect url='$callbackurl' />";
		}
		else if( !$utils->selfReferred() && !empty( $returnurl ) ) {
		   echo "<fb:redirect url='$returnurl' />";
		}
		else {
?>
	<fb:comments xid="<?php print $_GET['xid']; ?>" candelete="true" numposts="20" showform="true" aid="<?php print $_GET['aid']; ?>" sig="<?php print $_GET['sig']; ?>"/>
	
<?php
		}
	}
}
else if( $utils->hasDeleteParams() ) {
	$callbackurl = $_GET['callbackurl'];
	$returnurl = $_SERVER['HTTP_REFERER'];
	if( !$utils->isAuthorized( ) ) {
		   echo "<fb:redirect url='$returnurl' />";
	}
	else {
		$result = $ringside->api_client->comments_delete( $_GET['xid'], $_GET['cid'], $_GET['aid'] );
		if( !$utils->selfReferred() && !empty( $callbackurl ) ) {
		   echo "<fb:redirect url='$callbackurl' />";
		}
		else if ( !$utils->selfReferred() && !empty( $returnurl ) ) {
		   echo "<fb:redirect url='$returnurl' />";
		}
		else {
?>
	<fb:comments xid="<?php print $_GET['xid']; ?>" candelete="true" numposts="20" showform="true" aid="<?php print $_GET['aid']; ?>" sig="<?php print $_GET['sig']; ?>"/>
	
<?php
		}
	}
}
else if( $utils->hasDisplayParams() ) {
?>
<fb:comments xid="<?php print $_GET['xid']; ?>" candelete="true" numposts="20" showform="true" aid="<?php print $_GET['aid']; ?>" sig="<?php print $_GET['sig']; ?>"/>
<?php
}
else {
?>
<fb:comments xid="<?php print $uid; ?>" candelete="true" numposts="10" showform="true"/>
<?php
}
?>

<p>&nbsp;</p>
<hr/>
<p>&nbsp;</p>

<!-- 

 foreach ($_SESSION as $name => $value) {
  echo 'SESSION : ' . $name . ' = ' . $value . '<br />';
 }
 foreach ($_GET as $name => $value) {
  echo 'GET : ' . $name . ' = ' . $value . '<br />';
 }
 
 foreach ($_POST as $name => $value) {
  echo 'POST : ' . $name . ' = ' . $value . '<br />';
  $_SESSION['last_'.$name]=$value;
 }
 foreach ($_SERVER as $name => $value ) {
  echo 'SERVER : ' . $name . ' = ' . $value . '<br />';
 }
 
 foreach ($_COOKIE as $name => $value ) {
  echo 'COOKIE : ' . $name . ' = ' . $value . '<br />';
 }
-->
