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

require_once('ringsideidm/rest/UsersMapPrincipal.php');
require_once('ringsideidm/rest/UsersMapSubject.php');
require_once('BaseRestTestCase.php');
require_once('SuiteTestUtils.php');

/**
 * Tests the user mapping API.
 *
 * @author Jason Kinner <jkinner@ringsidenetworks.com>
 */
class UsersMapTestCase extends BaseRestTestCase {
    private function resetDb() {
        mysql_upload_string('delete from principal_map where principal_id > 50000 and principal_id < 51000;
									delete from principal where id > 50000 and id < 51000;
									delete from app where id = 1234;');
    }

    public function setUp() {
        $this->resetDb();
        mysql_upload_string("insert into app (id, name, canvas_url, sidenav_url) values (1234, 'Test app', 'test_app', 'http://example.com/test_app');
									insert into principal (id) values (50001);
									insert into principal_map (principal_id, app_id, uid, network_id)
									values (50001, 1234, 100001, '1234');
									insert into principal (id) values (50002);
									insert into principal_map (principal_id, app_id, uid, network_id)
									values (50002, 1234, 100002, '1234');");
    }

    public function tearDown() {
        $this->resetDb();
    }

    public static function mapToPrincipalProvider() {
        return array(
        /* caller uid */	/* params */ 								/* app_id */	/* nid */	/* expected */
        array(	100001, 				array(), 									1234, 			1234,			array(array('uid' => 100001, 'pid' => 50001))),
        array(	100002,				array('uids' => 100001),				1234,				1234,			array(array('uid' => 100001, 'pid' => 50001))),
        array(	100002,				array('uids' => '100001,100002'),	1234,				1234,			array(array('uid' => 100001, 'pid' => 50001), array('uid' => 100002, 'pid' => 50002))),
        array(	100002,				array('uids' => '100001,200002'),	1234,				1234,			array(array('uid' => 100001, 'pid' => 50001))),
        array(	100001,				array('uids' => '100001,100002'),	1234,				null,			array(array('uid' => 100001, 'pid' => 100001), array('uid' => 100002, 'pid' => 100002))),
        );
    }

    /**
     * @dataProvider mapToPrincipalProvider
     *
     * @param unknown_type $caller_uid
     * @param unknown_type $params
     * @param unknown_type $app_id
     * @param unknown_type $nid
     * @param unknown_type $expected
     */
    public function testMapToPrincipal($caller_uid, $params, $app_id, $nid, $expected_maps) {
        $mapApi = $this->initRest(new UsersMapPrincipal(), $params, $caller_uid, $app_id, $nid, array());
        $result = $mapApi->execute();
        $this->assertEquals(sizeof($expected_maps), sizeof($result['idmap']));
        foreach ( $expected_maps as $expected_map ) {
            $success = false;
            foreach ( $result['idmap'] as $idmap ) {
                if ( $idmap['uid'] == $expected_map['uid'] && $idmap['pid'] == $expected_map['pid'] ) {
                    $success = true;
                    break;
                }
            }
            $this->assertTrue($success, "Expected to find uid {$expected_map['uid']} mapped to {$expected_map['pid']} in results: ".var_export($result['idmap'], true));
        }
    }

    public static function mapToSubjectProvider() {
        return array(
        /* caller uid */			/* params */ 							/* app_id */	/* nid */	/* expected */
        array(	100002,				array('pids' => 50001),				1234,				1234,			array( array('pid' => 50001, 'uid' => 100001 ) ) ),
        array(	100002, 				array('pids' => '50001,50002'), 	1234, 			1234,			array( array('pid' => 50001, 'uid' => 100001), array('pid' => 50002, 'uid' => 100002 ) ) ),
        array(	100002, 				array('pids' => '50001,60002'), 	1234, 			1234,			array( array('pid' => 50001, 'uid' => 100001 ) ) ),
        array(	50001,				array('pids' => '50001,50002'),	1234,				null,			array( array('pid' => 50001, 'uid' => 50001), array( 'pid' => 50002, 'uid' => 50002))),
        );
    }

    /**
     * @dataProvider mapToSubjectProvider
     *
     * @param unknown_type $caller_uid
     * @param unknown_type $params
     * @param unknown_type $app_id
     * @param unknown_type $nid
     * @param unknown_type $expected
     */
    public function testMapToSubject($caller_uid, $params, $app_id, $nid, $expected_maps) {
        $mapApi = $this->initRest(new UsersMapSubject(), $params, $caller_uid, $app_id, $nid, array());
        $result = $mapApi->execute();
        $this->assertEquals(sizeof($expected_maps), sizeof($result['idmap']));
        foreach ( $expected_maps as $expected_map ) {
            $success = false;
            foreach ( $result['idmap'] as $idmap ) {
                if ( $idmap['uid'] == $expected_map['uid'] && $idmap['pid'] == $expected_map['pid'] ) {
                    $success = true;
                    break;
                }
            }
            $this->assertTrue($success, "Expected to find uid {$expected_map['uid']} mapped to {$expected_map['pid']} in results: ".var_export($result['idmap'], true));
        }
    }
}
?>