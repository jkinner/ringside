<?php
/**
 * Personal Information Listing Page
 * 
 */
require_once(dirname(__FILE__)."/ProfileApp.php");

// Get reference to our app, model and caclulated fields for display
$app= new ProfileApp();
$updateOccured=$app->savePersonalChanges($_REQUEST);
$user=$app->getUserData();
$page=4;
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
				<td>Activities:</td>

				<td>
				<textarea id="activities" name="activities" cols="30" rows="3"><?php print($user->userprofilepersonal->activities); ?></textarea><br />
				<span id="clubs_msg" style="display: none;"><small>(max 1,500 characters)</small></span></td>
			</tr>

			<tr>
				<td>Interests:</td>

				<td>
				<textarea  id="interests" name="interests" cols="30" rows="3"><?php print($user->userprofilepersonal->interests); ?></textarea><br />
				<span id="interests_msg" style="display: none;"><small>(max 1,500 characters)</small></span></td>
			</tr>

			<tr>
				<td>Favorite Music:</td>

				<td>
				<textarea id="music" name="music" cols="30" rows="3"><?php print($user->userprofilepersonal->music); ?></textarea><br />
				<span id="music_msg" style="display: none;"><small>(max 1,500 characters)</small></span></td>
			</tr>

			<tr>
				<td>Favorite TV Shows:</td>

				<td>
				<textarea id="tv" name="tv" cols="30" rows="3"><?php print($user->userprofilepersonal->tv); ?></textarea><br />
				<span id="tv_msg" style="display: none;"><small>(max 1,500 characters)</small></span></td>
			</tr>

			<tr>
				<td>Favorite Movies:</td>

				<td>
				<textarea id="movies" name="movies" cols="30" rows="3"><?php print($user->userprofilepersonal->movies); ?></textarea><br />
				<span id="movies_msg" style="display: none;"><small>(max 1,500 characters)</small></span></td>
			</tr>

			<tr>
				<td>Favorite Books:</td>

				<td>
				<textarea id="books" name="books" cols="30" rows="3"><?php print($user->userprofilepersonal->books); ?></textarea><br />
				<span id="books_msg" style="display: none;"><small>(max 1,500 characters)</small></span></td>
			</tr>

			<tr>
				<td>Favorite Quotes:</td>

				<td>
				<textarea id="quotes" name="quotes" cols="30" rows="3"><?php print($user->userprofilepersonal->quotes); ?></textarea><br />
				<span id="quote_msg" style="display: none;"><small>(max 3,000 characters)</small></span></td>
			</tr>

			<tr>
				<td>About Me:</td>

				<td>
				<textarea id="about" name="about" cols="30" rows="3"><?php print($user->userprofilepersonal->about); ?></textarea><br />
				<span id="about_me_msg" style="display: none;"><small>(max 3,000 characters)</small></span></td>
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
