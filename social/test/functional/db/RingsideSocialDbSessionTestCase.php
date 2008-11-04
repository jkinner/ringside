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

require_once('SuiteTestUtils.php');
require_once('ringside/social/db/RingsideSocialDbSession.php');

class RingsideSocialDbSessionTestCase extends PHPUnit_Framework_TestCase
{
	private function cleanDb()
	{
		$sqlDelete = "
		DELETE FROM rs_social_session_history;
		";

		mysql_upload_string($sqlDelete);
	}
	protected function setUp()
	{
		$this->cleanDb();
	}

	protected function tearDown()
	{
		$this->cleanDb();
	}

	//$pid, $uid, $userName, $network, $sessionKey, $trust_id, $dbCon
	public function testLogSessionHistory()
	{
		$dbCon = getDbCon();
		$pid="1";
		$uid="1234";
		$userName="test@ringside";
		$network="ringside";
		$sessionKey="mysessionkey";
		$trust_key = "ringside";
		RingsideSocialDbSession::logSessionHistory($pid, $uid, $userName, $network, $sessionKey, $trust_key);

		$sql = "SELECT * FROM rs_social_session_history WHERE trust_key='$trust_key' AND social_session_key='$sessionKey'
			AND principal_id=$pid AND uid=$uid AND user_name='$userName' AND network_key='$network'";
			
		$result = mysql_query( $sql, $dbCon );
		if($result)
		{
			$count = mysql_num_rows($result);
			$this->assertEquals($count, 1, "Expected 1 row, got $count rows returned!" );
		}else
		{
			$this->assertTrue(false, "Session not logged!");
		}

	}

	//trust_key, trust_name, trust_auth_class, trust_auth_url
	public function testGetAuthTokenApprovalClass()
	{
		$dbCon = getDbCon();
		$trust_key = "Ringside234534253245235325";
		$result = RingsideSocialDbSession::getTrustAuthority($trust_key);
		$this->assertNull($result);

		$sql = "INSERT INTO rs_trust_authorities (trust_key, trust_name, trust_auth_url, trust_auth_class) VALUES ('Ringside', 'Ringside Local', 'localhost/api/restserver.php', '')";

		mysql_query( $sql, $dbCon );

		$trust_key = "Ringside";
		$result = RingsideSocialDbSession::getTrustAuthority($trust_key);
		$this->assertEquals($result['trust_key'], 'Ringside');
		$this->assertEquals($result['trust_name'], 'Ringside Local');
		$this->assertEquals($result['trust_auth_url'], 'localhost/api/restserver.php');
		$this->assertEquals($result['trust_auth_class'], '');
	}
}

?>
