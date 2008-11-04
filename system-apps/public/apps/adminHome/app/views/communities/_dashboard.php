<!-- start code -->
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

    require_once("ringside/web/RingsideWebUtils.php");
    require_once("ringside/web/config/RingsideWebConfig.php");
    require_once("ringside/api/clients/RingsideApiClients.php");


    require_once( "ringside/api/dao/Network.php" );

    //This lines should be move to an include file?
     $client = new RingsideApiClients(RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey);
     $client->setLocalClient(true);
     $uid = $client->require_login();

    //$uid = $this->uid;
    
    $netId = $_GET['net'];
    
    $client = new RingsideApiClients( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey  );
    $client->require_login();
    $uid = $client->get_loggedin_user();

    $apps = $client->api_client->admin_getAppList();

    $appList = '';
    
    $count = 0;
    
    foreach($apps as $app)
    {	
        $appId = $app['id'];
    	$name = $app['name'];
    	$iconUrl = (empty($app['icon_url']))?$defaultIconUrl:$app['icon_url'];
    	$linkUrl = '#navcontainerVert_' . $netId . '-navcontainerHort_code-navcontainerSmall_app' . $appId;
    	
    	if ( empty($icon_url) ) { 
    	   $icon_url = RingsideWebConfig::$webRoot . '/images/missing_app.gif';
    	}    	
    	   	
    	$appList .= '<li><a href="' . $linkUrl . '"><span><img src="' . $iconUrl . '"/><br/>' . $name . '</span></li>';

    	$count++;
    	if($count==7) break;
        
    }
  
?>

<!--  TODO: Move this html out of here! -->
<style>
#hortApplist li {
  display: inline;
  list-style-type: none;
  padding: 0px;
  margin: 0px;
  float: left;
}
#hortApplist li span{
  display: block;
  width: 76px;
  height: 85px;
  border : 1px solid #525270;
  text-align: center;
  padding-top: 5px;
}

#hortApplist a:link, #navlist a:visited
{
text-decoration: none;
}

#hortApplist a:hover
{
color: #00f;
background: #f0f;
text-decoration: none;
}
</style>


<table>
	<tr>
		<td>		
			<h3>Stats</h3>
			<div id="" style="background-color: #DBE4E5; width: 350px; height: 200px;">
				M3 chart here
			</div>
			<a href="#navcontainerVert_<?php echo $netId; ?>-navcontainerHort_stat">More Stats</a>
		</td>
		<td>
			<h3>Feed Items</h3>
			<div id="" style="background-color: #DBE4E5; width: 200px; height: 200px;">
				
				<rs:feed uid="<?php print $uid; ?>" /> <!--  Needs to be the feed for the community and not the user! -->
				
			</div>
			<a href="#navcontainerVert_<?php echo $netId; ?>-navcontainerHort_feed">More Feed Items</a>
		</td>	
	</tr>
	<tr>
		<td colspan="2">
			<h3>Add a new App to this Community</h3>
			<div style="background-color: #DBE4E5; width: 550px; height: 100px;">
                <ul id="hortApplist">
				    <?php print $appList ?>
				</ul>
			</div>
			<a href="#navcontainerVert_<?php echo $netId; ?>-navcontainerHort_code">Browse More Applications</a>
		</td>
	</tr>
</table>
<!-- end code -->
