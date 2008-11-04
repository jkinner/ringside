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
// print "<pre>";
// print_r($_SERVER);
// print "</pre>";
// require_once("ringside/web/RingsideWebUtils.php");
// require_once("ringside/web/config/RingsideWebConfig.php");
// require_once("ringside/api/clients/RingsideApiClients.php");
// 
require_once( "ringside/api/dao/Network.php" );

//This lines should be move to an include file?
// $client = new RingsideApiClients(RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey);
// $client->setLocalClient(true);
// $uid = $client->require_login();

$networks = Api_Dao_Network::getNetworksPropertiesForUser($this->uid);

//This db call needs to be fixed. It is not filtering by userid!
//$networks = Api_Dao_Network::getNetworksPropertiesForUser($user);

//$numberOfNetworks = count( $networks );

//$resp = $client->api_client->admin_getNetworkProperties(array(), array('name', 'key', 'trust_login_url'));

//Get the url for the widget code for the code generat
$socialWidgetUrl = RingsideApiClientsConfig::$socialUrl . "/widget.php";

$defaultIconUrl = 'http://localhost:8080/web/images/default-app.png';

$ajaxUrl = $this->url_for($controller = "communities", $action = null, $only_path = false);
?>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
        google.load("visualization", "1", {packages:["piechart","barchart","columnchart"]});
    </script>

<h1>Communities (<?php print count($networks); ?>)</h1>
<br/>
  <table style=" background-color: #EEF2F3; border:1px solid black; width:95%; margin: auto;" cellpadding="0" cellspacing="0" >
    <tr>
      <td style="width:10px;overflow: hidden; " valign="top">
        <div id="navcontainerVert">
          <ul id="navlistVert" style="padding:0px;margin:0px;">
            
            
            <?php
            
            foreach ($networks as $net) {

            	$key = $net['trust_key'];
            	$name = $net['trust_name'];
            	
            	//I do not think this has an icon_url!!
            	$icon_url = (empty($net['icon_url']))?$defaultIconUrl:$net['icon_url'];

            		//trust_key 
            		//trust_name 
            		//trust_auth_url 
            		//trust_login_url 
            		//trust_canvas_url 
            		//trust_web_url 	


            	if (strlen(trim($key)) > 0) { ?>
                <li>
                  <a href="#<?php print $key; ?>" id="<?php print $net['trust_key']; ?>">
                    <span class="option">
                      <img src="<?php print $icon_url; ?>" class="icon" />
                      <span class="details">
                        <span class="line1"><?php print $net['trust_name']; ?></span><br/>
                        <span class="line2"><?php print $net['trust_web_url'] ?></span> 
                      </span>
                    </span>
                  </a>
                </li>
            	<?php }
            }
            
            ?>
          </ul>
        </div>
    </td>
    <td id="content" valign="top" style="padding:10px;">
      <div id="navcontainerHort">
        <ul id="navlistHort">
          <li><a href="#" id="dash" page="<?php print  $ajaxUrl; ?>&partial=dashboard" >Dashboard</a></li>
          <li><a href="#" id="stat" page="<?php print  $ajaxUrl; ?>&partial=stats" >Stats</a></li>
          <li><a href="#" id="feed" page="<?php print  $ajaxUrl; ?>&partial=feed" >Feed</a></li>
          <li><a href="#" id="prop" page="<?php print  $ajaxUrl; ?>&partial=properties" params="form_action=edit">Properties</a></li>
          <li><a href="#" id="paym" page="<?php print  $ajaxUrl; ?>&partial=payment" >Payment</a></li>
          <li><a href="#" id="keys" page="<?php print  $ajaxUrl; ?>&partial=keys" >Keys</a></li>
          <li><a href="#" id="code" page="<?php print  $ajaxUrl; ?>&partial=get_code" >Get Code</a></li>
        </ul>
      </div>
      <br/>
      
      <div id="loadingIcon">
        <img src="http://localhost:8080/system-apps/apps/adminHome/public/images/ajax-loader.gif" />
      </div>
      
      <div id="output">
        Loading...
      </div>
      
    </td>
  </tr>
</table>


  


