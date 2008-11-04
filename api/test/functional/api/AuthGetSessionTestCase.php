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
require_once( 'PHPUnit/Framework.php' );
require_once( "BaseAPITestCase.php" );
require_once( "ringside/api/OpenFBServer.php" );
require_once( "ringside/api/facebook/AuthGetSession.php" );

//$GLOBALS['global_validation'] = true;
//
//define( 'APP_1_ID', '8100' );
//define( 'APP_1_KEY', 'test_case_key_8100' );
//define( 'APP_1_SESSIONID', '900' );
//
//define( 'APP_2_ID', '8101' );
//define( 'APP_2_KEY', 'test_case_key_8101' );
//define( 'APP_2_SESSIONID', '901' );
//
//define( 'APP_3_ID', '8199' );
//define( 'APP_3_KEY', 'hahahaha' );
//define( 'APP_3_SESSIONID', '902' );

class AuthGetSessionTestCase extends BaseAPITestCase  {
    
	public function testDummyFunction()
	{
		//this prevents this test case from failing
		return;
	}

//    public static function providerTestExecute()
//    {        
//        $a3 = array('method'=>'auth.createToken', 'format'=>'php', 'api_key'=>'test_case_key_8100', 'v'=>'1');
//        $a3['sig'] = makeSig( $a3, 'secrety_key_1000' );
//        
//        return array(
//          array( $a3, '8001' )
//        );
//    }
    
    public static function providerTestExecute()
    {

        $tests[] = array( '3535', true );
        $tests[] = array( '3535', false, FB_ERROR_CODE_API_KEY_NOT_ASSOCIATED_WITH_APP  );
        $tests[] = array( '3535', true);

        return $tests;

    }    
 
	/**
     * @dataProvider providerTestExecute
     */
    /*
    public function testExecute( $uid, $approved, $code = 0)
    {    
       $this->markTestSkipped("Session tests are causing issues, because session is reloaded");
       return;
       
        try {
            $session = array( "approved"=>$approved );
            $method = new AuthGetSession( $uid, array(), $session );
            $actual = $method->execute();

//            $this->assertEquals( $code, 0 );
//            $this->assertEquals( $sessionId , $actual['auth_token']);
//            $this->assertEquals( $sessionId , $session['session_id'] );
//            $this->assertEquals( $apiKey, $session['api_key'] );
//            $this->assertEquals( 'auth_token' , $session['type'] );
//            $this->assertEquals( $appId, $session['app_id'] );
//            $this->assertGreaterThan( time(), $session['expires'] );
            
        } catch ( OpenFBAPIException $exception ) {
             $this->assertNotEquals( 0, $code , "Test should not have thrown exception " . $exception->getCode() );
            $this->assertEquals( $code, $exception->getCode() );
        }
            
//        $this->markTestSkipped( 'Problems executing concurrent session tests within PHPUNIT' );
            
//        $server = new RestServer( '', $apiParams );
//    	$response = $server->executeRequest( $apiParams );
//    	
//    	$this->assertArrayHasKey( 'auth_token', $response, "auth_token not set");
//        $this->assertTrue(  strlen($response['auth_token'])==26, "Auth Token length was not 26 - " . $response['auth_token'] );
//    	
//        $apiParams['method'] = 'auth.approveToken';
//    	$apiParams['auth_token'] = $response['auth_token'];
//    	$apiParams['uid'] = $uid;
//        $apiParams['sig'] = makeSig( $apiParams, 'secrety_key_1000' );
//    	
//    	$server = new RestServer( '', $apiParams );
//    	$response = $server->executeRequest( $apiParams );
//    	
//    	$this->assertArrayHasKey( 'result', $response, "auth.approveToken - FAILED - Missing result");
//    	$this->assertTrue(  $response['result']==1 , "Auth Token Not Approved - " . $response['auth_token'] );
//        
//    	$apiParams['method'] = 'auth.getSession';
//        unset( $apiParams['uid'] );
//    	$apiParams['sig'] = makeSig( $apiParams, 'secrety_key_1000' );
//    	
//    	$server = new RestServer( '', $apiParams );
//    	$response = $server->executeRequest( $apiParams );
//    	$this->assertArrayHasKey( 'session_key', $response, "auth.getSession - Failed - Missing session_key");
    	
    }
	 */
    
    /*
    public function testExecuteFails () {
       $this->markTestSkipped("Session tests are causing issues, because session is reloaded");
       return;
       $apiParams = array(  
        	'method'=>'auth.getSession', 
        	'format'=>'php', 
        	'api_key'=>'test_case_key_8100', 
        	'v'=>'1');
        $server = new OpenFBServer( '', $apiParams );
    	$response = $server->executeRequest( $apiParams );
    	
    	$this->assertEquals( FB_ERROR_CODE_INCORRECT_SIGNATURE, $response['error_code'] , $response['error_msg']); 
    }
	 */
}

?>
