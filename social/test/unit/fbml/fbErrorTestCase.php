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

class fbErrorTestCase extends PHPUnit_Framework_TestCase {

   public static function providerTestFbError() {

      return array(
         array( "<fb:error message=\"Love message\"/>" , self::makeExpected( "Love message" , "") ),
         array( "<fb:error><fb:message>Friend message</fb:message></fb:error>" , self::makeExpected( "Friend message" , "")  ),
         array( "<fb:error><fb:message>Error message</fb:message>This is the error message text.</fb:error>" , self::makeExpected( "Error message" , "This is the error message text." ) ),
         array( "<fb:error>Hello,<fb:message>Error message</fb:message>This is the error message text.</fb:error>" , self::makeExpected( "Error message" , "Hello,This is the error message text." ) )
      );
   }

   /**
    * @dataProvider providerTestFbError
    */
   public function testFbError ( $parseString, $expected ) {
       $parser = new RingsideSocialDslParser( new MockApplication() );

       $results = $parser->parseString( $parseString );

       $this->assertEquals( $expected, trim($results) );
   }

   public static function makeExpected( $message, $body ) {
      $expected  = "<div class=\"error_message\"><h2>$message</h2>\n";
      $expected .= $body;
      $expected .= "</div>";
      return $expected;
   }
}
?>
