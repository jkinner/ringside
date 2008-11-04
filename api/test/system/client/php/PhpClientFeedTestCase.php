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

require_once("BasePhpClientTestCase.php");


class PhpClientFeedTestCase extends BasePhpClientTestCase
{

    public function testFeedPublishStoryToUser()
	{
		$res = $this->fbClient->feed_publishStoryToUser("newsflash!", "this is a feed.");		
		$this->assertEquals($res, 1);

		// Goint to validate against DB, need to think about a better way for this (whats built into phpunit)?
		include ('LocalSettings.php' );
		$dbCon = mysql_connect( $db_server, $db_username, $db_password );
		$this->assertTrue( $dbCon !== false , "Could not connect to database $db_server:$db_username ");
		$selectDb = mysql_select_db( $db_name , $dbCon );
		$this->assertTrue( $selectDb !== false , "Could not connect to database $db_name ");
		
		$sql = "SELECT * FROM feed WHERE author_id=17001 and title='newsflash!'";
		if (!($ds = mysql_query($sql, $dbCon))) {
			$this->assertTrue(false, "Couldn't execute query: " . mysql_error() . "\nsql='$sql'");	
		}
		
		$row = mysql_fetch_assoc($ds);		
		$this->assertEquals($row["title"], "newsflash!");
		$this->assertEquals($row["body"], "this is a feed.");
	}
	
	public function testFeedPublishActionOfUser()
	{
		$res = $this->fbClient->feed_publishActionOfUser("OMG!", "some user did something!");		
		$this->assertEquals($res, 1);
		
		include ('LocalSettings.php' );
		$dbCon = mysql_connect( $db_server, $db_username, $db_password );
		$this->assertTrue( $dbCon !== false , "Could not connect to database $db_server:$db_username ");
		$selectDb = mysql_select_db( $db_name , $dbCon );
		$this->assertTrue( $selectDb !== false , "Could not connect to database $db_name ");
				
		$sql = "SELECT * FROM feed WHERE author_id=17001 and title='OMG!'";
		if (!($ds = mysql_query($sql, $dbCon))) {
			$this->assertTrue(false, "Couldn't execute query: " . mysql_error() . "\nsql='$sql'");	
		}
		
		$row = mysql_fetch_assoc($ds);
		$this->assertEquals(mysql_num_rows($ds), 1);
		$this->assertEquals($row["title"], "OMG!");
		$this->assertEquals($row["body"], "some user did something!");
	}
	
	public function testFeedPublishTemplatizedAction()
	{
		//TODO: some problem with response to this method,
		//there's a space before <?xml version="1.0" in the
		//response that shouldn't be there
		/*		
		$this->fbClient->printResponse = true;
		//TODO: check results
		$res = $this->fbClient->feed_publishTemplatizedAction("17002", "Template {var1} story from {actor}",
																				"{\"var1\":\"val1\"}",
																				"This is my story, and var2={var2}",
																				"{\"var2\":\"val2\"}",
																				"Here is some general data?");				
		//$this->assertEquals($res, 1);		
		$this->fbClient->printResponse = false;
		*/
	}
	
}

?>
