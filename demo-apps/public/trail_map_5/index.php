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

// Include the ringside client libraries
require_once('config.php');
require_once('SuggestionUtils.php');
include_once('ringside/api/clients/RingsideApiClients.php');
include_once('ringside/api/clients/RingsideRestClient.php');

// Get the ringside client
RingsideApiClientsConfig::$serverUrl = Config::$ringsideApi;
RingsideApiClientsConfig::$webUrl = Config::$ringsideWeb;
$ringsideClient = new RingsideApiClients( Config::$api_key, Config::$secret );
// Make sure the user is logged in and get the UID
$userid = $ringsideClient->require_login();

// Instantiate the rest client, this is for the ratings api in extension apps
$rest = new RingsideRestClient($ringsideClient->api_client);
$name = $rest->delegate->users_getInfo($userid, "first_name");

// Show the dashboard
?>
<fb:dashboard>
	<fb:action href="index.php?action=list">View Suggestions</fb:action>
	<fb:create-button href="index.php?action=add">Create a Suggestion</fb:create-button>
</fb:dashboard>
<?php
// Say hi to the user, but let's use an fbml tag, just as another example of using fbml
// instead of html
echo '<fb:explanation><fb:message>Suggestions Demo Application</fb:message>Welcome '.$name[0]['first_name'].'</fb:explanation>';

// Control what template we load
// Templates are just php files with fbml/html in them.  We use php so we can perform variable substitution
// loop through data, etc...  Normal template language stuff without the template language!

if(isset($_REQUEST['action']))
{
	$action = $_REQUEST['action'];
	error_log("Action=$action");

	// Add == show the add page
	if($action == 'add')
	{
		$uids = $rest->delegate->friends_get();
		
		$topics = SuggestionUtils::getTopics($uids);
		include_once('add.php');
	} // add_suggestion comes from the add.php form, so add the suggestion, set profile, then show list
	else if($action == 'add_suggestion')
	{
		if((isset($_REQUEST['existingtopic']) || isset($_REQUEST['newtopic'])) && isset($_REQUEST['suggestion']))
		{
			$ownerid = 0;
			$topic = '';
			$suggestion = $_REQUEST['suggestion'];
			
			// Topics are messed up for new topics
			if(isset($_REQUEST['existingtopic']) && $_REQUEST['existingtopic'] != 'none') {
				$str = $_REQUEST['existingtopic'];
				// comes in the form ownerid:topic
				$pos = strpos($str, ':');
				if($pos) {
					$ownerid = substr($str, 0, $pos);
					$topic = substr($str, $pos + 1, strlen($str));
				} else {
					throw new Exception("Invalid existing topic syntax: $str");
				}
			} else {
				$topic = $_REQUEST['newtopic'];
				$ownerid = $userid;
			}
			SuggestionUtils::createSuggestion($topic, $suggestion, $ownerid);
		}
		showSuggestions();
	} // rate == rate an item then show the list page
	else if($action=='rate')
	{
		if(isset($_REQUEST['iid']) & isset($_REQUEST['vote']))
		{
			$iid = $_REQUEST['iid'];
			$vote = $_REQUEST['vote'];
			
			// Using a null type, type allows you to put things in buckets
			// For instance, type could equal a bookmark.  But, ratings are app
			// specific, so since we only have 1 type we can leave it null.
			$rest->ratings_set($iid, $vote, null);
			showSuggestions();
		}else
		{
			error_log("Rating error, bad params!");
		}
	} // Default is to just show the list page
	else
	{
		error_log("Unknown Action: $action");
		showSuggestions();
	}
}// No action means show the list page
else
{
	showSuggestions();
}

/**
 * Queries for a list of suggestions and then includes the list page
 *
 * @param int $topic Topic ID, optional parameter.
 */
function showSuggestions()
{
	global $rest;
	$suggestions = SuggestionUtils::getSuggestions();	 
	if (!$suggestions || (count($suggestions) < 1)) {	
		$suggestions = array();
	}
	
	$iids = array();
   foreach ($suggestions as $suggestion) {		
   	$iids[] = $suggestion['sid'];
      $rest->items_setInfo($suggestion['sid'], 'trail5', 'trail5', null);
   }
    	
	//Now get ratings    
   $ratings = array();
   if (count($iids) > 0) {
   	$ratings = $rest->ratings_get($iids);
   	if (!$ratings) $ratings = array();
   }
   include_once('list.php');
}

?>

