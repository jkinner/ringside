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
require_once( 'ringside/rest/AdminGetUserMaps.php');
require_once( 'BaseRestTestCase.php' );
require_once( 'SuiteTestUtils.php');

/**
 * Document this file.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
class AdminGetUserMapsTestCase extends BaseRestTestCase
{
    private function resetDb() {
        mysql_upload_string("
									delete from principal_map where principal_id > 50000 and principal_id < 51000;
									delete from principal where id > 50000 and id < 51000;
									delete from users where id > 200000 and id < 201000;
									DELETE FROM rs_trust_authorities WHERE trust_key like 'test%';
									delete from app where id = 1234;
									");
    }

    public function setUp() {
        $this->resetDb();

        // These are the principals we will map
        mysql_upload_string("
							insert into app (id, name, canvas_url, sidenav_url) values (1234, 'Test app', 'test_app', 'http://example.com/test_app');
							INSERT INTO rs_trust_authorities (trust_key, trust_name, trust_auth_url, trust_login_url)
					VALUES ('testRingside', 'Ringside Test', 'http://localhost/api/restserver.php', 'http://localhost/web/login.php');
					INSERT INTO rs_trust_authorities (trust_key, trust_name, trust_auth_url, trust_login_url)
					VALUES ('testFacebook', 'Facebook Test', 'http://api.facebook.com/restserver.php', 'http://www.facebook.com/login.php');
									insert into principal (id) values (50002);
									insert into principal_map (principal_id, app_id, uid, network_id)
									values (50002, 1234, 100002, 'testFacebook');
									insert into principal_map (principal_id, app_id, uid, network_id)
									values (50002, 1234, 200002, 'testRingside');");
    }

    public function tearDown() {
        $this->resetDb();
    }

    private function _checkSubjects(array $subjects)
    {
        $foundFacebook = false;
        $foundRingside = false;
        foreach ( $subjects as $subject )
        {
            if ( $subject['nid'] == 'testFacebook' && $subject['uid'] == 100002 )
            {
                $foundFacebook = true;
            }
            if ( $subject['nid'] == 'testRingside' && $subject['uid'] == 200002 )
            {
                $foundRingside = true;
            }
        }
        
        $this->assertTrue($foundFacebook, 'Did not find subject for Facebook');
        $this->assertTrue($foundRingside, 'Did not find subject for Ringside');
    }
        
    public function testMapFromPrincipal()
    {
        $m = new AdminGetUserMaps();
        $this->initRest($m, array('pid' => 50002, 'aid' => 1234));
        $r = $m->execute();
        $this->assertArrayHasKey('subject', $r);
        $subjects = $r['subject'];
        $this->assertEquals(2, sizeof($subjects));
        $this->_checkSubjects($subjects);
    }

    public function testMapFromSubject()
    {
        $m = new AdminGetUserMaps();
        $this->initRest($m, array('aid' => 1234, 'nid' => 'testFacebook', 'uid' => 100002));
        $r = $m->execute();
        $this->assertArrayHasKey('subject', $r);
        $subjects = $r['subject'];
        $this->assertEquals(2, sizeof($subjects));
        $this->_checkSubjects($subjects);
        
        $this->initRest($m, array('aid' => 1234, 'nid' => 'testRingside', 'uid' => 200002));
        $r = $m->execute();
        $this->assertArrayHasKey('subject', $r);
        $subjects = $r['subject'];
        $this->assertEquals(2, sizeof($subjects));
        $this->_checkSubjects($subjects);
    }
    
}
?>