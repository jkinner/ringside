
<h1>Developer</h1>
<h2><?php echo $pageHeader; ?></h2>
<br />

<?php
	if (($formAction == "update") || ($formAction == "edit")) {; 
   	$appPropsSelected = 'true';
   	$payPlansSelected = 'false';
   	$manageKeysSelected = 'false';
   	include("ringside/apps/developer/templates/tabitems.tpl");
   };
?>

<div class="rs-content-block" >

	
   <div style="font-weight: bold; color: red; align: center;"><?php echo $errorMessage; ?></div><br />
   <div style="font-style: italic; align: center;"><?php echo $statusMessage; ?></div><br />


	
	<fb:editor action="edit_app.php" labelwidth="150" width="525">		
		<fb:editor-custom> 		
    		<input type="hidden" name="form_action" value="<?php echo $formAction; ?>" /> 
    		<input type="hidden" name="app_id" value="<?php echo $appId; ?>" />  
    		<br />   		    		
    	</fb:editor-custom>    	    	
		<fb:editor-text label="Name" name="app_name" value="<?php echo $appName; ?>"/>
		<fb:editor-custom>
			The name of your application.
		</fb:editor-custom>
		<?php if (($formAction == "update") || ($formAction == "edit")) {; ?>
		
			<fb:editor-custom><br /><hr /><br /></fb:editor-custom>
			<fb:editor-custom label="API Key"><?php echo $apiKey; ?></fb:editor-custom>
			<fb:editor-custom>
				Unique API key for your application.
			</fb:editor-custom>
			<fb:editor-custom label="API Secret"><?php echo $secret; ?></fb:editor-custom>
			<fb:editor-custom>
				Unique secret key for your application.
			</fb:editor-custom>
			<fb:editor-text label="Icon URL" name="icon_url" value="<?php echo $iconUrl; ?>" />
			<fb:editor-custom>
				URL of application icon (.jpg, .png, .gif, etc.)
			</fb:editor-custom>
			<fb:editor-text label="Developer Email" name="email" value="<?php echo $email; ?>" />
			<fb:editor-custom>
				Contact email of developer.
			</fb:editor-custom>
			<fb:editor-text label="Callback URL" name="callback_url" value="<?php echo $callbackUrl; ?>" />
			<fb:editor-custom>
				The URL where your application is hosted.
			</fb:editor-custom>
			<fb:editor-text label="Canvas URL" name="canvas_url" value="<?php echo $canvasUrl; ?>" />
			<fb:editor-custom>
				The URL that users will use to access your application.
			</fb:editor-custom>
			<fb:editor-custom>
				<input type="radio" name="canvas_type" value="fbml" <?php echo $fbmlChecked; ?> />Use FBML
				<input type="radio" name="canvas_type" value="iframe" <?php echo $iframeChecked; ?> />Use IFrame
				<input type="radio" name="canvas_type" value="os" <?php echo $osChecked; ?> />Use Open Social
			</fb:editor-custom>
			<fb:editor-custom>
				The type of display for your application - choose iframe to render your app as an iframe, or FBML which may or may not include iframes.
			</fb:editor-custom>
			<fb:editor-custom label="Application Type">
				<input type="radio" name="app_type" value="web" <?php echo $webChecked; ?> />Website
				<input type="radio" name="app_type" value="desktop" <?php echo $desktopChecked; ?> />Desktop
			</fb:editor-custom>
			<fb:editor-custom>
				Whether your app is hosted via Ringside (web) or a desktop application.
			</fb:editor-custom>
			<fb:editor-custom label="Mobile Integration">
				<input type="checkbox" name="mobile" <?php echo $mobileChecked; ?> />My Application is Mobile
			</fb:editor-custom>
			<fb:editor-custom>
				Check this if you have a mobile application.
			</fb:editor-custom>
			<fb:editor-custom label="Deployed">
				<input type="radio" name="deployed" value="1" <?php echo $yesDeployedChecked; ?> />Yes
				<input type="radio" name="deployed" value="0" <?php echo $noDeployedChecked; ?> />No
			</fb:editor-custom>
			<fb:editor-custom>
				Check this if your application is to be deployed publicly.
			</fb:editor-custom>
			<fb:editor-text label="IP Addresses" name="ip_list" value="<?php echo $ipList; ?>" />
			<fb:editor-custom>
				Block all IP addresses from accessing your app except the ones listed here. If no addresses are specified, then noone will be blocked.
			</fb:editor-custom>
			<fb:editor-text label="TOS URL" name="tos_url" value="<?php echo $tosUrl; ?>" />
			<fb:editor-custom>
				URL which points to your terms of service.
			</fb:editor-custom>
			
			<fb:editor-custom><br /><hr /><br /></fb:editor-custom>
			
			<fb:editor-text label="Post-Add URL" name="post_install_url" value="<?php echo $postInstallUrl; ?>" />
			<fb:editor-custom>
				URL users will be redirected to after adding your application.
			</fb:editor-custom>
			<fb:editor-textarea label="Description" name="description"><?php echo $description; ?></fb:editor-textarea>
			<fb:editor-custom>
				A text description of your application.
			</fb:editor-custom>
			<fb:editor-text label="Post-Remove URL" name="post_remove_url" value="<?php echo $postRemoveUrl; ?>" />
				<fb:editor-custom>
				URL users will be redirected to after removing your application.
			</fb:editor-custom>
			<fb:editor-textarea label="Default FBML" name="default_fbml"><?php echo $defaultFbml; ?></fb:editor-textarea>
			<fb:editor-custom>
				Default FBML to be rendered into the user's profile after adding the app.
			</fb:editor-custom>			
			<fb:editor-custom label="Default Profile Box Column">
				<input type="radio" name="default_column" value="1" <?php echo $wideChecked; ?> />Wide
				<input type="radio" name="default_column" value="0" <?php echo $narrowChecked; ?> />Narrow
			</fb:editor-custom>
			<fb:editor-custom>
				Column to display app within user's profile.
			</fb:editor-custom>			
			<fb:editor-custom label="Developer Mode">
				<input type="checkbox" name="dev_mode" <?php echo $developerModeChecked; ?> />Developers Only
			</fb:editor-custom>
			<fb:editor-custom>
				Allows only developers of the app to view the app.
			</fb:editor-custom>			
			<fb:editor-text label="Edit URL" name="edit_url" value="<?php echo $editUrl; ?>" />
			<fb:editor-custom>
				URL for users to edit their app settings.
			</fb:editor-custom>			
			
			<fb:editor-custom><br /><hr /><br /></fb:editor-custom>
			
			<fb:editor-text label="Side Navigation URL" name="sidenav_url" value="<?php echo $sidenavUrl; ?>" />
			<fb:editor-custom>
				URL for your app if you want a sidebar link.
			</fb:editor-custom>
			<fb:editor-text label="Privacy URL" name="privacy_url" value="<?php echo $privacyUrl; ?>" />
			<fb:editor-custom>
				URL for your app's privacy page.
			</fb:editor-custom>
			<fb:editor-text label="Attachment Action" name="attachment_action" value="<?php echo $attachmentAction; ?>" />
			<fb:editor-custom>
				Javascript action called when a user creates a wall or message attachment.
			</fb:editor-custom>
			<fb:editor-text label="Attachment Callback URL" name="attachment_callback_url" value="<?php echo $attachmentCallbackUrl; ?>" />
			<fb:editor-custom>
				The URL where attachments can be retrieved.
			</fb:editor-custom>
			<fb:editor-custom><br /><hr /><br /></fb:editor-custom>
		<?php }; ?>
		<fb:editor-custom>
				<br />
		</fb:editor-custom>
		<fb:editor-buttonset>
			<fb:editor-button value="<?php echo $submitText; ?>" class="btn-input" />
		</fb:editor-buttonset>
	</fb:editor>
		
<br /><br />
</div>
