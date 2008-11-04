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
 * A UI to browse/search all the applications installed on a system.
 * Should allow a user to add an application as well.
 */

require_once( 'ringside/web/RingsideWebUtils.php' );
require_once( 'ringside/api/clients/RingsideApiClients.php');
require_once( 'ringside/api/db/RingsideApiDbDatabase.php');

$client = new RingsideApiClients( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey  );
$client->require_login();
$uid = $client->get_loggedin_user();

$apps = $client->api_client->admin_getAppList();
//u.app_id, u.user_id, u.enabled, a.name, a.canvas_url, a.sidenav_url, a.api_key,
//a.callback_url, ap.canvas_type, ap.application_type, ap.description, ap.icon_url, ap.postadd_url, ap.postremove_url, ap.about_url

?>
<h1 style="">Browse Applications</h1>
<p>
This page lists all of the available applications you have on this network.
</p>

<table cellpadding="0" cellspacing="0" border="0" id="apps-list">
<?php
/*
 icon | Name
 | Author
 | Description
 */
$appsCount = 0;
$appsSizeOfArray = count($apps);
foreach($apps as $app)
{	
	$isdefault = $app['isdefault'];
	if ($isdefault == 0) {
    	$app_id = $app['id'];
    	$canvas_url = $app['canvas_url'];
    	$name = $app['name'];
    	$author = $app['author'];
		$author_display = (empty($author)) ? 'style="display:none;"' : '';
    	$author_url = $app['author_url'];
    	$description = $app['description'];
		$description_display = ($description == '') ? 'style="display:none;"' : '';
    	$icon_url = $app['icon_url'];
	    $remove_url=RingsideWebConfig::$webRoot.'/canvas.php/apps/remove.php?app_id='.$app_id.'&name='.$name;
    	
    	if ( empty($icon_url) ) { 
    	   $icon_url = RingsideWebConfig::$webRoot . '/images/missing_app.gif';
    	}

		$appClassAttr = '';
		$appClassAttr .= ($appsCount==0) ? ' first' : '';
		$appClassAttr .= ($appsCount==count($apps)-1) ? ' last' : '';
?>
	<tr>
		<td class="app-name<?php echo $appClassAttr ?>" width="390">
			<a href="<?php echo RingsideWebConfig::$webRoot.'/canvas.php/'.$canvas_url.'/' ?>"><img src="<?php echo $icon_url ?>" alt="<?php echo $name ?>" /></a>
			<a href="<?php echo RingsideWebConfig::$webRoot.'/canvas.php/'.$canvas_url.'/' ?>"><?php echo $name ?></a><br />
			<div <?php echo $author_display ?>><a href="<?php echo $author_url ?>"><?php echo $author ?></a></div>
			<div <?php echo $description_display ?>><?php echo $description ?></div>
		</td>
<?php
        //$result = 'enabled';
        $result = $client->api_client->users_isAppEnabled( $uid, $app_id );
        if ( $result == "enabled" )  {
        	// TODO: need to make remove link be a dialog box
        	// Add this application should also be in a dialog box
?>
		<td class="app-goto<?php echo $appClassAttr ?>">
      		<a href="<?php echo RingsideWebConfig::$webRoot.'/canvas.php/'.$canvas_url.'/' ?>">Go to this Application</a>
		</td>
		<td class="app-action app-remove<?php echo $appClassAttr ?>">
      		<a href="#" onClick="if(confirm('Are you sure you want to remove this application?')){ window.location.href='<?php echo $remove_url?>'}">Remove this Application</a>
		</td>
      
<?php
		} else {
?>
		<td class="app-goto<?php echo $appClassAttr ?>">
      		<a href="<?php echo RingsideWebConfig::$webRoot.'/canvas.php/'.$canvas_url.'/' ?>">Go to this Application</a>
		</td>
		<td class="app-action app-remove<?php echo $appClassAttr ?>">
      		<a href="<?php echo RingsideWebConfig::$webRoot.'/canvas.php/apps?app_id='.$app_id ?>">Add this Application</a>
		</td>
<?php
		}
		$appsCount++;
	}

}
?>
	</tr>
</table>





















