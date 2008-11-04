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
<?php

if ( isset( $error ) && !empty($error)) 
{
	echo  '<div id="error">' . $error . '</div>';
}

?>
<div id="login-sidebar">
	<form method="post" action="login.php" name="loginform" id="loginform">
	
	<?php
	if ( isset ( $nextPage ) && !empty( $nextPage ) )
	{
		echo '<input type="hidden" name="next" value="'.$nextPage.'" id="next" />' . "\n";
	}
	?> <label> <span>Email:</span><br />
	<input type="text"
		class="inputtext" name="email" value="" id="email" size="15" /> </label>
	<br />
	<label> <span>Password:</span><br />
	<input class="inputtext" type="password" name="p" id="pass" size="15" />
	</label> <br />
	
	<label class="persistent">
	<input type="checkbox" id="persistent" name="persistent" value="1"
		class="inputcheckbox" /> <span>Remember me</span> </label> <br />
	
	<input type="submit" value="Login" name="doquicklogin" id="doquicklogin"
		onclick="this.disabled=true; this.form.submit();" class="inputsubmit" />
	</form>
	<ul>
		<li><a href="reset.php">Forgot Password?</a></li>
		<li><a href="register.php">Sign Up!</a></li>
	</ul>
</div>


