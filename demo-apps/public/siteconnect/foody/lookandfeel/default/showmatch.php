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

$difference_description = array(
0    => '<span style="font-size: 18px; color: #75ae29">Perfect!</span>',
1    => '<span style="font-size: 18px;">Good</span>',
2    => '<span style="font-size: 18px;">Good</span>',
3    => '<span style="font-size: 18px; color: #ee701e">Awful!</span>'
);

?>
<h1>Welcome to Foody, <fb:name uid="loggedinuser" useyou="false" firstnameonly="true">!</h1>
<h2>Find your Food Compatibility Quotient</h2>
<?php
if ( $best_friend_quiz != null ) {
    echo '<div style="width: 100%; text-align: center;">Your Food Mate is:<br><span style="font-size: 28px; font-weight: bold">'.$friend_infos[$i]['first_name']." ".$friend_infos[$i]['last_name']."</span></div><br />\n";
    foreach ( $best_friend_quiz->answers as $friend_answer ) {
        $my_score = 0;
        foreach ( $quiz->answers as $answer ) {
            if ( $answer->quiz_item == $friend_answer->quiz_item ) {
                $my_score = $answer->score;
            }
        }
        $score_difference = abs($my_score - $friend_answer->score);
        $our_difference = $difference_description[$score_difference];
        render_quiz_question_row($quiz_questions[intval($friend_answer->quiz_item)-1], <<<EOF
        <div style="text-align: center; width: 80px; font-size: 18px;">{$friend_answer->score}<br><span style="font-size: 10px">(Their score)</span></div></td>
        <td><div style="text-align: center; width: 150px; font-size: 18px;">{$my_score}<br><span style="font-size: 10px">(My score)</span></td><td>$our_difference
EOF
        );
    }
} else {
    echo '<div style="font-size: 18px">None of your friends responded yet!</div>';
}
?>