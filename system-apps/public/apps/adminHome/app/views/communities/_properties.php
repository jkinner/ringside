<?php


require_once( 'ringside/web/RingsideWebUtils.php' );
require_once( 'ringside/api/clients/RingsideApiClients.php');
require_once( 'ringside/web/config/RingsideWebConfig.php');
require_once( "ringside/api/dao/Network.php" );



$client = new RingsideApiClients( RingsideSocialConfig::$apiKey, RingsideSocialConfig::$secretKey  );
$client->require_login();
$uid = $client->get_loggedin_user();


function getRequestParam($name, $default = NULL)
{
	if (isset($_REQUEST[$name])) {		
		return $_REQUEST[$name];
	}
	return $default;
}

$netId = $_REQUEST['net'];


$statusMessage = null;
$errorMessage = null;
$pageHeader = "";
$formAction = $_REQUEST['form_action'];
$createButtonDisplay = "block";

if ($formAction == "edit") { 

    $props = array('key', 'name', 'auth_url', 'login_url',
    							'canvas_url', 'web_url', 'social_url',
    							'auth_class', 'postmap_url');
    try {
    	$resp = $client->api_client->admin_getNetworkProperties(array($netId), $props);
    } catch (Exception $e) {
    	$errorMessage = 'Could not get network properties: ' . $e->getMessage(); 
    }
        
    if ($errorMessage == null) {
        $name = $resp[0]['name'];
        $key = $resp[0]['key'];
        $authUrl = $resp[0]['auth_url'];
        $loginUrl = $resp[0]['login_url'];
        $canvasUrl = $resp[0]['canvas_url'];
        $webUrl = $resp[0]['web_url'];
        $socialUrl = $resp[0]['social_url'];
        $authClass = $resp[0]['auth_class'];
        $postmapUrl = $resp[0]['postmap_url'];
        
        $success = $_REQUEST['success'];
        if ($success != null) {
        	$statusMessage = 'Network successfully updated.';
        }
        $created = $_REQUEST['created'];
        if ($created != null) {
        	$statusMessage = 'Network successfully created.';
        }
    }
    
    $formAction = 'update';
    $pageHeader = 'Edit Network';
    $submitText = 'Save Changes';   
    
} else if ($formAction == 'update') {
	
	$props = array('key', 'name', 'auth_url', 'login_url',
    					'canvas_url', 'web_url', 'social_url',
    					'auth_class', 'postmap_url');
	$oldKey = $_REQUEST('old_key');
	$newVals = array(); 
	foreach ($props as $name) {
		$newVals[$name] = getRequestParam($name, '');
	}
	   
	try {
   	$resp = $client->api_client->admin_setNetworkProperties($oldKey, $newVals);
	} catch (Exception $e) {
		$errorMessage = 'Could not set properties: ' . $e->getMessage();
	}
   if ($errorMessage == null) {
   	RingsideWebUtils::redirect("edit_network.php?key=$key&form_action=edit&success=true");
   } else {   
   	$getFailed = false;
   	try {
        	$resp = $client->api_client->admin_getNetworkProperties(array($oldKey), $props);
      } catch (Exception $e) {
      	$getFailed = true;
        	$errorMessage = 'Could not get network properties: ' . $e->getMessage(); 
      }
        
      if (!$getFailed) {
      	$name = $resp[0]['name'];
         $key = $resp[0]['key'];
         $authUrl = $resp[0]['auth_url'];
         $loginUrl = $resp[0]['login_url'];
         $canvasUrl = $resp[0]['canvas_url'];
         $webUrl = $resp[0]['web_url'];
         $socialUrl = $resp[0]['social_url'];
         $authClass = $resp[0]['auth_class'];
         $postmapUrl = $resp[0]['postmap_url'];
      }
   
   	$formAction = 'update';
    	$pageHeader = 'Edit Network';
    	$submitText = 'Save Changes';
   }

} else if ($formAction == 'new') {

	$pageHeader = 'Create a New Network';
	$formAction = 'new';
	$submitText = 'Create Network';
	$createButtonDisplay = 'none';
	
	$errorMessage = null;
	
	$name = getRequestParam('name', '');
	if (strlen($name) == 0) $errorMessage = 'Please specify a network name.';	
	$authUrl = getRequestParam('auth_url', '');
	if (strlen($authUrl) == 0) $errorMessage = 'Please specify an authorization URL.';
	$loginUrl = getRequestParam('login_url', '');
	if (strlen($loginUrl) == 0) $errorMessage = 'Please specify an login URL.';
	$canvasUrl = getRequestParam('canvas_url', '');
	if (strlen($canvasUrl) == 0) $errorMessage = 'Please specify an canvas URL.';
	$webUrl = getRequestParam('web_url', '');
	if (strlen($webUrl) == 0) $errorMessage = 'Please specify an web URL.';
	
	if ($errorMessage == null) {
		try {
			$resp = $client->api_client->admin_createNetwork($name, $authUrl, $loginUrl, $canvasUrl, $webUrl);			
			$key = $resp['network']['key'];
		} catch (Exception $e) {
			$errorMessage = "Error creating app: " . $e->getMessage();
		}
	}
	if ($errorMessage == null) {
		RingsideWebUtils::redirect("edit_network.php?key=$key&created=true&form_action=edit");
	}


} else {

	$pageHeader = 'Create a New Network';
	$statusMessage = 'Please select a unique name for your network. ' .
						  'A key will be created for you.';
	$formAction = 'new';
	$submitText = 'Create Application';
	$name = '';
}



?>
<h1>community properties</h1>
<input type="button" value="Create A New Network" style="display:<?php print $createButtonDisplay; ?>;float:right;margin-right: 20%;" onclick="createNew()"/>
<h2><?php echo $pageHeader; ?></h2>

<div style="background-color: #dddddd; text: #000000; width: 80%; align: center; border-width: thin; border-style: solid">
	
	<center>
   <div style="font-weight: bold; color: red; align: center;"><?php echo $errorMessage; ?></div>
   <div style="font-style: italic; align: center;"><?php echo $statusMessage; ?></div>
	</center>
	
	<fb:editor action="edit_network.php" labelwidth="150" >		
		<fb:editor-custom> 		
    		<input type="hidden" name="form_action" value="<?php echo $formAction; ?>" /> 
    		<input type="hidden" name="old_key" value="<?php echo $key; ?>" />     		    		
    		<input type="hidden" name="old_name" value="<?php echo htmlentities($name, ENT_QUOTES); ?>" />
    	</fb:editor-custom>    	    	
		<fb:editor-text label="Name" name="name" value="<?php echo htmlentities($name, ENT_QUOTES); ?>"/>
		<fb:editor-custom>
			The name of your network.
		</fb:editor-custom>
		<fb:editor-custom><br /><hr /><br /></fb:editor-custom>
		<fb:editor-text label="Key" name="key" maxlength="32" value="<?php echo htmlentities($key, ENT_QUOTES); ?>" />
		<fb:editor-custom>
			Unique key for your network.
		</fb:editor-custom>
		<fb:editor-text label="Auth URL" name="auth_url" value="<?php echo htmlentities($authUrl, ENT_QUOTES); ?>" />
		<fb:editor-custom>
			URL of API server endpoint.
		</fb:editor-custom>
		<fb:editor-text label="Login URL" name="login_url" value="<?php echo htmlentities($loginUrl, ENT_QUOTES); ?>" />
		<fb:editor-custom>
			URL of login.php endpoint.
		</fb:editor-custom>			
		<fb:editor-text label="Canvas URL" name="canvas_url" value="<?php echo htmlentities($canvasUrl, ENT_QUOTES); ?>" />
		<fb:editor-custom>
			URL of canvas rendering endpoint, i.e. http://api.facebook.com or http://someserver/canvas.php.
		</fb:editor-custom>
		<fb:editor-text label="Web URL" name="web_url" value="<?php echo htmlentities($webUrl, ENT_QUOTES); ?>" />
		<fb:editor-custom>
			URL of web root.
		</fb:editor-custom>
		<?php if (($formAction == "update") || ($formAction == "edit")) {; ?>
			<fb:editor-text label="Social URL" name="social_url" value="<?php echo htmlentities($socialUrl, ENT_QUOTES); ?>" />
			<fb:editor-custom>
				URL of social root.
			</fb:editor-custom>
			<fb:editor-text label="Auth Class" name="auth_class" value="<?php echo htmlentities($authClass, ENT_QUOTES); ?>" />
			<fb:editor-custom>
				The classname of the Authentication class (empty = default class).
			</fb:editor-custom>
			<fb:editor-text label="Post Map URL" name="postmap_url" value="<?php echo htmlentities($postmapUrl, ENT_QUOTES); ?>" />
			<fb:editor-custom>
				The URL user is redirected to after identity mapping takes place.
			</fb:editor-custom>
		<?php }; ?>
		<fb:editor-custom>
		    <div style="text-align:right">
  		        <input type="button" value="<?php echo $submitText; ?>" onclick="handleForm()"/>
		    </div>    		
            <br />
		</fb:editor-custom>
		
    <br />
    <br />

</div>



<script type="text/javascript">
    function createNew(){
        rsAppViewer.ajaxCall("GET","form_action=new");
    }
  
  
    function handleForm(){
        var action = "<?php print $formAction; ?>";
        alert(action);
    }
</script>


