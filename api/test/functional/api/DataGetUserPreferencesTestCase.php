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
require_once( "ringside/api/facebook/DataGetUserPreferences.php" );

      define( 'DATA_GETPREFS_USER_ID', '18000' );
      define( 'DATA_GETPREFS_APP_ID', '18100' );
      
class DataGetUserPreferencesTestCase extends BaseAPITestCase {
 
   public static function providerTestConstructor() {
      
      $test[] = array ( DATA_GETPREFS_USER_ID, array(), array('app_id'=>DATA_GETPREFS_APP_ID), 0 );
      
      return $test;
   }
    
   /**
    * @dataProvider providerTestConstructor
    */
   public function testConstructor( $userId, $params, $session, $code )
   {
      try {
         $method = $this->initRest( new DataGetUserPreferences(), $params, $userId, $session['app_id'] );
         $this->assertTrue( $code == 0 );
      } catch ( OpenFBAPIException $exception ) {
         $this->assertEquals( $code, $exception->getCode() );
      }

   }

   public static function providerTestExecute() {
      
      $params = array();
      $params[] = array('pref_id'=>1, 'value'=>'alpha');
      $test[] = array ( DATA_GETPREFS_USER_ID, array("replace"=>"true", "values"=>json_encode($params)), array('app_id'=>DATA_GETPREFS_APP_ID) );
      
      $params = array();
      $params[] = array('pref_id'=>0, 'value'=>'the big lebowski');
      $test[] = array ( DATA_GETPREFS_USER_ID, array("replace"=>"true", "values"=>json_encode($params)), array('app_id'=>DATA_GETPREFS_APP_ID) );
      
      $params = array();
      $params[] = array('pref_id'=>200, 'value'=>'the end of the line');
      $test[] = array ( DATA_GETPREFS_USER_ID, array("replace"=>"true", "values"=>json_encode($params)), array('app_id'=>DATA_GETPREFS_APP_ID) );
      
      $params = array();
      $params[] = array('pref_id'=>32, 'value'=>'alpha');
      $params[] = array('pref_id'=>99, 'value'=>'gremmlins');
      $test[] = array ( DATA_GETPREFS_USER_ID, array("replace"=>"true", "values"=>json_encode($params)), array('app_id'=>DATA_GETPREFS_APP_ID) );

      $params = array();
      $params[] = array('pref_id'=>80, 'value'=>'http://some.url.com/and?thing=rock');
      $params[] = array('pref_id'=>30, 'value'=>'gamma ray');
      $test[] = array ( DATA_GETPREFS_USER_ID, array("replace"=>"true", "values"=>json_encode($params)), array('app_id'=>DATA_GETPREFS_APP_ID) );

      $params = array();
      $params[] = array('pref_id'=>80, 'value'=>'right back at you<a>');
      $params[] = array('pref_id'=>50, 'value'=>'http://haha.url.com/and?thing=rock');
      $test[] = array ( DATA_GETPREFS_USER_ID, array("replace"=>"true", "values"=>json_encode($params)), array('app_id'=>DATA_GETPREFS_APP_ID) );
      
      $params = array();
      $params[] = array('pref_id'=>10, 'value'=>'i am ten?');
      $params[] = array('pref_id'=>30, 'value'=>'going apps');
      $params[] = array('pref_id'=>152, 'value'=>'Going higher.');
      $test[] = array ( DATA_GETPREFS_USER_ID, array("replace"=>"true", "values"=>json_encode($params)), array('app_id'=>DATA_GETPREFS_APP_ID) );
      
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
            
            $method = $this->initRest( new DataGetUserPreferences(), array(), $userId, $session['app_id'] );
            $response = $method->execute();
            
            $this->assertArrayHasKey( "preference", $response );            
            
            $actualPrefs = $response['preference'];
            $actual = array();
            foreach ( $actualPrefs as $count=>$prefIdValue ) {
                 $actual[$prefIdValue['pref_id']] = $prefIdValue['value'];               
            }
            
            $expectedPrefs = json_decode( $params['values'] , true );
            $expected = array();
            foreach ( $expectedPrefs as $count=>$prefIdValue ) {
                 $expected[$prefIdValue['pref_id']] = $prefIdValue['value'];               
            }
            
            foreach ( $expected as $key=>$value ) {
               $this->assertArrayHasKey( $key, $actual , " Expected has key not in actual ($key)" );
               $this->assertEquals( $value, $actual[$key] );   
            }
            
            foreach ( $actual as $key=>$value ) {
               $this->assertArrayHasKey( $key, $expected , " Actual has key not expected ($key)" );
               $this->assertEquals( $value, $expected[$key] );   
            }
            
        } catch ( OpenFBAPIException $exception ) {
            $this->fail("Exception not expected!");
        }
       
    }


}
