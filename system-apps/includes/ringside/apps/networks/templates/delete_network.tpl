<h2>Delete Network</h2>

<div style="background-color: #dddddd; text: #000000; width: 75%; align: center; border-width: thin; border-style: solid">
   
   <div style="font-weight: bold;"><?php echo $statusMessage; ?></div>
   <div style="font-weight: bold; color: red"><?php echo $errorMessage; ?></div>
   
   <fb:editor action="delete_network.php" labelwidth="150">
   
   	<fb:editor-custom>
    		<input type="hidden" name="key" value="<?php echo $key; ?>" />    		
    		<input type="hidden" name="form_action" value="<?php echo $formAction; ?>" />    		    		
    	</fb:editor-custom>
   
   	<fb:editor-buttonset>
			<fb:editor-button value="Delete"/>
		</fb:editor-buttonset>
   </fb:editor>
      
</div>


