<?php
/**
 * Profile Listing Page
 * 
 */
require_once(dirname(__FILE__)."/ProfileApp.php");

// Get reference to our app, model and caclulated fields for display
$app= new ProfileApp();
$updateOccured=$app->saveRelationshipChanges($_REQUEST);
$user=$app->getUserData();
$formDataArry=$app->getRelationshipFormData();
$page=3;
?>


<h1>Edit Profile</h1>

<?php include(dirname(__FILE__)."/menu.inc"); ?>
<?php if($updateOccured){ ?>
<fb:success><fb:message><strong>Success!</strong>&nbsp;&nbsp;  Your changes have been saved. </fb:message></fb:success>
<?php } ?>

<div class="profile-editor-form">
  <form id="form1" name="form1" method="post" action="">
  
		<table border="0" cellspacing="5">
			<tr>
				<td  align="right">Relationship&nbsp;Status:</td>

				<td colspan="2"><select name="status" id="status"><!-- users_profile_rel -->
					<?php $app->printNumericOption(1,7,$user->userprofilerel->status,array('Select Status:','Single','In a Relationship',
					'Engaged','Married',"It's Complicated",'In an Open Relationship','ICQ'),true,true) ?>
				</select></td>
			</tr>

			<tr>
				<td  align="right" valign="top">Former Name:</td>

			  <td colspan="2"><input type="text" id="alternate_name" name="alternate_name" value="<?php print($user->userprofilerel->alternate_name); ?>" maxlength="20" /><br />
				<small>Note: Please enter a full name. Former Name is only used to help<br> people find you in search and will not show up in your profile. </small></td>
			</tr>

			<tr>
				<td align="right">Interested in:</td>

				<td colspan="2">
					<table border="0" cellspacing="0">
						<tr>
							<td><input type="checkbox" name="interested_in_men" id="interested_in_men" value="true" <?php if($formDataArry['interested_in_men']){ print(' checked="checked" ');} ?> /><label for="interested_in_men">Men</label></td>
							<td><input type="checkbox" name="interested_in_women" id="interested_in_women" value="true" <?php if($formDataArry['interested_in_women']){ print(' checked="checked" ');} ?> /><label for="interested_in_women">Women</label></td>
						</tr>
						<tr>
							<td></td>
						</tr>
					</table>				</td>
			</tr>
			<tr >
				<td  align="right">Looking for:</td>

				<td colspan="2">
		  <tr>
							<td align="right"><label for="looking_for_friendship"></label></td>
							
							<td align="left"><label for="looking_for_dating">
							  <input type="checkbox" name="looking_for_friendship" id="looking_for_friendship" value="true" <?php if($formDataArry['looking_for_friendship']){ print(' checked="checked" ');} ?> />
						    Friendship</label></td>
					
						    <td align="left"><input type="checkbox" name="looking_for_dating" id="looking_for_dating" value="true" <?php if($formDataArry['looking_for_dating']){ print(' checked="checked" ');} ?> />
					        <label for="looking_for_dating">Dating</label></td>
		  </tr>

						<tr>
							<td align="right"><label for="looking_for_relationship"></label></td>
							
							<td align="left"><label for="looking_for_networking">
							  <input type="checkbox" name="looking_for_relationship" id="looking_for_relationship" value="true" <?php if($formDataArry['looking_for_relationship']){ print(' checked="checked" ');} ?>/>
						    A Relationship</label></td>
							<td align="left"><input type="checkbox" name="looking_for_networking" id="looking_for_networking" value="true"  <?php if($formDataArry['looking_for_networking']){ print(' checked="checked" ');} ?> />
						    <label for="looking_for_networking">Networking</label></td>
							<td></td>
						</tr>

						<tr>
							<td></td>
						</tr>
				
			

						<tr>
					    	<td><div align="right">&nbsp;<br /></div></td>
							<td>
								<br />
								<input type="submit" class="btn-input" id="save" name="action" value="Save Changes" />&nbsp;
								<input type="button" class="btn-input" id="" name="action" value="Cancel" />
							</td>
						</tr>
		</table>  
	</form>  
</div>
