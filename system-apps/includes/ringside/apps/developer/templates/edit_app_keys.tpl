<h1>Developer</h1>
<h2><?php echo $pageHeader; ?></h2>

<?php
$appPropsSelected = 'false';
$payPlansSelected = 'false';
$manageKeysSelected = 'true';
include("ringside/apps/developer/templates/tabitems.tpl");
?>

<div class="developer-content-block">

<fb:editor action="edit_app_keys.php" labelwidth="150" width="525">	
	

   <div style="font-weight: bold; color: red; align: center;"><?php echo $errorMessage; ?></div>
   <div style="font-style: italic; align: center;"><?php echo $statusMessage; ?></div>

	
	<fb:editor-custom>
   	<input type="hidden" name="app_id" value="<?php echo $appId; ?>" />
   	<input type="hidden" name="form_action" value="<?php echo $formAction; ?>" />
   </fb:editor-custom>
	
<?php foreach ($keySets as $keySet) {
			if ( $keySet['network_id'] == RingsideSocialConfig::$apiKey ) {
				// Do NOT show the "local" Ringside's API key and secret for this application; they are read-only
				continue;
			}
?>			 
		<fb:editor-custom>
   		<h3>Network: <?php echo $keySet['name']; ?></h3>
   	</fb:editor-custom>
   	
   	<fb:editor-text label="API Key" name="<?php echo 'api_key_' . $keySet['network_id']; ?>" value="<?php echo $keySet['api_key']; ?>" />
		<fb:editor-text label="Secret" name="<?php echo 'secret_key_' . $keySet['network_id']; ?>" value="<?php echo $keySet['secret']; ?>" />
<?php	} ?>

	<fb:editor-buttonset>
		<fb:editor-button value="Save Keys" class="btn-input" />
	</fb:editor-buttonset>
	
</fb:editor>

</div>
