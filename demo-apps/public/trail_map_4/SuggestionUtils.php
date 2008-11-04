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

class SuggestionUtils
{
	/**
	 * Creates a database connection and returns it.
	 *
	 * NOTE: php will pool connections, so no need for us to attempt that here.
	 *
	 * @return mysql resource
	 */
	static private function get_db_conn()
	{
		//SuggestionUtils::connection =
		$link = mysql_connect( Config::$db_ip, Config::$db_user, Config::$db_pass );
		$db = mysql_select_db( Config::$db_name, $link );
		if($db)
		{
			return $link;
		}
		return false;
	}

	/**
	 * Saves a suggestion
	 *
	 * @param int $tid Topic ID
	 * @param string $suggestion The Suggestion string to save
	 * @param int $uid User ID to create suggestion as
	 * @return bool
	 */
	static public function createSuggestion( $topic, $suggestion, $uid, $owneruid )
	{
		return mysql_query("INSERT INTO suggestions SET topic='$topic', uid=$uid, owneruid=$owneruid, suggestion='$suggestion', api_key='".config::$api_key."'",SuggestionUtils::get_db_conn() );
	}

	/**
	 * Returns suggestions for topics from this user or their friends
	 *
	 * @param int $uid
	 * @param array $friends
	 * @return array
	 */
	static public function getSuggestions(array $friends) 
	{
		if(!isset( $friends ))
		{
			print( 'UID ($uid) and Friends($friends) must both be set!' );
			return;
		}
		
		$friends_list = implode(',', $friends);

		$sql = "SELECT * FROM suggestions WHERE api_key ='".Config::$api_key."'";

		if(strlen($friends_list) > 0)
		{
			$sql .= " AND owneruid IN ($friends_list)";
		}
		
		$sql .= " ORDER BY topic, sid";
		
		error_log("executing sql: $sql");
		$suggestionsResults = mysql_query( $sql, SuggestionUtils::get_db_conn() ) or print( mysql_error() );
		$displaySuggestions = array();
		while( $row = mysql_fetch_assoc( $suggestionsResults ) ) 
		{
			$displaySuggestions[] = $row;
		}
		return $displaySuggestions;
	}

	/**
	 * Returns all the topics for this user
	 *
	 * @param array $uids
	 * @return array
	 */
	static public function getTopics(array $friends)
	{
		$friends_list = implode(',', $friends);
		
		$sql = "SELECT DISTINCT topic, owneruid FROM suggestions";
		
		if(strlen($friends_list) > 0)
		{
			$sql .= " WHERE owneruid IN ($friends_list)";
		}
		
		$res = mysql_query($sql, SuggestionUtils::get_db_conn());
		$ret = array();
		while ($row = mysql_fetch_assoc($res)) {
			$ret[] = $row;
		}
		return $ret;
	}
}
?>
