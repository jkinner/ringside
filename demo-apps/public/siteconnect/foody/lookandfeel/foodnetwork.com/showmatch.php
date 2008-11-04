<?php
 /*
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
  */

/**
 * Document this file.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */

$score_description = array(
    0    =>    'Not Interested',
    1    =>    'Didn\'t like this one',
    2    =>    'Would like to try',
    3    =>    'This one was good!'
);

$match_description = array(
    0    => 'Perfect!',
    1    => 'Good',
    2    => 'Good',
    3    => 'Awful!'
);

?>
	<div id="foodtv-wrapper">
	
		<div id="title-bar">&nbsp;</div>
			<div id="content-cell">			
				<h2 class="food-mate">Your Food Mate</h2>
				
				<div id="most-compatible">
					<div id="most-compatible-view">
						<div class="user-view" style="background:transparent url() no-repeat;"><fb:profile-pic size='small' uid='<?php echo $ringside->get_loggedin_user(); ?>' /><br>You</div>
						<div id="compatible-with">You are most compatible with...</div>
						<div class="user-view float-right" style="background:transparent url(<fb:profile-pic uid='<?php echo $friend ?>'>) no-repeat;"><fb:profile-pic size="small" uid='<?php echo $friend ?>'><br><fb:name uid="<?php echo $best_friend ?>" firstnameonly="true" useyou="false"></div>
						<br class="clearfix" />
					</div>
				</div>
				
				<h3 class="your-ratings clearfix">Your Recipe Ratings:</h3>
				<div id="recipe-ratings">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<th>Recipe</th>
							<th>You</th>
							<th><fb:name uid="<?php echo $best_friend ?>" firstnameonly="true" useyou="false"></th>
							<th>Match</th>
						</tr>
<?php foreach ( $quiz_questions as $item ) {
    foreach ( $quiz->answers as $quiz_answer ) {
        if ( $quiz_answer->quiz_item == $item['id'] ) {
            $my_score = $quiz_answer->score;
        }
    }
    foreach ( $best_friend_quiz->answers as $quiz_answer ) {
        if ( $quiz_answer->quiz_item == $item['id'] ) {
            $friend_score = $quiz_answer->score;
        }
    }
    require("showmatchitem.php"); 
}
?>
					</table>
				</div><!-- end div#recipe-rating -->
				
			</div><!-- end div#content-cell -->
	</div><!-- end div#wrapper -->
