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
require_once('SuiteTestUtils.php');
require_once('ringside/social/db/RingsideSocialDbTrust.php');

/**
 * Tests trust authority database operations.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
class RingsideSocialDbTrustTestCase extends PHPUnit_Framework_TestCase {
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
					";
		mysql_upload_string($sql);
	}

	public function tearDown()
	{
		$this->cleanDb();
	}
	
	public function testGetAllTrustAuthorities() {
		$results = RingsideSocialDbTrust::getTrustAuthorities();
		$foundTestRingside = false;
		$foundTestFacebook = false;
		
		foreach ( $results as $result ) {
			if ( $result['trust_key'] == 'testRingside' ) {
				$foundTestRingside = true;
				$this->assertEquals($result['trust_auth_url'], 'http://localhost/api/restserver.php');
				$this->assertEquals($result['trust_login_url'], 'http://localhost/web/login.php');
			}

			if ( $result['trust_key'] == 'testFacebook' ) {
				$foundTestFacebook = true;
				$this->assertEquals($result['trust_auth_url'], 'http://api.facebook.com/restserver.php');
				$this->assertEquals($result['trust_login_url'], 'http://www.facebook.com/login.php');
			}
			
		}
		$this->assertTrue($foundTestRingside, "Expected trust authority testRingside to be found");
		$this->assertTrue($foundTestFacebook, "Expected trust authority testRingside to be found");
	}

	public function testGetOneTrustAuthority() {
	    $trust = 'testRingside';
		$results = RingsideSocialDbTrust::getTrustAuthorities(array( $trust ));
		$this->assertEquals(1, sizeof($results));
		$this->assertArrayHasKey( $trust , $results );		
		$this->assertEquals($results[$trust]['trust_auth_url'], 'http://localhost/api/restserver.php');
		$this->assertEquals($results[$trust]['trust_login_url'], 'http://localhost/web/login.php');
	}
}
?>