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

require_once( 'BaseApiUnitTestCase.php' );
require_once( "ringside/rest/FeedGet.php" );

class FeedGetUnitTestCase extends BaseApiUnitTestCase {

	public static function providerTestConstructor() {
		return array(
    		array( 16010, array( 'uid'=>16010 ), true ),
            array( 16010, array( 'actorid'=>16200 ), true ),
    		array( 16010, array( 'uid'=>16010, 'friends'=>false ), true ),
            array( 16010, array( 'uid'=>16010, 'friends'=>false, 'actions'=>false ), true ),
            array( 16010, array( 'uid'=>16010, 'friends'=>false, 'stories'=>false ), true ),
            array( 16010, array( 'uid'=>16010, 'friends'=>false, 'actions'=>false, 'stories'=>false ), false ), //can't ask for nothing (one of actions or stories must be true)
            array( 16010, array(), false ), //uid or actorid must be specified
            array( 16010, array( 'uid'=>16010, 'actorid'=>16200 ), false ), //can't specify both uid and actor id
            array( 16010, array( 'actorid'=>16200, 'friends'=>true ), false ) //actor_id is a page or an app, which currently can't have friends
        );
	}

	/**
	 * @dataProvider providerTestConstructor
	 */
	public function testConstructor( $uid , $params, $expectingPass ) {
        try {
			$api = $this->initRest( new FeedGet(), $params, $uid );
			$api->setSessionValue( 'test', true );
			$api->validateRequest();
			
			$this->assertTrue( $expectingPass, "This test should have failed with exception!" );
			$this->assertNotNull($api, "Object missing!");

		} catch ( OpenFBAPIException $exception ) {
			$this->assertFalse( $expectingPass, "This test should have not thrown an exception! " . $exception );
		}
	}
}
?>
