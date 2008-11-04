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
<div id="addfriend" style="display: block;">

<div id="header" class="display: block;">
<h2>Add a Friend</h2>
</div>

<?php
$button_label = 'Add Friend!';
$hiddenInputs = '<input type="hidden" name="action" value="add_friend" id="action" />';

if(isset($error) && !empty($error))
{
	echo "<fb:error><fb:message>Add Friend Error</fb:message>$error</fb:error>";
}else if(isset($message) && !empty($message))
{
	echo "<fb:explanation><fb:message>Add Friend Message</fb:message>$message</fb:explanation>";
}

$name = '';
if(isset($_REQUEST['name'])) $name = $_REQUEST['name'];

$fbForm = <<<heredoc
<fb:editor action="$url/friends.php" width="600" labelwidth="450px" >
	<fb:editor-custom>
	$hiddenInputs
	</fb:editor-custom>
	<fb:editor-text label="Friend Name" name="name" value="$name"/>

	<fb:editor-buttonset>
		<fb:editor-button name="submit_button" value="$button_label" />
		<fb:editor-cancel href="friends.php"/>
	</fb:editor-buttonset>
</fb:editor>

<h2>Add a Friend by Email</h2>
<fb:editor action="$url/friends.php" width="600" labelwidth="450px" >
	<fb:editor-custom>
	$hiddenInputs
	</fb:editor-custom>
    <fb:editor-text label="Friend Email" name="email"/>

    <fb:editor-buttonset>
        <fb:editor-button name="submit_button" value="$button_label" />
		  <fb:editor-cancel href="friends.php"/>
	 </fb:editor-buttonset>
</fb:editor>

heredoc;

	echo $fbForm;

	?></div>
