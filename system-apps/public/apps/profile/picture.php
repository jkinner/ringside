<?php
/**
 * Profile Listing Page
 * 
 */
require_once(dirname(__FILE__)."/ProfileApp.php");
require_once("ringside/web/RingsideWebUpload.php");

// Get reference to our app, model and caclulated fields for display
$app= new ProfileApp();
$updateOccured=$app->savePicture($_REQUEST);
$user=$app->getUserData();
$page=7;
?>


<h1>Edit Profile</h1>

<?php include(dirname(__FILE__)."/menu.inc"); ?>
<?php if($updateOccured){ ?>
<fb:success><fb:message><strong>Success!</strong>&nbsp;&nbsp;  Your changes have been saved. </fb:message></fb:success>
<?php } ?>

<div class="profile-editor-form">
	<div style="float:left; width:30%">
		<h2>Current Picture</h2>
		<fb:profile-pic uid="<?php print( $user->id); ?>" />
		<p>You are user ID <?php print( $user->id ); ?>.</p>
	  	<p>&nbsp;</p>
	  	<p>&nbsp;</p>
  	</div>
	<div style="float:right; width:70%">
	<form id="form1" name="form1" method="post" enctype="multipart/form-data" action="">
		<div id="upload_title" style="border-bottom:1px solid">
		<h2>Upload Picture</h2>
		</div>
		<p>You can upload a JPG, GIF or PNG file.</p>
		<p>&nbsp;<input type="file" name="upfile" />
	  	</p>
		<p>
		  <input type="checkbox" name="certify" value="true" />
  		  
 	    I certify that I have the right to distribute this picture and that it does not violate the Terms of Use.</p>
		<p>
		  <input type="submit" name="action" value="Upload Picture" class="btn-input" />
 	</form>
 	
 	<br />
	<form id="form2" name="form2" method="post" enctype="multipart/form-data" action="">
 	
		<p>
		<div style="border-bottom:1px solid">
          <h2>Remove Picture </h2>
	  </div>
		<p>You can remove this picture, but be sure to upload another or we will display a question mark in its place.</p>
		<p>
			<input type="hidden" name="action" value="Remove Picture"/>
		  <input value="Remove Picture" onClick="if(confirm('Are you sure you want to remove your picture?')){ document.form2.submit()}" type="button" class="btn-input" />
		</p>
	</form>
	</div>
</div>
