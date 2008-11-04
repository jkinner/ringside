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

?>
<h1>Welcome to Foody, <fb:name uid="loggedinuser" useyou="false" firstnameonly="true">!</h1>
<h2>Find your Food Compatibility Quotient</h2>
<h3>Let's get Started</h3>
<p>
To start, simply look at the list of foods below and for each one, tell us if you would try it, or, if you have already
had that recipe or something like it, whether you liked it or not. Then, we'll be able to tell you which of your
friends you should be trading recipes with!
</p>
<div id="quiz">
	<form action="" method="POST">
	<table>
		<tbody>
<?php
$quiz_ids = array();
foreach ( $quiz_questions as $quiz_question ) {
    $quiz_ids[] = $quiz_question['id'];
    $lastColumn = <<<EOF
    <div style="width: 80px; text-align: center; font-size: 10px"><input type="radio" name="scale_{$quiz_question['id']}" value="0" /><br>Not interested</div></td>
    <td><div style="width: 80px; text-align: center; font-size: 10px"><input type="radio" name="scale_{$quiz_question['id']}" value="1" /><br>Didn't like it</div></td>
    <td><div style="width: 80px; text-align: center; font-size: 10px"><input type="radio" name="scale_{$quiz_question['id']}" value="2" /><br>Would try it</div></td>
    <td><div style="width: 80px; text-align: center; font-size: 10px"><input type="radio" name="scale_{$quiz_question['id']}" value="3" /><br>Loved it!</div>
    </div>
EOF;
    render_quiz_question_row($quiz_question, $lastColumn);
}
?>
		</tbody>
	</table>
	<input type="hidden" name="quiz_ids" value="<?php echo join(',', $quiz_ids); ?>" />
	<input type="submit" name="submit" value="Finish Quiz &gt;" />
	</form>
</div>