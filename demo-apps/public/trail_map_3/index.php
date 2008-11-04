<?php

// this defines some of your basic setup
include_once 'config.php';

// instantiate a new ringside client
$ringsideClient = new RingsideApiClients( Config::$api_key, Config::$secret );
// the require_frame method redirects the user to be inside of canvas
$ringsideClient->require_frame();

$userid = $ringsideClient->require_login();
$name = $ringsideClient->api_client->users_getInfo( $userid, "first_name" );
echo '<p>Hello, ' . $name[0]['first_name']. '!</p>';

$friends = $ringsideClient->api_client->friends_get();
?>
<body>
<a href="display.php?uid=<?php print $userid; ?>">Display Existing Topics</a>
<br /><br />
<div class="suggest_form">
	<h3>Make Suggestions</h3>
	<br />
	<form method="post" action="display.php">
		<input type="hidden" name="uid" value="<?php print $userid; ?>" />
		<div class="suggest_topic_label">New Topic:</div>
		<div class="suggest_topic_input"><input type="text" name="newtopic" size="80" maxlength="80"/></div>

<?php
// Get the list of existing topics owned by the logged in user or their friends
$utils = new SuggestionUtils();
$suggestions = $utils->getSuggestions( $userid, $friends );
$topics = array();
foreach( $suggestions as $suggestion ) {
  // dont show the owner topics
	//if( !in_array( array( $suggestion['owneruid'], $suggestion['topic'] ), $topics ) ) {
		$topics[] = array( $suggestion['owneruid'], $suggestion['topic'] );
	//}
}
// Only display options if there are existing topics in the user's social network
if( !empty( $topics ) ) {
?>
		<br />
		<div class="suggest_topic_label">Existing Topic:</div>
		<div class="suggest_topic_input">
		<select name="existingtopic">
			<option selected="true" value="none">-</option>
<?php
	foreach( $topics as $topic ) {
		echo '<option value="' . $topic[0] . ':' . $topic[1] . '">' . $topic[1] . '</option>';
	}
?>
		</select>
		</div>
<?php
}
?>
		<br />
		<div class="suggest_text_label">Suggestion:</div>
		<div class="suggest_text_input"><input type="text" name="suggestion" size="80" maxlength="80"/></div>
		<br />
		<div class="suggest_submit"><input type="submit" name="submit" value="Post"/></div>
	</form>
</div>
</body>
