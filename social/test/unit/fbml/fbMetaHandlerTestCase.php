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

class fbMetaHandlerTestCase extends PHPUnit_Framework_TestCase {
   
   public static function providerTestFbMeta() {

      return array(
         array( fbMetaHandlerTestCase::buildCase() , fbMetaHandlerTestCase::buildCase() )
      );
   }
   
   /**
    * @dataProvider providerTestFbMeta
    */
   public function testFbMeta ( $parseString, $expected ) {
        $ma = new MockApplication();        
        $parser = new RingsideSocialDslParser( $ma );
        
        $results = $parser->parseString( $parseString );
        
        $this->assertEquals( stripNewlines($expected), stripNewlines(trim($results)), "$expected != $results" );
   }
   
   public function buildCase() {
      $case = '<html><head>';
      $case .= '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />';
      $case .= '<meta name="something" content="else" />';
      $case .= '<title>Untitled Document</title>';
      $case .= '</head><body>'."\n".'</body></html>';
      return $case;
   }
}

function stripNewlines($str)
{
	return str_replace("\n" , '', $str);
}
?>
