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

require_once( 'BaseAPITestCase.php' );
require_once( "ringside/rest/FeedGet.php" );

class FeedGetTestCase extends BaseAPITestCase {

	public static function providerTestGet() {
        return array(
            array( 16010, array( 'uid'=>16010 ), 2, false ),
            array( 16011, array( 'uid'=>16011 ), 3, false ),
            array( 16014, array( 'uid'=>16014 ), 0, false ),
            array( 16010, array( 'uid'=>16010, 'actions'=>false ), 1, false ),
            array( 16010, array( 'uid'=>16010, 'stories'=>false ), 1, false ),
            array( 16010, array( 'uid'=>16010, 'friends'=>true ), 3, false ),
            array( 16010, array( 'actorid'=>16020 ), 3, false ),
            array( 16014, array( 'actorid'=>16021 ), 0, false ),
            array( 16010, array( 'actorid'=>16020, 'actions'=>false ), 0, false ),
            array( 16010, array( 'actorid'=>16020, 'stories'=>false ), 3, false )
        );
	}

	/**
	 * @dataProvider providerTestGet
	 * 
	 * This test relies on data to already be present from the AllAPITests-setup.sql script.
	 */
	public function testGet( $uid , $params, $expectedResultsCount, $expectingFailure ) {
        try {
            $method = $this->initRest( new FeedGet(), $params, $uid );
            $method->validateRequest();
            $response = $method->execute();
//            error_log( 'response: ' . var_export( $response, true ) );
            $entries = $response[ 'result' ];
            $count = count( $entries );
            $this->assertEquals( $count, $expectedResultsCount, "Actual count ($count) did not match expected count ($expectedResultsCount)" );

            $entries = $response[ 'result' ];
            if( isset( $entries ) ) {
	            //make sure responses have information in them
	            foreach( $entries as $entry ) {
	                $authorid = $entry[ 'author_id' ];
	                $actorid = $entry[ 'actor_id' ];
	                $type = $entry[ 'type' ];
	                $title = $entry[ 'title' ];
	                $created = $entry[ 'created' ];
	                
	                $this->assertTrue( isset( $authorid ) || isset( $actorid ) );
	                $this->assertTrue( isset( $type ) && ( $type == '1' || $type == '2' ) );
	                $this->assertTrue( isset( $title ) && !empty( $title ) );
	                $this->assertTrue( isset( $created ) );
	            }
            }
            $this->assertFalse( $expectingFailure, "Should have thrown an exception" );
        } catch ( Exception $exception ) {
        	$this->assertTrue( $expectingFailure, "Should not have thrown an exception: " . $exception->getMessage() . 'trace' . $exception->getTraceAsString() );
        }
	}
}
?>
