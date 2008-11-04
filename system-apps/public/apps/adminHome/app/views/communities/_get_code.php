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
require_once ("ringside/rest/AdminGetServerInfo.php");

$client = new RingsideApiClients( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey  );
$client->require_login();
$uid = $client->get_loggedin_user();

$apps = $client->api_client->admin_getAppList();
//$apps = $this->m3->inventoryGetApplications();
//u.app_id, u.user_id, u.enabled, a.name, a.canvas_url, a.sidenav_url, a.api_key,
//a.callback_url, ap.canvas_type, ap.application_type, ap.description, ap.icon_url, ap.postadd_url, ap.postremove_url, ap.about_url

//$infoApi = new AdminGetServerInfo($this->getUserId(), array(), $this->getSession());
//$sinfo = $infoApi->execute();
//$wurl = $sinfo['result']['web_url'];
//if($wurl == 'http://:')
//    $wurl = 'http://localhost:8080';
//$defaultIconUrl = $wurl . '/images/icon-app-default.png';
$defaultIconUrl = 'http://localhost:8080/web/images/default-app.png';


$netId = $_GET['net'];

?>


<style>
#navlist li
{
padding:0px;margin:0px;
display: inline;
/* for IE5 and IE6 */
}

#navlist
{
width: 200px;
/* to display the list horizontaly */
font-family: sans-serif;
margin: 0;
padding: 0;
border-top: 1px #DDD solid;
border-left: 1px #DDD solid;
}

#navlist a
{
display: block;
background-color: #fff;
border-bottom: 1px #DDD solid;
text-align: left;
text-decoration: none;
color: #000;
padding-left:10px;
}

#navlist a.selected { background-color : #DDD; }

#navlist a:hover { background-color: orange; }
#navlist a:visited { color: #000; }


#navcontainerSmall { height:175px;overflow:hidden;}

</style>
<script>
  function fixBrokenIcon(img){
    img.src="<?php echo $defaultIconUrl ?>";
  }
</script>

<table style="width:95%">
<tr>
<td width="18px">

<div id="scrollMeParent" style="height:150px; width:18px; overflow: scroll;" onscroll="document.getElementById('navcontainerSmall').scrollTop = this.scrollTop;">
  <div id="scrollMe">&nbsp;</div>
</div>
</td>

<td width="190px;overflow:hidden;">

<div id="navcontainerSmall">
<ul id="navlist">

<?php

$appsCount = 0;
$appsSizeOfArray = count($apps);

$isAuthorVisible = true;

foreach($apps as $app)
{	
	$isdefault = $app['isdefault'];
	if ($isdefault == 0) {
    	$app_id = $app['id'];
    	$canvas_url = $app['canvas_url'];
    	$name = $app['name'];
    	$author = (empty($app['author']))?'N/A':$app['author'];
		$author_url = $app['author_url'];
    	$description = (empty($app['description']))?'N/A':$app['description'];
		$icon_url = (empty($app['icon_url']))?$defaultIconUrl:$app['icon_url'];
	    $remove_url=RingsideWebConfig::$webRoot.'/canvas.php/apps/remove.php?app_id='.$app_id.'&name='.$name;
    	
    	if ( empty($icon_url) ) { 
    	   $icon_url = RingsideWebConfig::$webRoot . '/images/missing_app.gif';
    	}


?>

		<li>
			<a href="#"			  
			  id="app<?php echo $app_id ?>"
			  appname="<?php echo $name ?>" 
			  appurl="<?php echo RingsideWebConfig::$webRoot.'/canvas.php/'.$canvas_url.'/' ?>"			
		      authorurl="<?php echo $author_url ?>"
		      authorname="<?php echo $author ?>"
			  title="<?php echo $description ?>"			
			>
				<span>
					<img style="margin-top:2px;float: left;" src="<?php echo $icon_url ?>" alt="<?php echo $name ?>" onerror="fixBrokenIcon(this);"/>&nbsp;
				    <?php echo $name ?>
				</span><br/>
				
				<?php if($isAuthorVisible) { ?>
				
				<span style="font-smaller;font-weight:normal;margin-left: 25px;">Author: <?php echo $author ?></span>
				
				<?php } //End of is AuthorVisible?>
				
			</a>
		</li>
			
<?php

	}

}
?>

        </ul>
      </div>
    </td>
    <td valign="top" style="text-align:left; padding:20px;background-color:#DDD;">
      <h1 id="appName"></h1>
      <h4 id="appAuthor"></h4>
      <h3 id="appDesc"></h3>
      <input id="btnCode" type="button" value="get code" />
    </td>
  </tr>
</table>


<script>



//Methods that are specific to this page
var rsAppPicker = {
    
    //function that is called when a link is clicked
    selection : function() {
    
      //grab the application deatils from the element
      var appId = this.selectedIndex.attr("id");
      var appName = this.selectedIndex.attr("appname");   
      var appDesc = this.selectedIndex.attr("title");
      var appUrl = this.selectedIndex.attr("appurl");
      var authorUrl = this.selectedIndex.attr("authorurl");
      var authorName = "Author: " + this.selectedIndex.attr("authorname");
      
      //If we have an author, set their url
      if(authorName=="N/A"){
         authorName = "Author: <a href='" + authorUrl + "'>" + this.selectedIndex.attr("authorname"); + "</a>";
      }
      
      //Set the labels in the panel with the details of the application
      jQuery("#appName").text(appName);
      jQuery("#appDesc").text(appDesc);
      jQuery("#appAuthor").text(authorName);
      
      //set the onclick of the button to get the code
      jQuery("#btnCode").unbind("click");
      jQuery("#btnCode").bind("click",function(){rsAppPicker.openAppCode(appId);});

    },
    
    openAppCode : function(id){
      if(id!=null){
                        
          //Move to the new page to get the code
          var navDetails = "#navcontainerVert_" + id + "-navcontainerHort_code-network_" + menu1.selectedIndex.attr("id");          
          document.location.href = "admin.php?apps" + navDetails;

      } else {
          alert("Please select another application");
      }
      
    },
    
    loadMenu : function(){

	  //check to see if we have anything specified in the page addy         
      var re = new RegExp(menuApp.id + "_(\\w+)","i");
      var tab = window.location.hash.match(re);
      var isSelected = false;
      
      //if we have a length of 2 we have our appid
      if(tab &&tab.length==2){

		  //Make sure the app is in the list
          var elem = jQuery("#" + tab[1]);
          if(elem.length==1){
            menuApp.select(elem);
            isSelected = true;;
          }          
      }
      
      //If nothing is selected, select the default value
      if(!isSelected) menuApp.selectDefault();
      this.scrollToSelected();
      
    },
    
    scrollToSelected : function(){
        	    
        //finds the selected value in the list and scrolls it into view
		var dist = menuApp.selectedIndex.attr("offsetTop");
		
		//scroll the list
		jQuery("#" + menuApp.id).attr({scrollTop:dist});
		
    	//Make the scrollbar on the left scrollable!
    	jQuery("#scrollMe").height( jQuery("#navcontainerSmall").attr("scrollHeight") +"px");

		//scroll the scrollbar
		jQuery("#scrollMeParent").attr({scrollTop:dist});

    }

}



//Register our menu
var menuApp = new ItemSelector("navcontainerSmall", rsAppPicker.selection, null, 0, true);
rsAppPicker.loadMenu();

</script>
