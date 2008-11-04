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
require_once( 'ringside/web/config/RingsideWebConfig.php' );
require_once( 'ringside/web/RingsideWebUtils.php' );
require_once( 'ringside/api/clients/RingsideApiClients.php' );
require_once( 'ringside/api/db/RingsideApiDbDatabase.php' );

$client = new RingsideApiClients( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey  );
$client->setLocalClient( true );
$client->require_login();
$uid = $client->get_loggedin_user();
$add_new_apps_url=RingsideWebConfig::$webRoot.'/canvas.php/apps/browse.php';
$imgRoot=RingsideWebConfig::$webRoot.'/images/';


?>
<h1 class="float-left nomargin">My Applications</h1>
<a class="btn-nav float-right" href="<?php echo $add_new_apps_url?>">+ Find More Apps</a>
<p class="fixclear" style="padding:10px 0 0 0;">
This page lists all of the applications you have installed for this user profile. 
Use the links to the right of each app to edit settings or remove the app.  You can change your 
settings at any time.
</p>

<table cellpadding="0" cellspacing="0" border="0" id="apps-list">
<?php
$apps = $client->api_client->users_getAppList();

$appsCount = 0;
$appsSizeOfArray = count($apps);
if ( empty( $apps ) ) { 
   $apps = array();
}

foreach($apps as $app) {
   
	$app_id = $app['app_id'];
	$name = $app['name'];
	$remove_url=RingsideWebConfig::$webRoot.'/canvas.php/apps/remove.php?app_id='.$app_id.'&name='.$name;
	$edit_url=	RingsideWebConfig::$webRoot.'/canvas.php/apps?app_id='.$app_id;
	$appClassAttr = '';
	$appClassAttr .= ($appsCount==0) ? ' first' : '';
	$appClassAttr .= ($appsCount==count($apps)-1) ? ' last' : '';
	//u.app_id, u.user_id, u.enabled, a.name, a.canvas_url, a.sidenav_url, a.api_key, 
	//a.callback_url, ap.canvas_type, ap.application_type, ap.description, ap.icon_url, ap.postadd_url, ap.postremove_url, ap.about_url	
?>		
			<tr>
				<td class="app-name<?php echo $appClassAttr ?>" width="340">
					<?php echo '<img src="'.$app['icon_url'].'"" alt="" class=""  />'; ?>
					<?php echo '<a href="'.RingsideWebConfig::$webRoot.'/canvas.php/'.$app['canvas_url'].'/">'.$app['name'].'</a>'; ?>
					<div class="apps_description_body"><?php echo $app['description']; ?></div>
				</td>
				<td class="app-action app-edit<?php echo $appClassAttr ?>"><a href="#" onClick="window.location.href='<?php echo $edit_url?>'">Edit Settings</a></td>
				<td class="app-restrictions<?php echo $appClassAttr ?>">All features enabled</td>
				<td class="app-action app-remove<?php echo $appClassAttr ?>"><a href="#" onClick="if(confirm('Are you sure you want to remove this application?')){ window.location.href='<?php echo $remove_url?>'}">Remove this App</a></td>
			</tr>
<?php
$appsCount++;
}
?>
</table>

<a class="btn-nav float-right" href="<?php echo $add_new_apps_url?>">+ Find More Apps</a>

