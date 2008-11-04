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

if(isset($error) && !empty($error))
{
	echo "<fb:error><fb:message>Invites Error</fb:message>$error</fb:error>";
}else if(isset($message) && !empty($message))
{
	echo "<fb:explanation><fb:message>Invites Info</fb:message>$message</fb:explanation>";
}
?>
<div class="display: block;">Unacknowledged Invites</div>
<div class="display: block;">
<?php

if(isset($invites))
{
	foreach($invites as $fuid)
	{
		if(isset($fuid) && strlen(trim($fuid)) > 0)
		{
			$user_info = $client->api_client->users_getInfo($fuid, "first_name");
			$friend_name = $user_info[0]['first_name'];
			$base_url = "$url/friends.php/?action=accept_friend&fuid=$fuid";
			if ( isset($inv_source) )
			{
			    $base_url .= "&inv=$inv_source";
			}
			echo "<div>$friend_name wants to add you to their friends list.
				<a href='$base_url&access=1&status=2'>Allow</a> |
				<a href='$base_url&access=0&status=0'>Deny</a></div>";
		}
	}
}
?>
</div>
