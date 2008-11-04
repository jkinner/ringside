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

require_once('config.php');
require_once('ringside/api/clients/RingsideApiClients.php');
require_once('ringside/api/clients/SuggestionClient.php');

class SuggestionUtils
{
	static private function getSuggestionClient()
	{
	   	$client = new RingsideApiClients( Config::$api_key, Config::$secret );
	   	$uid = $client->require_login();
	   	
	   	$delegate = $client->api_client; 
		$obj = new ReflectionObject( $delegate );
	   	$suggestionClient = new SuggestionClient( $delegate );
	   	
	   	return $suggestionClient;
	}

	/**
	 * Saves a suggestion
	 *
	 * @param string $suggestion The Suggestion string to save
	 * @param int $uid User ID to create suggestion as
	 * @return bool
	 */
	static public function createSuggestion($topic, $suggestion, $owner)
	{
		$client = self::getSuggestionClient();
		$client->suggestion_add($topic, $owner, Config::$api_key, $suggestion);
	}

	/**
	 * Returns suggestions for topics from this user or their friends
	 *
	 * @param int $uid
	 * @param array $friends
	 * @return array
	 */
	static public function getSuggestions() 
	{
		$client = self::getSuggestionClient();
		$friends = $client->delegate->friends_get();
		
		$resp = $client->suggestion_get(Config::$api_key, $friends);
		
		if (!$resp)  return array();
		return $resp;
	}

	/**
	 * Returns all the topics for this user
	 *
	 * @return array
	 */
	static public function getTopics()
	{
		$client = self::getSuggestionClient();		
		$friends = $client->delegate->friends_get();
		$resp = $client->suggestion_getTopics($friends);
		if (!$resp) return array(); 
		return $resp;
	}
}
?>
