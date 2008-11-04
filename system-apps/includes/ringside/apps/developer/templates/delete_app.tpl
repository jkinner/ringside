<h2>Delete App</h2>
<br />
<div>
   
   <div style="font-weight: bold;"><?php echo $statusMessage; ?></div>
   <div style="font-weight: bold; color: red"><?php echo $errorMessage; ?></div>
   
   <fb:editor action="delete_app.php" labelwidth="150">
   
   	<fb:editor-custom>
    		<input type="hidden" name="app_id" value="<?php echo $appId; ?>" />    		
    		<input type="hidden" name="form_action" value="<?php echo $formAction; ?>" />    	
    		<br />	    		
    	</fb:editor-custom>
   
   	<fb:editor-buttonset>
			<fb:editor-button value="Delete" class="btn-input" />
		</fb:editor-buttonset>
   </fb:editor>
      
</div>


