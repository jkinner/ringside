<?php

include_once 'config.php';

$utils = new SuggestionUtils();

// get the user's ID from POST or GET if it was POSTED
$uid = isset( $_POST['uid'] ) ? $_POST['uid'] : $_GET['uid'];

$newtopic = $_POST['newtopic'];

// if the user picked an existing topic
// grab the user_id and topic from the
// string separated with a colon, example -> "100001:SomeTopicName"
if( isset( $_POST['existingtopic'] ) && $_POST['existingtopic'] != 'none' ) {
	$strings = split( ":", $_POST['existingtopic'] );
	$owneruid = $strings[0];
	$existingtopic = $strings[1];
} 
else {
  // since it wasn't an existing topic
  // the logged in user created it so 
  // set the logged in user as the owner_id
  $owneruid = $uid;
}

  // simply ensure that the parameters necessary for
  // a suggestion were POSTed
if( $utils->hasAddParams() ) {
  // insert the suggestion into the database
	$result = $utils->suggestion_add( ( $existingtopic == null) ? $newtopic : $existingtopic, $_POST['suggestion'], $uid, $owneruid, Config::$api_key );
}
error_log(RingsideApiClientsConfig::$webUrl);
error_log(RingsideApiClientsConfig::$serverUrl);
error_log("before new client".Config::$api_key);
// create new API client
$ringsideClient = new RingsideApiClients( Config::$api_key, Config::$secret );

?>
<br />
<a href="index.php">Make Suggestions</a>
<br />
<?php

// get friends
$friends = $ringsideClient->api_client->friends_get();

// get suggestions for the current user and their friends
$suggestions = $utils->getSuggestions( $uid, $friends );
error_log("suggestions on next line");
error_log(var_export($suggestions,true));

// instantiate the lasttopic var and set it to something random
// so the HTML list will iterate appropriately
$lasttopic = '242455_bogus_adfasdf##!!$';

$count = 0;
// iterate through the suggestions array
foreach( $suggestions as $suggestion ) {
	$topic = $suggestion['topic'];
	try {
		$names = $ringsideClient->api_client->users_getInfo( $suggestion['uid'], "first_name" );
		$name = $names[0];
		$firstname = $name['first_name'];
	}
	catch( Exception $e ) {
		$firstname = 'Friend of Friend';
	}
?>
<?php
  // end the previous HTML table if a different topic and not the first topic in the array
	if( $topic != $lasttopic ) {
		if( $count != 0 ) {
		print("</table>\n");
	}
?>
<br />
<table border="0" width="500px" cellpadding="5">
<tr>
	<td width="500px" colspan="2" bgcolor="#f7931d">Topic: <?php print $topic; ?></td>
</tr>
<?php
	}
	$lasttopic = $topic;
?>

<tr><td width="35%" bgcolor="#fdc589"><?php print $suggestion['suggestion']; ?></td><td width="65%" bgcolor="#eeeeee">posted by <?php print $firstname; ?> at <?php print $suggestion['created']; ?></td></tr>

<?php
	$count++;
}
?>
