<?php
/**
 * Profile Listing Page
 * 
 */
require_once(dirname(__FILE__)."/ProfileApp.php");

// Get reference to our app, model and caclulated fields for display
$app= new ProfileApp();
$updateOccured=$app->saveContactChanges($_REQUEST);
$user=$app->getUserData();
$page=2;
?>


<h1>Edit Profile</h1>

<?php include(dirname(__FILE__)."/menu.inc"); ?>
<?php if($updateOccured){ ?>
<fb:success><fb:message><strong>Success!</strong>&nbsp;&nbsp;  Your changes have been saved. </fb:message></fb:success>
<?php } ?>

<div class="profile-editor-form">
  <form id="form1" name="form1" method="post" action="">
	<table border="0" cellspacing="0">

		<tr>
			<td>Contact Information:</td>
			<td><input type="text" id="contact_value1" name="contact_value1" value="<?php print($app->extractValue($user->userprofileecontact,0,'contact_value',''));?>" />
			<select id="contact_type1" name="contact_type1">
				<?php $app->printNumericOption(1,8,$app->extractValue($user->userprofileecontact,0,'contact_type','email'),array('email','AIM','Google Talk','Skype','Windows Live','Yahoo','Gadu-Gadu','ICQ'),true) ?>
			</select>
			</td>
		</tr>

		<tr>
			<td></td>

			<td><input type="text" id="contact_value2" name="contact_value2" value="<?php print($app->extractValue($user->userprofileecontact,1,'contact_value',''));?>" />
              <select id="contact_type2" name="contact_type2">
				<?php $app->printNumericOption(1,8,$app->extractValue($user->userprofileecontact,1,'contact_type','email') ,array('email','AIM','Google Talk','Skype','Windows Live','Yahoo','Gadu-Gadu','ICQ'),true) ?>
              </select></td>
		</tr>

		<tr>
			<td></td>

			<td><input type="text" id="contact_value3" name="contact_value3" value="<?php print($app->extractValue($user->userprofileecontact,2,'contact_value',''));?>" />
              <select id="contact_type2" name="contact_type3">
 				<?php $app->printNumericOption(1,8,$app->extractValue($user->userprofileecontact,2,'contact_type','email') ,array('email','AIM','Google Talk','Skype','Windows Live','Yahoo','Gadu-Gadu','ICQ'),true) ?>
             </select></td>
		</tr>

		<tr>
			<td></td>

			<td><input type="text" id="contact_value4" name="contact_value4" value="<?php print($app->extractValue($user->userprofileecontact,3,'contact_value',''));?>" />
              <select id="contact_type3" name="contact_type4">
				<?php $app->printNumericOption(1,8,$app->extractValue($user->userprofileecontact,3,'contact_type','email') ,array('email','AIM','Google Talk','Skype','Windows Live','Yahoo','Gadu-Gadu','ICQ'),true) ?>
              </select></td>
		</tr>

		<tr>
			<td></td>

			<td><input type="text" id="contact_value5" name="contact_value5" value="<?php print($app->extractValue($user->userprofileecontact,4,'contact_value',''));?>" />
              <select id="contact_type4" name="contact_type5">
 				<?php $app->printNumericOption(1,8,$app->extractValue($user->userprofilecontact,4,'contact_type','email') ,array('email','AIM','Google Talk','Skype','Windows Live','Yahoo','Gadu-Gadu','ICQ'),true) ?>
              </select></td>
		</tr>

		<tr>
			<td></td>

			<td>&nbsp;</td>
		</tr>

		<tr>
			<td></td>

			<td>
				</td>
		</tr>

		<tr>
			<td>Mobile Phone:</td>
			<td><input type="text" id="mobile_phone" name="mobile_phone" value="<?php print($user->userprofilecontact->mobile_phone); ?>" /></td>
		</tr>

		<tr>
			<td>Land Phone:</td>
			<td><input type="text" id="home_phone" name="home_phone" value="<?php print($user->userprofilecontact->home_phone); ?>"/></td>
		</tr>

		<tr>
			<td></td>

			<td>			</td>
		</tr>

		<tr>
			<td>Address:</td>
			<td><input type="text" id="address" name="address" value="<?php print($user->userprofilecontact->address); ?>" /></td>
		</tr>
		<tr>
			<td>City/Town:</td>
			<td><input id="city" name="city" value="<?php print($user->userprofilecontact->city); ?>" maxlength="100" size="25" autocomplete="off" /></td>
		</tr>

		<tr>
			<td>Zip:</td>
			<td><input type="text" id="zip" name="zip" value="<?php print($user->userprofilecontact->zip); ?>" onblur="" /></td>
		</tr>

		<tr>
			<td></td>

			<td>
						</td>
		</tr>

		<tr>
			<td>Website:</td>

			<td>
			<textarea cols="30" rows="2" id="website" name="website"><?php print($user->userprofilecontact->website); ?></textarea></td>
		</tr>

		<tr>
			<td></td>

			<td>
					</td>
		</tr>

		<tr>
    <td>&nbsp;</td>
    <td>
    	<br />
    	<input name="action" type="submit" id="action" value="Save Changes" class="btn-input" />
    	<input name="action" type="submit" id="action" value="Cancel" class="btn-input" />
    </td>
  </tr>
	</table>
  </form>
</div>
