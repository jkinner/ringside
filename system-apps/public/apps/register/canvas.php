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
<script type="text/javascript" src="<?php echo RingsideWebConfig::$webRoot ?>/js/password_util.js"></script>

<h1 class="loginh1">Welcome to Ringside!</h1>
<div id="registration">
<div id="registration-inner">

<div id="step1" class="step">
<h1 class="registrationtitle">Create a New Profile</h1>
<p>Start from scratch!  Begin with a blank profile, and build a network of your own.  
If this sounds like too much work, start with our <a href="<?php echo RingsideWebConfig::$webRoot ?>/login.php">Instant Demo</a>.</p>
</div>

<?php
$button_label = 'Sign Up!';
$hiddenInputs = '';
foreach ( $_REQUEST as $key=>$value ) {
	// Do not copy the values as hidden fields
	if ( $key!='PHPSESSID'
		  && $key != 'name'
		  && $key != 'email'
		  && $key != 'reg_passwd__') {
		$hiddenInputs .= '<input type="hidden" name="'.$key.'" value="'.$value.'" id="'.$key.'" />';
	}
}

if(isset($error) && !empty($error)) {
	echo "<fb:error><fb:message>Registration Error</fb:message>$error</fb:error>";
}

$name = '';
$email = '';

if(isset($_REQUEST['name'])) $name = $_REQUEST['name'];
if(isset($_REQUEST['email'])) $email = $_REQUEST['email'];

$fbForm = <<<heredoc
<fb:editor action="register.php" width="600" labelwidth="150" >
	<fb:editor-custom>
		$hiddenInputs
	</fb:editor-custom>
	<fb:editor-text label="Full Name" name="name" value="$name"/>
	<fb:editor-text label="Email" name="email" value="$email"/>
	<fb:editor-custom label="Create Password">
		<input type="password" class="inputpassword" id="reg_passwd__"
			name="reg_passwd__" value=""
			onkeyup="update_strength('reg_passwd__','reg_passwd__strength_display__')"
			autocomplete="off" />
		<div style="display:block;" id="reg_passwd__strength_display__" class="tips">Password strength</div>
	</fb:editor-custom>

	<fb:editor-buttonset>
		<fb:editor-button name="submit_button" value="$button_label" />
		<fb:editor-cancel href="login.php"/>
	</fb:editor-buttonset>
</fb:editor>
heredoc;

echo $fbForm;

?>
</div><!-- end div#registration -->
</div><!-- end div#registration-inner -->

