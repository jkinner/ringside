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

$friends = $ringside->api_client->friends_get();

$friend_infos = $ringside->api_client->users_getInfo($friends, array('first_name', 'last_name'));

$matches[] = array();
$i = 0;
$best_friend_quiz = null;
$best_friend_score = 0;
$best_friend = 0;
foreach ( $friends as $friend ) {
    $friend_quiz = Doctrine::getTable('Quiz')->findOneByUser($_REQUEST['fb_sig_nid'], $friend);
    if ( $friend_quiz != null ) {
        $score_difference = 0;
        foreach ( $friend_quiz->answers as $friend_answer ) {
            $my_score = 0;
            foreach ( $quiz->answers as $answer ) {
                if ( $answer->quiz_item == $friend_answer->quiz_item ) {
                    $my_score = $answer->score;
                }
            }
            $score_difference += abs($my_score - $friend_answer->score);
        }
        if ( $best_friend_quiz == null || $best_friend_score > $score_difference ) {
            $best_friend_quiz = $friend_quiz;
            $best_friend = $friend;
        }
    }
}

include("lookandfeel/$lookandfeel/showmatch.php");
?>
