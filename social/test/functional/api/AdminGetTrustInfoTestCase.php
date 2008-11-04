<?php
 /*
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
  */
require_once('ringside/rest/AdminGetTrustInfo.php');
require_once('SuiteTestUtils.php');
require_once('BaseRestTestCase.php');

/**
 * Tests the admin_getTrustInfo API implementation.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
class AdminGetTrustInfoTestCase extends BaseRestTestCase  {
	private function cleanDb()
	{
		
		$sqlDelete = "
		DELETE FROM rs_trust_authorities WHERE trust_key like 'test%';
		";

		mysql_upload_string($sqlDelete);
	}
	
	public function setUp()
	{
		$this->cleanDb();
		$sql = "
					INSERT INTO rs_trust_authorities (trust_key, trust_name, trust_auth_url, trust_login_url)
					VALUES ('testRingside', 'Ringside Test', 'http://localhost/api/restserver.php', 'http://localhost/web/login.php');
					INSERT INTO rs_trust_authorities (trust_key, trust_name, trust_auth_url, trust_login_url)
					VALUES ('testFacebook', 'Facebook Test', 'http://api.facebook.com/restserver.php', 'http://www.facebook.com/login.php');
					INSERT INTO rs_trust_authorities (trust_key, trust_name, trust_auth_url, trust_login_url)
					VALUES ('testAuthority', 'Placeholder Test', 'http://api.test.com/restserver.php', 'http://www.test.com/login.php');
					";
		mysql_upload_string($sql);
	}

	public function tearDown()
	{
		$this->cleanDb();
	}
	
	public function testGetAllInfo() {
	    
	    $uid = 5001;
	    $params = array();
 	    $m = $this->initRest( new AdminGetTrustInfo(), $params, $uid );
		$results = $m->execute();
		$foundRingside = false;
		$foundFacebook = false;
		
		foreach ( $results['trust_auth'] as $result ) {
			if ( $result['trust_key'] == 'testRingside' ) {
				$foundRingside = true;
				$this->assertEquals('http://localhost/api/restserver.php', $result['trust_auth_url']);
				$this->assertEquals('http://localhost/web/login.php', $result['trust_login_url']);
			} else if ( $result['trust_key'] == 'testFacebook' ) {
				$foundFacebook = true;
				$this->assertEquals('http://api.facebook.com/restserver.php', $result['trust_auth_url']);
				$this->assertEquals('http://www.facebook.com/login.php', $result['trust_login_url']);
			}
		}
		$this->assertTrue($foundRingside, "Expected trust key testRingside to be found");
		$this->assertTrue($foundFacebook, "Expected trust key testFacebook to be found");
	}

	public function testGetOneInfo() {
	    $uid = 5001;
	    $params = array('tids' => 'testRingside');
 	    $m = $this->initRest( new AdminGetTrustInfo(), $params, $uid );
		$result = $m->execute();
		$this->assertEquals(1, sizeof($result['trust_auth']));
		$this->assertEquals('testRingside', $result['trust_auth'][0]['trust_key']);
		$this->assertEquals('http://localhost/api/restserver.php', $result['trust_auth'][0]['trust_auth_url']);
		$this->assertEquals('http://localhost/web/login.php', $result['trust_auth'][0]['trust_login_url']);
	}

	public function testGetTwoInfo() {
	    $uid = 5001;
	    $params = array('tids' => 'testRingside,testFacebook');
 	    $m = $this->initRest( new AdminGetTrustInfo(), $params, $uid );
		$results = $m->execute();
		$foundRingside = false;
		$foundFacebook = false;
		
		// Same as getting all, but the size should always be exactly 2.
		$this->assertEquals(2, sizeof($results['trust_auth']));
		foreach ( $results['trust_auth'] as $result ) {
			if ( $result['trust_key'] == 'testRingside' ) {
				$foundRingside = true;
				$this->assertEquals('http://localhost/api/restserver.php', $result['trust_auth_url']);
				$this->assertEquals('http://localhost/web/login.php', $result['trust_login_url']);
			} else if ( $result['trust_key'] == 'testFacebook' ) {
				$foundFacebook = true;
				$this->assertEquals('http://api.facebook.com/restserver.php', $result['trust_auth_url']);
				$this->assertEquals('http://www.facebook.com/login.php', $result['trust_login_url']);
			}
		}
		$this->assertTrue($foundRingside, "Expected trust key testRingside to be found");
		$this->assertTrue($foundFacebook, "Expected trust key testFacebook to be found");
	}
}
?>