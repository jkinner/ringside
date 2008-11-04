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

require_once('ringside/api/clients/RingsideApiClients.php');
require_once('config.php');
require_once('quiz.class.php');
require_once('quiztable.class.php');
require_once('answer.class.php');

$ringside = new RingsideApiClients($api_key, $secret);

//error_log("Request is ".var_export($_REQUEST, true));
Doctrine_Manager::getInstance()->openConnection($db_url);

$quiz = Doctrine::getTable('Quiz')->findOneByUser($_REQUEST['fb_sig_nid'], $ringside->get_loggedin_user());
if ( isset($quiz) ) {
    error_log("Quiz is $quiz");
    error_log("Serialized as ".var_export($quiz->toArray(), true));
}
if ( isset($_REQUEST['quiz_ids']) ) {
    if ( $quiz != null ) {
        $quiz->delete();
    }
    $quiz = new Quiz();
    $quiz->nid = $_REQUEST['fb_sig_nid'];
    $quiz->uid = $ringside->get_loggedin_user();
    foreach ( explode(',', $_REQUEST['quiz_ids']) as $quiz_id ) {
        error_log("For ".$quiz_id." user answered ".$_REQUEST['scale_'.$quiz_id]);
        $a = new Answer();
        $a->quiz = $quiz;
        $a->quiz_item = $quiz_id;
        $a->score = $_REQUEST['scale_'.$quiz_id];
        $quiz->answers[] = $a;
    }
    $quiz->save();
}

$base = "http://social.example.com/demo/siteconnect/foody/lookandfeel/$lookandfeel";

?>
<html>
	<head>
<?php include("lookandfeel/$lookandfeel/showheader.php") ?>
	</head>
	<body>
<?php
if ( $quiz != null ) {
    include 'matches.php';
} else {
    include "lookandfeel/$lookandfeel/showquiz.php";
}
?>
	</body>
</html>
