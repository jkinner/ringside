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

$status_updates_checked = "true";
$create_listing_checked="true";
$photo_upload_checked="true";
$auth_information_checked="true";
$auth_profile_checked="true";
$auth_leftnav_checked="true";
$auth_newsfeeds_checked="true";

$app_row = $client->api_client->users_getApp($app_id);
$app = $app_row['app'];

$button_label = "Add application!";
if($status == 'enabled') {   
	$button_label = "Update Application!";
	if(isset($app['allows_status_update']) && $app['allows_status_update']==0)$status_updates_checked="false";
	if(isset($app['allows_create_listing']) && $app['allows_create_listing']==0)$create_listing_checked="false";
	if(isset($app['allows_photo_upload']) && $app['allows_photo_upload']==0)$photo_upload_checked="false";
	if(isset($app['auth_information']) && $app['auth_information']==0)$auth_information_checked="false";
	if(isset($app['auth_profile']) && $app['auth_profile']==0)$auth_profile_checked="false";
	if(isset($app['auth_leftnav']) && $app['auth_leftnav']==0)$auth_leftnav_checked="false";
	if(isset($app['auth_newsfeeds']) && $app['auth_newsfeeds']==0)$auth_newsfeeds_checked="false";
}

$hiddenInputs = '';
foreach ( $_REQUEST as $key=>$value ) {
	if ( $key!='PHPSESSID') {
		$hiddenInputs .= '<input type="hidden" name="'.$key.'" value="'.$value.'" id="'.$key.'" />';
	}
}

$fbForm = <<<heredoc
<fb:editor action="" width="600" labelwidth="450px" >
<fb:editor-custom>
   <input type="hidden" name="app_id" value="$app_id" />
   <input type="hidden" name="status" value="$status" />
   $hiddenInputs
</fb:editor-custom>
	<fb:editor-checkbox label="Allow Status Updates" checked="$status_updates_checked" name="allows_status_update" value="yes" />
	<fb:editor-checkbox label="Allow Create Listing" checked="$create_listing_checked" name="allows_create_listing" value="yes" />
	<fb:editor-checkbox label="Allow Photo Upload" checked="$photo_upload_checked" name="allows_photo_upload" value="yes" />
	<fb:editor-checkbox label="Authorize profile" checked="$auth_profile_checked" name="auth_profile" value="yes" />
	<fb:editor-checkbox label="Authorize information" checked="$auth_information_checked" name="auth_information" value="yes" />
	<fb:editor-checkbox label="Authorize Left Navigation" checked="$auth_leftnav_checked" name="auth_leftnav" value="yes" />
	<fb:editor-checkbox label="Authorize Newsfeeds" checked="$auth_newsfeeds_checked" name="auth_newsfeeds" value="yes" />
	<fb:editor-buttonset>
		<fb:editor-button name="submit_button" value="$button_label" />
	</fb:editor-buttonset>
</fb:editor>
heredoc;

echo $fbForm;
		
