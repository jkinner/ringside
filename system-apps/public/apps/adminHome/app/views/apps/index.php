<?php

require_once 'ringside/m3/AbstractRest.php';
require_once 'ringside/api/bo/App.php';

$apps = Api_Bo_App::getAllApplicationsAndKeys();

$ajaxUrl = $this->url_for($controller = "apps", $action = null, $only_path = false);

?>
<h1>Applications</h1>
<table style=" background-color: #EEF2F3; border:1px solid black; width:95%; margin: auto;" cellpadding="0" cellspacing="0" >
  <tr>
    <td style="width:10px;overflow: hidden; " valign="top">
      <div id="navcontainerVert">
        <ul id="navlistVert" style="padding:0px;margin:0px;">
              
 
                <?php for($i=0; $i<count($apps);$i++) { ?>  
                  <li>
                      <a href="#app<?php print $apps[$i]['id']; ?>" id="app<?php print $apps[$i]['id']; ?>" appname="<?php print $apps[$i]['name']; ?>" api_key="<?php print $apps[$i]['keys'][0]['api_key'] ?>">
                      <span class="option">
                        <img src="<?php print $apps[$i]['icon_url']; ?>" class="icon" />
                        <span class="details">
                          <span class="line1"><?php print $apps[$i]['name']; ?></span><br/>
                          <span class="line2">By: <?php print $apps[$i]['author']; ?></span>
                        </span>
                      </span>
                    </a>
                  </li>
                <?php } ?>

            </ul>
          </div>
      </td>
      <td id="content" valign="top" style="padding:10px;">
        <div id="navcontainerHort">
          <ul id="navlistHort">
            <li><a href="#" id="dash" page="<?php print $ajaxUrl; ?>&partial=dashboard" >Dashboard</a></li>
            <li><a href="#" id="stat" page="<?php print $ajaxUrl; ?>&partial=stats" >Stats</a></li>
            <li><a href="#" id="feed" page="<?php print $ajaxUrl; ?>&partial=feed" >Feed</a></li>
            <li><a href="#" id="prop" page="<?php print $ajaxUrl; ?>&partial=properties" >Properties</a></li>
            <li><a href="#" id="paym" page="<?php print $ajaxUrl; ?>&partial=payment" >Payment</a></li>
            <li><a href="#" id="keys" page="<?php print $ajaxUrl; ?>&partial=keys" >Keys</a></li>
            <li><a href="#" id="code" page="<?php print $ajaxUrl; ?>&partial=get_code" >Get Code</a></li>
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
  

