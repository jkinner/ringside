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

require_once( "BaseAPITestCase.php" );
require_once( "ringside/api/facebook/AuthCreateToken.php" );
require_once( 'UnitTestUtils.php' );


$GLOBALS['global_validation'] = true;


define( 'APP_1_ID', '8100' );
define( 'APP_1_KEY', 'test_case_key_8100' );
define( 'APP_1_SESSIONID', '900' );

define( 'APP_2_ID', '8101' );
define( 'APP_2_KEY', 'test_case_key_8101' );
define( 'APP_2_SESSIONID', '901' );

define( 'APP_3_ID', '8199' );
define( 'APP_3_KEY', 'hahahaha' );
define( 'APP_3_SESSIONID', '902' );

class AuthCreateTokenTestCase extends BaseAPITestCase {

    public static function providerTestExecute()
    {

        $tests[] = array( APP_1_KEY, APP_1_SESSIONID, APP_1_ID, true );
        $tests[] = array( APP_2_KEY, APP_2_SESSIONID, APP_2_ID, true);
        $tests[] = array( APP_3_KEY, APP_3_SESSIONID, APP_3_ID, false, FB_ERROR_CODE_API_KEY_NOT_ASSOCIATED_WITH_APP );

        return $tests;

    }
 
	/**
     * @dataProvider providerTestExecute
     */
    public function testExecute( $apiKey, $sessionId, $appId, $pass, $code = 0 )
    {        
        try {
            $session = array( "session_id"=>$sessionId );
            $params = array("api_key"=>$apiKey);
            $method = $this->initRest( new AuthCreateToken(), $params, null, null, null, $session  );
            $actual = $method->execute();
            
            $this->assertEquals( $code, 0 );
            $this->assertEquals( $sessionId , $actual['auth_token']);
            $this->assertEquals( $sessionId , $session['session_id'] );
            $this->assertEquals( $apiKey, $session['api_key'] );
            $this->assertEquals( 'auth_token' , $session['type'] );
            $this->assertEquals( $appId, $session['app_id'] );
            $this->assertGreaterThan( time(), $session['expires'] );
            
        } catch ( OpenFBAPIException $exception ) {     
             $this->assertNotEquals( 0, $code , "Test should not have thrown exception " . $exception->getTraceAsString() );
            $this->assertEquals( $code, $exception->getCode() );
        }
     
    }

}
?>
