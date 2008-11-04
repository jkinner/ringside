<?php
/*******************************************************************************
 * Ringside Networks, Harnessing the power of social networks.
 *
 * Copyright 2008 Ringside Networks, Inc., and individual contributors as indicated
 * by the @authors tag or express copyright attribution
 * statements applied by the authors.  All third-party contributions are
 * distributed under license by Ringside Networks, Inc.
 *
 * This is free software; you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation; either version 2.1 of
 * the License, or (at your option) any later version.
 *
 * This software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this software; if not, write to the Free
 * Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA
 * 02110-1301 USA, or see the FSF site: http://www.fsf.org.
 ******************************************************************************/

?>

<h1 class="loginh1">Welcome to Ringside!</h1>	
<div id="loginform">
<div id="loginform-inner">
<?php
$button_label = 'Login!';

$hiddenInputs = '';
foreach ( $_REQUEST as $key=>$value ) {
   if ( $key!='PHPSESSID' && $key != 'email' && $key != 'p' && $key!= 'doquicklogin' && $key!= 'persistent') {
      $hiddenInputs .= "<input type='hidden' name='$key' value='$value' />";
   }
}

$email = isset( $_REQUEST['email'] ) ? $_REQUEST['email'] : '';
$email = isset( $_REQUEST['newUser'] ) ? $_REQUEST['newUser'] : '';
$title = isset( $_REQUEST['newUser'] ) ? 'Congratulations!' : 'Instant Demo';
$message = isset( $_REQUEST['newUser'] ) ? 'Registration Succeeded!  Please login.' 
	: 'Log into a prepopulated profile - complete with pre-installed applications, and your very own make-believe friends!  This is the best way to get a quick idea of Ringside&lsquo;s capabilties.';

$fbForm = <<<heredoc
<fb:editor action="login.php" width="600" labelwidth="100" >
<h1 class="logintitle">$title</h1>	
<p>$message</p>
	<fb:editor-custom>
		$hiddenInputs
	</fb:editor-custom>
	<fb:editor-custom label="Email">
		<input type="text" class="login-input-txtField" id="e"
			name="email" value="$email"  />
	</fb:editor-custom>
	<fb:editor-custom label=""><div class="form-spacer" style="margin-bottom:6px;">&nbsp;</div></fb:editor-custom>
	<fb:editor-custom label="Password">
		<input type="password" class="login-input-txtField" id="p"
			name="p" value=""
			autocomplete="off" />
	</fb:editor-custom>
	<fb:editor-custom label=""><div class="form-spacer" style="margin-bottom:6px;">&nbsp;</div></fb:editor-custom>
	<fb:editor-custom label="">
		<input type="checkbox" name="infinite" value="true">&nbsp;Keep me logged in
	</fb:editor-custom>
	<fb:editor-custom label=""><div class="form-spacer" style="margin-bottom:6px;">&nbsp;</div></fb:editor-custom>
	<fb:editor-buttonset>
		<fb:editor-button name="submit_button" value="$button_label" class="btn-input btn-login" />
	</fb:editor-buttonset>
</fb:editor>
heredoc;

echo $fbForm;

if(isset($error) && !empty($error)) {
	echo "<fb:error><fb:message>Login Error</fb:message>$error</fb:error>";
} else if( isset( $_REQUEST['newUser'] ) ) {
	//do nothing
} else {
	echo "<fb:explanation><fb:message>...or start from scratch.  Begin with a blank profile, and build a network of your own.</fb:message><a href=\"register.php\">Create a New Profile</a></fb:explanation>";
}
?>
</div><!-- end div#loginform-inner -->
</div><!-- end div#loginform -->


	














