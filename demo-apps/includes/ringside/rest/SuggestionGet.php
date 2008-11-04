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

require_once( "ringside/api/DefaultRest.php" );

class SuggestionGet extends Api_DefaultRest
{
	/**
    * The API key of the application containing the topic.
    */
	protected $m_appApiKey;
	
	/**
	 * Array of friend IDs to query topics from.
	 */
	protected $m_friends;
	
	function beginsWith( $str, $sub ) {
		return ( substr( $str, 0, strlen( $sub ) ) === $sub );
	}
	function endsWith( $str, $sub ) {
	   return ( substr( $str, strlen( $str ) - strlen( $sub ) ) === $sub );
	}


	public function validateRequest( )
	{

		$this->m_friends = $this->getRequiredApiParam( 'friends' );
		$this->m_appApiKey = $this->getRequiredApiParam( 'app_api_key' );
		
		if($this->endsWith($this->m_friends, ','))
		{
			$this->m_friends = substr($this->m_friends, 0, strlen($this->m_friends) - 1);
		}
	}
	
	public function execute()
	{
		//get DB connection
		if (!($db = $this->getDbCon())) {
			throw new Exception('Could not connect to DB: ' . mysql_error());
		}
		
		//construct query
		$sql = "SELECT * FROM suggestions WHERE api_key='{$this->m_appApiKey}' " .
				 " AND owneruid IN ({$this->getUserId()}";
		if (strlen($this->m_friends) > 0) {
			$sql .= ",$this->m_friends";
		}
		$sql .= ') ORDER BY topic, sid';

		//execute query
		$results = mysql_query( $sql, $db);
		if ( mysql_errno($db) > 0  && !$results) {
			throw new Exception('Could not execute query: ' . mysql_error() . "\nSQL='$sql'");
		}

		$suggestions = array();
  		//create response
  		while($row = mysql_fetch_assoc($results)) {
   			$suggestions[] = $row;
  		}
  		
  		$response = array();
  		if (!empty( $suggestions ) ) { 
     		$response['suggestions'] = $suggestions;
  		} 
  		else {
     		$response['result'] = '';
 		}
 		return $response;
	}
}

?>