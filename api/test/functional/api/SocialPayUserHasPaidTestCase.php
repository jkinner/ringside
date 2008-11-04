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
require_once( "ringside/api/OpenFBAPIException.php" );

require_once( "ringside/rest/SocialPayUserHasPaid.php" );

/**
 * @author Brian R. Robinson brobinson@ringsidenetworks.com
 */
class SocialPayUserHasPaidTestCase extends BaseAPITestCase
{

	public static function providerConstructor() {

		return array(
		array( 36001, array( 'uid'=>36001, 'aid'=>36050, 'nid'=>36010 ), 36050 , false )
		);
	}

	/**
	 * @dataProvider providerConstructor
	 *
	 * $expected if true, we're expecting the constructor to throw an exception
	 */
	public function testConstructor( $uid, $params, $appId, $expected )
	{
		try {
		    $plan = $this->initRest( new SocialPayUserHasPaid(), $params, $uid, $appId );
			$this->assertFalse ( $expected, "Should have thrown exception " . var_export( $params ,true ) );
		} catch ( OpenFBAPIException $e ) {
			$this->assertTrue( $expected, "Should NOT have thrown exception " . var_export( $params ,true ) );
			$this->assertEquals( FB_ERROR_CODE_PARAMETER_MISSING, $e->getCode() );
		}
	}

	public static function providerExecute() {
		$params1 = array( 'uid'=>36070, 'aid'=>36060 );
		$params2 = array( 'uid'=>36070, 'aid'=>36060, 'plan'=>'a, b' );
		$params3 = array( 'uid'=>36070, 'aid'=>36060, 'plan'=>'b,c' );
        $params4 = array( 'uid'=>36070, 'aid'=>36060, 'plan'=>'c' );
        $params5 = array( 'uid'=>36070, 'aid'=>36060, 'plan'=>'A' );
        return array(
    		array( 36070, $params1, 36060, true ),
            array( 36070, $params2, 36060, true ),
            array( 36070, $params3, 36060, false ),
            array( 36070, $params4, 36060, false ),
            array( 36070, $params5, 36060, true )
		);
	}

	/**
	 * @dataProvider providerExecute
	 */
	public function testExecute( $uid, $params, $appId, $expectedResult )
	{
		try {
            //error_log( 'params: ' . print_r( $params, true ) );
			$api = $this->initRest( new SocialPayUserHasPaid(), $params, $uid, $appId, 'socialkey' );
			$result = $api->execute();
			//error_log( 'result: ' . print_r( $result, true ) );

			$this->assertEquals( $result, $expectedResult );
		} catch ( OpenFBAPIException $e ) {
			if( !$expectingException ) {
				$this->fail( "Exception not expected " . $e->getMessage() . ' - ' . $e->getTraceAsString() );
			}
		}
	}

}

?>
