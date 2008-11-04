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

class SuggestionAdd extends Api_DefaultRest
{

	/**
	 * String representing the topic of discussion.
	 */
	protected $m_topic;
	
	/**
	 * The UID of the user who created the topic.
	 */
	protected $m_owner;
	
	/**
	 * The actual suggestion. 
	 */
	protected $m_suggestion;
	
	/**
    * The API key of the application containing the topic.
    */
	protected $m_appApiKey;
	

	public function validateRequest( )
	{
		
		$this->m_topic = $this->getRequiredApiParam( 'topic' );
		$this->m_owner = $this->getRequiredApiParam( 'owner' );
		$this->m_suggestion = $this->getRequiredApiParam( 'suggestion' );
		$this->m_appApiKey = $this->getRequiredApiParam( 'app_api_key' );
	}
	
	public function execute()
	{
		//construct insertion SQL
		$sql = 'INSERT INTO suggestions SET ' .
    				 "topic='{$this->m_topic}'," .
    				 'uid=' . $this->getUserId() .
    				 ", suggestion='{$this->m_suggestion}', " .
    				 "api_key='{$this->m_appApiKey}', " .
    				 "owneruid={$this->m_owner}";
		
		//get DB connection
		if (!($db = $this->getDbCon())) {
			throw new Exception('Could not connect to DB: ' . mysql_error());
		}
		//execute query
		if (!mysql_query($sql, $db)) {
			throw new Exception('Error inserting into DB: ' . mysql_error() . "\nSQL='$sql'");
		} 
		
		//if made it this far, then return successfully
		return true;	
	}
}

?>