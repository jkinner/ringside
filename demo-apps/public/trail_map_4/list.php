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

$lasttopic = 'bogus';
$firstname = '';
foreach( $suggestions as $suggestion ) 
{
	$topic = $suggestion['topic'];
	try {
		$names = $rest->delegate->users_getInfo( $suggestion['uid'], "first_name" );
		$name = $names[0];
		$firstname = $name['first_name'];
	}catch( Exception $e ) {
		$firstname = 'Friend of Friend';
	}
	if( $topic != $lasttopic ) 
	{
		echo '<br/>';
		echo "<div>Topic: $topic</div>";
	}
	$lasttopic = $topic;
	$sid = $suggestion['sid'];
	$yes = 'black';
	$no = 'black';
	foreach($ratings as $rating)
	{
		$iid = $rating['iid'];
		if($iid == $sid)
		{
			$vote = $rating['vote'];
			if($vote == 1)
			{
				$yes = 'green';
			}else
			{
				$no = 'red';
			}
		}
	}

	$vote_string = '<a href="index.php?action=rate&vote=1&iid='.$suggestion['sid'].'"><font color="'.$yes.'">yes</font></a>|<a href="index.php?action=rate&vote=0&iid='.$suggestion['sid'].'"><font color="'.$no.'">no</font></a> ';
	
	echo '<div>'.$vote_string.$suggestion['suggestion'].' posted by '.$firstname.' at '.$suggestion['created'].'</div>';
}
?>



