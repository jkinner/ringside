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
require_once 'ringside/social/dsl/RingsideSocialDslParser.php';
require_once 'MockApplication.php';

class fbGoogleAnalyticsTestCase extends PHPUnit_Framework_TestCase {

   public static function providerTestFbGoogleAnalytics() {

      return array(
         array( '<fb:google-analytics uacct="UA-9999999-99" />' , self::makeExpected( "UA-9999999-99" ) )
      );
   }

   /**
    * @dataProvider providerTestFbGoogleAnalytics
    */
   public function testFbGoogleAnalytics ( $parseString, $expected ) {
       $parser = new RingsideSocialDslParser( new MockApplication() );

       $results = $parser->parseString( $parseString );

       $this->assertEquals( $expected, trim($results), "results do not match" );
   }

   public static function makeExpected( $uacct ) {
      $expected = '<script src="https://ssl.google-analytics.com/urchin.js" type="text/javascript">';
      $expected .= '</script>';
      $expected .= '<script type="text/javascript">';
      $expected .= '_uacct = "'.$uacct.'"';
      $expected .= 'urchinTracker();';
      $expected .= ' </script>';
      return $expected;
   }
}

?>
