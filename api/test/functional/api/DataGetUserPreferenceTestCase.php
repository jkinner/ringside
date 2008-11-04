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
require_once( "ringside/api/facebook/DataSetUserPreference.php" );
require_once( "ringside/api/facebook/DataGetUserPreference.php" );

      define( 'DATA_GETPREF_USER_ID', '18000' );
      define( 'DATA_GETPREF_APP_ID', '18100' );
      
      define( 'DATA_PREFS_1_VALUE', 'alpha' );
      define( 'DATA_PREFS_2_VALUE', 'alpha' );
      define( 'DATA_PREFS_3_VALUE', 'alpha' );
      define( 'DATA_PREFS_4_VALUE', 'alpha' );
      define( 'DATA_PREFS_5_VALUE', 'alpha' );
      
class DataGetUserPreferenceTestCase extends BaseAPITestCase {
 
   public static function providerTestConstructor() {
      
    return array(
          array ( DATA_GETPREF_USER_ID, array(), array(), FB_ERROR_CODE_PARAMETER_MISSING ), 
          array ( DATA_GETPREF_USER_ID, array(), array('app_id'=>DATA_GETPREF_APP_ID), FB_ERROR_CODE_PARAMETER_MISSING ), 
          array ( DATA_GETPREF_USER_ID, array('pref_id'=>1), array('app_id'=>DATA_GETPREF_APP_ID), 0 ), 
          array ( DATA_GETPREF_USER_ID, array('pref_id'=>-1), array('app_id'=>DATA_GETPREF_APP_ID), FB_ERROR_CODE_PARAMETER_MISSING ), 
          array ( DATA_GETPREF_USER_ID, array('pref_id'=>300), array('app_id'=>DATA_GETPREF_APP_ID), FB_ERROR_CODE_PARAMETER_MISSING ), 
          array ( DATA_GETPREF_USER_ID, array('pref_id'=>100), array('app_id'=>DATA_GETPREF_APP_ID), 0 ), 
      );
   }
   
   /**
    * @dataProvider providerTestConstructor
    */
   public function testConstructor( $userId, $params, $session, $code )
    {     
        try {
            $method = $this->initRest( new DataGetUserPreference(), $params, $userId, $session['app_id'] );
            $this->assertTrue( $code == 0 );
        } catch ( OpenFBAPIException $exception ) {
            $this->assertEquals( $code, $exception->getCode() );
        }

    }
    
   public static function providerTestExecute() {
      
    return array(
          array ( DATA_GETPREF_USER_ID, 0,  'alpha' , array('app_id'=>DATA_GETPREF_APP_ID) ), 
          array ( DATA_GETPREF_USER_ID, 25, 'gamma ray' , array('app_id'=>DATA_GETPREF_APP_ID) ), 
          array ( DATA_GETPREF_USER_ID, 44, 'http://some.url.com/and?thing=rock' , array('app_id'=>DATA_GETPREF_APP_ID) ), 
          array ( DATA_GETPREF_USER_ID, 25, '' , array('app_id'=>DATA_GETPREF_APP_ID) ), 
          array ( DATA_GETPREF_USER_ID, 44, '0' , array('app_id'=>DATA_GETPREF_APP_ID) ), 
          );
   }
   
   /**
    * @dataProvider providerTestExecute
    */
    public function testExecute( $userId, $prefId, $prefValue, $session ) {
        try {
            $params = array( "pref_id"=>$prefId, "value"=>$prefValue);
            $method = $this->initRest( new DataSetUserPreference(), $params, $userId, $session['app_id'] );
            $response = $method->execute();
            $this->assertTrue( empty( $response ) , "Method response should have been empty." );
            
            // Validate it was really set?
            $params = array( "pref_id"=>$prefId );
            $method = $this->initRest( new DataGetUserPreference(), $params, $userId, $session['app_id'] );
            $response = $method->execute();
            
            $this->assertArrayHasKey( "result", $response );
            if ( $prefValue == '0' ) $prefValue = '';
            $this->assertEquals( $prefValue, $response['result'] );
            
            
        } catch ( OpenFBAPIException $exception ) {
            $this->fail("Exception not expected! " . $exception->getMessage() );
        }
       
    }

}
