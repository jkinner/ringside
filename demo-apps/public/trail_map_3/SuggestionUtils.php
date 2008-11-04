<?php


class SuggestionUtils {
	
	private $ringsideClient;
	
	/**
	* create, connect and return that mysql connection
	* 
	*/
	public static function get_db_conn() {
		$link = mysql_connect( Config::$db_ip, Config::$db_user, Config::$db_pass );
		$db = mysql_select_db( Config::$db_name, $link );
		if( $db ) {
			return $link;
		}
		return false;
	}
	
	/**
	* insert the suggestion into the database
	* 
	* @param string $topic
	* @param string $suggestion
	* @param string $uid
	* @param string $owneruid
	* @param string @api_key
	* @return mysql_resource
	*/
	function suggestion_add( $topic, $suggestion, $uid, $owneruid, $api_key ) {
		$sql = 'INSERT INTO suggestions SET topic=\'' . $topic . '\', uid=' . $uid . ', suggestion=\'' . $suggestion . '\', api_key=\'' . $api_key . '\', `owneruid` = \'' . $owneruid . '\'';
		return mysql_query( $sql, $this->get_db_conn() ) or print( mysql_error() );
	}
	
  /**
  * simply ensure that the parameters necessary for
  * a suggestion were POSTed
  * 
	*/
	function hasAddParams() {
		return ( ( isset( $_POST['newtopic'] ) || isset( $_POST['existingtopic'] ) ) && isset( $_POST['suggestion'] ) );
	}
	

	/**
	 * Returns suggestions for topics from this user or their friends
	 *
	 * @param int $uid
	 * @param array $friends
	 * @return array
	 */
	static public function getSuggestions( $uid, array $friends ) 
	{
		if(!isset( $uid ) && !isset( $friends ))
		{
			print( "UID ($uid) and Friends($friends) must both be set!" );
			return;
		}

		$friends[] = $uid;
		$friends_list = implode( ',', $friends );

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
}
?>
