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

foreach($friends as $friend)
{
	if(isset($friend) && !empty($friend))
	{
		$img = '';
		$pic = $friend['pic_small_url'];
		$name = $friend['first_name'].' '.$friend['last_name'];
		$user_id = $friend['user_id'];
		if(isset($pic) && strlen($pic) > 0)
		{
			$img = "<img src='$pic'/>";
		}
?>
<div class="column1" style="float: left;">
<a href="<?php echo $url ?>/canvas.php/profile/?id=<?php echo $user_id ?>"><?php echo $img ?><?php echo $name ?></a>
</div>
<BR><BR>
<hr>
<?php
	}
}
?>