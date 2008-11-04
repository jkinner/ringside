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
require_once( "ringside/api/facebook/DataSetUserPreferences.php" );
require_once( "ringside/api/dao/AppPrefs.php" );

      define( 'DATA_SETPREFS_USER_ID', '18000' );
      define( 'DATA_SETPREFS_APP_ID', '18100' );
            
class DataSetUserPreferencesTestCase extends BaseAPITestCase {
 
   public static function providerTestConstructor() {
      
      $test[] = array ( DATA_SETPREFS_USER_ID, array(), array(), FB_ERROR_CODE_PARAMETER_MISSING );
      $test[] = array ( DATA_SETPREFS_USER_ID, array(), array('app_id'=>DATA_SETPREFS_APP_ID), FB_ERROR_CODE_PARAMETER_MISSING );
      
      $params = array ();
      $params[] = array('pref_id'=>1, 'value'=>'alpha');
      $test[] = array ( DATA_SETPREFS_USER_ID, array("values"=>json_encode($params)), array('app_id'=>DATA_SETPREFS_APP_ID), 0 );
      
      $params = array();
      $params[] = array('pref_id'=>1, 'value'=>'alpha');
      $params[] = array('pref_id'=>99, 'value'=>'gremmlins');
      $test[] = array ( DATA_SETPREFS_USER_ID, array("values"=>json_encode($params)), array('app_id'=>DATA_SETPREFS_APP_ID), 0 );

      $params = array();
      $params[] = array('pref_id'=>1, 'value'=>'alpha');
      $params[] = array('pref_id'=>-1, 'value'=>'alpha');      
      $test[] = array ( DATA_SETPREFS_USER_ID, array("values"=>json_encode($params)), array('app_id'=>DATA_SETPREFS_APP_ID), FB_ERROR_CODE_PARAMETER_MISSING );
      
      $params = array();
      $params[] = array('pref_id'=>1, 'value'=>'alpha');
      $params[] = array('pref_id'=>300, 'value'=>'alpha');      
      $test[] = array ( DATA_SETPREFS_USER_ID, array("values"=>json_encode($params)), array('app_id'=>DATA_SETPREFS_APP_ID), FB_ERROR_CODE_PARAMETER_MISSING );
      
      $params = array();
      $params[] = array('pref_id'=>1, 'value'=>'alpha');
      $params[] = array('pref_id'=>100, 'value'=>str_repeat('1234567890',30));      
      $test[] = array ( DATA_SETPREFS_USER_ID, array("values"=>json_encode($params)), array('app_id'=>DATA_SETPREFS_APP_ID), FB_ERROR_CODE_PARAMETER_MISSING );

      $params = array();
      $params[] = array('pref_id'=>1, 'value'=>'alpha');
      $params[] = array('pref_id'=>99, 'value'=>'gremmlins');
      $test[] = array ( DATA_SETPREFS_USER_ID, array("replace"=>"true","values"=>json_encode($params)), array('app_id'=>DATA_SETPREFS_APP_ID), 0 );
      $test[] = array ( DATA_SETPREFS_USER_ID, array("replace"=>"false","values"=>json_encode($params)), array('app_id'=>DATA_SETPREFS_APP_ID), 0 );
      $test[] = array ( DATA_SETPREFS_USER_ID, array("replace"=>"oranges","values"=>json_encode($params)), array('app_id'=>DATA_SETPREFS_APP_ID), FB_ERROR_CODE_PARAMETER_MISSING );
      
      return $test;
   }
   
   /**
    * @dataProvider providerTestConstructor
    */
   public function testConstructor( $userId, $params, $session, $code )
    {     
        try {
            $method = $this->initRest( new DataSetUserPreferences(), $params, $userId, $session['app_id'] );
            $this->assertTrue( $code == 0 );
        } catch ( OpenFBAPIException $exception ) {
            $this->assertEquals( $code, $exception->getCode() );
        }

    }
    
   public static function providerTestExecute() {
      
      $params = array();
      $params[] = array('pref_id'=>1, 'value'=>'alpha');
      $test[] = array ( DATA_SETPREFS_USER_ID, array("values"=>json_encode($params)), array('app_id'=>DATA_SETPREFS_APP_ID) );
      
      $params = array();
      $params[] = array('pref_id'=>0, 'value'=>'the big lebowski');
      $test[] = array ( DATA_SETPREFS_USER_ID, array("values"=>json_encode($params)), array('app_id'=>DATA_SETPREFS_APP_ID) );
      
      $params = array();
      $params[] = array('pref_id'=>200, 'value'=>'the end of the line');
      $test[] = array ( DATA_SETPREFS_USER_ID, array("values"=>json_encode($params)), array('app_id'=>DATA_SETPREFS_APP_ID) );
      
      $params = array();
      $params[] = array('pref_id'=>32, 'value'=>'alpha');
      $params[] = array('pref_id'=>99, 'value'=>'gremmlins');
      $test[] = array ( DATA_SETPREFS_USER_ID, array("values"=>json_encode($params)), array('app_id'=>DATA_SETPREFS_APP_ID) );

      $params = array();
      $params[] = array('pref_id'=>80, 'value'=>'http://some.url.com/and?thing=rock');
      $params[] = array('pref_id'=>30, 'value'=>'gamma ray');
      $test[] = array ( DATA_SETPREFS_USER_ID, array("replace"=>"false","values"=>json_encode($params)), array('app_id'=>DATA_SETPREFS_APP_ID) );

      $params = array();
      $params[] = array('pref_id'=>80, 'value'=>'');
      $params[] = array('pref_id'=>50, 'value'=>'http://haha.url.com/and?thing=rock');
      $test[] = array ( DATA_SETPREFS_USER_ID, array("values"=>json_encode($params)), array('app_id'=>DATA_SETPREFS_APP_ID) );
      
      $params = array();
      $params[] = array('pref_id'=>10, 'value'=>'i am ten?');
      $params[] = array('pref_id'=>30, 'value'=>'0');
      $params[] = array('pref_id'=>152, 'value'=>'Going higher.');
      $test[] = array ( DATA_SETPREFS_USER_ID, array("values"=>json_encode($params)), array('app_id'=>DATA_SETPREFS_APP_ID) );
      
      return $test;
   }
   
   /**
    * @dataProvider providerTestExecute
    */
    public function testExecute( $userId, $params, $session ) {
        try {
            $method = $this->initRest( new DataSetUserPreferences(), $params, $userId, $session['app_id'] );
            $response = $method->execute();
            $this->assertTrue( empty( $response ) , "Method response should have been empty." );
            
            // Validate it was really set?
            $preference = Api_Dao_AppPrefs::getAppPrefsByAppIdAndUserId($session['app_id'], $userId);
            $prefs = json_decode($preference[0]->value, true);

            $paramPreferences = json_decode( $params['values'] , true );
            foreach ( $paramPreferences as $count=>$param ) {
               $value = $prefs[$param['pref_id']];
               if ( $param['value'] == '0' || $param['value'] == '' ) {
                  $this->assertTrue( empty($value), "Preference ({$param['pref_id']}) should have been empty!" );
               } else {
                  $this->assertEquals( $param['value'], $value );
               }
            }
        } catch ( OpenFBAPIException $exception ) {
            $this->fail("Exception not expected!");
        }
       
    }

   public static function providerTestReplaceExecute() {

      $params = array();
      $params[] = array('pref_id'=>1, 'value'=>'alpha');
      $test[] = array ( DATA_SETPREFS_USER_ID, array("replace"=>true,"values"=>json_encode($params)), array('app_id'=>DATA_SETPREFS_APP_ID) , 99 );
      
      $params = array();
      $params[] = array('pref_id'=>32, 'value'=>'alpha');
      $params[] = array('pref_id'=>99, 'value'=>'gremmlins');
      $test[] = array ( DATA_SETPREFS_USER_ID, array("replace"=>true,"values"=>json_encode($params)), array('app_id'=>DATA_SETPREFS_APP_ID), 1 );

      $params = array();
      $params[] = array('pref_id'=>80, 'value'=>'http://some.url.com/and?thing=rock');
      $params[] = array('pref_id'=>30, 'value'=>'gamma ray');
      $test[] = array ( DATA_SETPREFS_USER_ID, array("replace"=>true,"values"=>json_encode($params)), array('app_id'=>DATA_SETPREFS_APP_ID), 32 );

      $params = array();
      $params[] = array('pref_id'=>80, 'value'=>'');
      $params[] = array('pref_id'=>50, 'value'=>'http://haha.url.com/and?thing=rock');
      $test[] = array ( DATA_SETPREFS_USER_ID, array("replace"=>true,"values"=>json_encode($params)), array('app_id'=>DATA_SETPREFS_APP_ID), 30 );
      
      $params = array();
      $params[] = array('pref_id'=>10, 'value'=>'i am ten?');
      $params[] = array('pref_id'=>30, 'value'=>'0');
      $params[] = array('pref_id'=>152, 'value'=>'Going higher.');
      $test[] = array ( DATA_SETPREFS_USER_ID, array("replace"=>true,"values"=>json_encode($params)), array('app_id'=>DATA_SETPREFS_APP_ID), 50 );
      
      return $test;
      
   }
   
   /**
    * @dataProvider providerTestReplaceExecute
    */
    public function testReplaceExecute( $userId, $params, $session, $missingId ) {
        try {
            $method = $this->initRest( new DataSetUserPreferences(), $params, $userId, $session['app_id'] );
            $response = $method->execute();
            $this->assertTrue( empty( $response ) , "Method response should have been empty." );
            
            // Validate it was really set?
            $preference = Api_Dao_AppPrefs::getAppPrefsByAppIdAndUserId($session['app_id'], $userId);
            $prefs = json_decode($preference[0]->value, true);
            $this->assertNull($prefs[$missingId]);

            $paramPreferences = json_decode( $params['values'] , true );
            foreach ( $paramPreferences as $count=>$param ) {
               $value = $prefs[$param['pref_id']];
               if ( $param['value'] == '0' || $param['value'] == '' ) {
                  $this->assertTrue( empty($value), "Preference ({$param['pref_id']}) should have been empty!" );
               } else {
                  $this->assertEquals( $param['value'], $value );
               }
            }
        } catch ( OpenFBAPIException $exception ) {
            $this->fail("Exception not expected!");
        }
       
    }

}
