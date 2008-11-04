
<h2><?php echo $pageHeader; ?></h2>

<div style="background-color: #dddddd; text: #000000; width: 75%; align: center; border-width: thin; border-style: solid">
	
	<center>
   <div style="font-weight: bold; color: red; align: center;"><?php echo $errorMessage; ?></div>
   <div style="font-style: italic; align: center;"><?php echo $statusMessage; ?></div>
	</center>
	
	<fb:editor action="edit_network.php" labelwidth="150" width="525">		
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
		<fb:editor-buttonset>
			<fb:editor-button value="<?php echo $submitText; ?>"/>
		</fb:editor-buttonset>
	</fb:editor>
		
    <br />
    <br />

</div>
