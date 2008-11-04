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
include_once('ringside/api/clients/RingsideApiClients.php');
include_once('ringside/api/clients/SuggestionClient.php');
include_once 'ringside/api/clients/RingsideRestClient.php';

// Control your configuration via this config file
include_once('config.php');

// Get the ringside client
RingsideApiClientsConfig::$webUrl = Config::$ringsideWeb;
RingsideApiClientsConfig::$serverUrl = Config::$ringsideApi;
$ringside = new RingsideApiClients( Config::$api_key, Config::$secret );

//// Make sure the user is logged in and get the UID
// TODO: Probably don't need this in the app.  Let the framework figure this out
// suggestions can be anonymous
$userid = $ringside->require_login();

// Show the add and the dashboard.
if( (isset( $_REQUEST['existingtopic'] ) || isset( $_REQUEST['newtopic'] ) ) && isset( $_REQUEST['suggestion'] ) ) {
   $ownerid = 0;
   $topic = '';
   $suggestion = $_REQUEST['suggestion'];
   	
   // Topics are messed up for new topics
   if( isset( $_REQUEST['existingtopic'] ) && $_REQUEST['existingtopic'] != 'none' ) {
      $str = $_REQUEST['existingtopic'];
      // comes in the form ownerid:topic
      $pos = strpos( $str, ':' );
      if( $pos ) {
         $ownerid = substr( $str, 0, $pos );
         $topic = substr( $str, $pos + 1, strlen( $str ) );
      } else {
         throw new Exception( "Invalid existing topic syntax: $str" );
      }
    }
    else {
      $topic = $_REQUEST['newtopic'];
      $ownerid = $userid;
   }
   	
   $suggestionClient = new SuggestionClient( $ringside->api_client );
   $result = $suggestionClient->suggestion_add( $topic, $ownerid, 'yaya', $suggestion );
}
else if( isset( $_REQUEST['action'] ) && $_REQUEST['action']=='rate' )
{
	if( isset( $_REQUEST['iid'] ) & isset( $_REQUEST['vote'] ) )
	{
		$iid = $_REQUEST['iid'];
		$vote = $_REQUEST['vote'];
		
		// Using a null type, type allows you to put things in buckets
		// For instance, type could equal a bookmark.  But, ratings are app
		// specific, so since we only have 1 type we can leave it null.
		$rest = new RingsideRestClient( $ringside->api_client );
		$rest->ratings_set( $iid, $vote, null );
	}else
	{
		error_log("Rating error, bad params!");
	}
} // Default is to just show the list page

?>
<rs:suggestions showform="true" newtopics="false" existingtopics="false" topic="Great Band Names">
	<rs:ratings style="yesno"/>
</rs:suggestions>
