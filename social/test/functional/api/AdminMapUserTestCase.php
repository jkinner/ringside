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

require_once( 'ringside/rest/AdminMapUser.php');
require_once( 'ringsideidm/rest/UsersMapPrincipal.php');
require_once( 'ringsideidm/rest/UsersMapSubject.php');
require_once( 'BaseRestTestCase.php' );
require_once( 'SuiteTestUtils.php');

/**
 * Tests the API implementation for admin_mapUser.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
class AdminMapUserTestCase extends BaseRestTestCase {
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
									");
    }

    public function tearDown() {
        $this->resetDb();
    }

    public static function mappingProvider() {
        return array(
        			/* uid */	/* snid */			/* sid */	/* nid */			/* app_id */
        //			array(	'100001',	'testRingside',	'200001',	'testFacebook',	1234			),
        array(	'100001',	'testRingside',	'200001',	'testFacebook',	1234			)
        );
    }

    /**
     * @dataProvider mappingProvider
     *
     * @param unknown_type $uid
     * @param unknown_type $snid
     * @param unknown_type $sid
     * @param unknown_type $nid
     * @param unknown_type $app_id
     */
    public function testMap($uid, $snid, $sid, $nid, $app_id) {
        $f = $this->initRest(new AdminMapUser(), array('snid' => $snid, 'sid' => $sid, 'uid' => $uid), $uid, $app_id, $nid );
        $result = $f->execute();

        $m = $this->initRest(new UsersMapPrincipal(), array('uids' => $sid), $uid, $app_id, $snid);
        $result = $m->execute();
        $this->assertArrayHasKey('idmap', $result);
        $this->assertEquals($sid, $result['idmap'][0]['uid']);
        $pid = $result['idmap'][0]['pid'];

        $m = $this->initRest(new UsersMapSubject(), array('pids' => $pid, 'nid' => $nid), $uid, $app_id, $nid );
        $result = $m->execute();
        $this->assertArrayHasKey('idmap', $result);
        $this->assertEquals($pid, $result['idmap'][0]['pid']);
        $this->assertEquals($uid, $result['idmap'][0]['uid']);

        // Confirm that the same principal maps to the subject ID on the subject network
        $m = $this->initRest(new UsersMapSubject(), array('pids' => $pid, 'nid' => $snid), $uid, $app_id, $nid );
        $result = $m->execute();
        $this->assertArrayHasKey('idmap', $result);
        $this->assertEquals($pid, $result['idmap'][0]['pid']);
        $this->assertEquals($sid, $result['idmap'][0]['uid']);
    }
}
?>