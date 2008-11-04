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
require_once( "ringside/api/facebook/PagesIsAdmin.php" );

define( 'PAGES_ISADMIN_NOSUCHUSER', '2999' );
define( 'PAGES_ISADMIN_BADUSER', 'xxx' );
define( 'PAGES_ISADMIN_USER', 2000 );

define( "PAGES_ISADMIN_PAGE_1", "2200" );
define( "PAGES_ISADMIN_PAGE_2_NO_APPS", "2201" );
define( "PAGES_ISADMIN_PAGE_3_NO_FRIENDS", "2202" );

define( "PAGES_ISADMIN_APP_1_ENABLED", "2400" );
define( "PAGES_ISADMIN_APP_2_DISABLED", "2401" );
define( "PAGES_ISADMIN_APP_3_ENABLED", "2402" );
define( "PAGES_ISADMIN_APP_2_ENABLED_FOR_PAGE_2", "2401" );

define( "PAGE_ISADMIN_1_ADMIN_FAN", "2000" );
define( "PAGE_ISADMIN_1_ADMIN", "2001" );
define( "PAGE_ISADMIN_1_FAN", "2002" );
define( "PAGE_ISADMIN_2_FAN", "2003" );
define( "PAGE_ISADMIN_3_FAN", "2004" );

class PagesIsAdminTestCase extends BaseAPITestCase {

   public static function providerTestConstructor() {

      return array(
      array( PAGES_ISADMIN_NOSUCHUSER, array(), false ),
      array( PAGES_ISADMIN_NOSUCHUSER, array("page_id"=>PAGES_ISADMIN_PAGE_1), true )
      );
   }

   /**
    * @dataProvider providerTestConstructor
    */
   public function testConstructor( $uid, $params, $pass )
   {
      try {
         $notif = $this->initRest( new PagesIsAdmin(), $params, $uid );
          
         $this->assertTrue( $pass, "This test should have failed with exception!" );
         $this->assertNotNull($notif, "Object missing!");

      } catch ( OpenFBAPIException $exception ) {
         $this->assertFalse( $pass, "This test should have not thrown an exception! " . $exception );
         $this->assertEquals( FB_ERROR_CODE_PARAMETER_MISSING, $exception->getCode() );
      }
   }

    
   public static function providerTestExecute() {

      return array(
      array( PAGE_ISADMIN_1_ADMIN_FAN, array("page_id"=>PAGES_ISADMIN_PAGE_1), '1' ),
      array( PAGE_ISADMIN_1_ADMIN, array("page_id"=>PAGES_ISADMIN_PAGE_1), '1' ),
      array( PAGE_ISADMIN_1_FAN, array("page_id"=>PAGES_ISADMIN_PAGE_1), '0' ),
      array( PAGES_ISADMIN_USER, array("page_id"=>PAGES_ISADMIN_PAGE_3_NO_FRIENDS), '0' ),
      );
   }

   /**
    * @dataProvider providerTestExecute
    */
   public function testExecute( $uid, $params, $expected )
   {
       
      // pass in uid
      try {
         $method = $this->initRest( new PagesIsAdmin(), $params, $uid );
         $result = $method->execute();
         $this->assertArrayHasKey( 'result', $result, $result );
         $this->assertSame( $expected, $result['result']);
      } catch ( OpenFBAPIException $exception ) {
         $this->fail( "Should not have thrown exception " . $exception );
      }

   }
    
}
?>
