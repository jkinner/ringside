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
require_once 'MockClient.php';

class fbLinkHandlerTestCase extends PHPUnit_Framework_TestCase {
   
   public static function providerTestFbLink() {

      return array(
         array( self::buildCase() , self::buildExpected() )
      );
   }
   
   /**
    * @dataProvider providerTestFbLink
    */
   public function testFbLink ( $parseString, $expected ) {
        $ma = new MockApplication();        
        $parser = new RingsideSocialDslParser( $ma );
        
        $results = $parser->parseString( $parseString );
        
        $this->assertEquals( $expected, trim($results), "$expected != $results" );
   }

   public function buildCase() {
      $case = '<html><head>';
      $case .= '<link rel="Next" href="http://www.techcrunch.com"/>';
      $case .= '<title>Untitled Document</title>';
      $case .= '</head><body></body></html>';
      return $case;
   }
   
   public function buildExpected() {
      $case = '<html><head>';
      $case .= '<title>Untitled Document</title>';
      $case .= '</head><body>';
      $case .= '<link rel="Next" href="http://www.techcrunch.com"/>';
      $case .= '</body></html>';
      return $case;
   }
}
?>
