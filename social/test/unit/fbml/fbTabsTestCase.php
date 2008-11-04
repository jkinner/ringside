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

class fbTabsTestCase extends PHPUnit_Framework_TestCase {

	public static function providerTestFbTabs() {
    
		$items = array( 
			array ( "href"=>"runlicious/archive.php", "title"=>"Archives", "align"=>"left" ),
			array ( "href"=>"runlicious/discussion.php", "title"=>"Discussion" ),
			array ( "href"=>"runlicious/friends.php", "title"=>"Friends" ),
			array ( "href"=>"runlicious/invite.php", "title"=>"Invite", "selected"=>"true", "align"=>"right" )
		);
		
		$itemsExepected = array( 
			array ( "href"=>"runlicious/archive.php", "title"=>"Archives", "align"=>"left", "selected" => "false" ),
			array ( "href"=>"runlicious/discussion.php", "title"=>"Discussion", "align"=>"left", "selected" => "false" ),
			array ( "href"=>"runlicious/friends.php", "title"=>"Friends", "align"=>"left", "selected" => "false" ),
			array ( "href"=>"runlicious/invite.php", "title"=>"Invite", "selected"=>"true", "align"=>"right" )
		); 
		
		return array(
        	array( self::buildCase( $items ), self::makeExpected( $itemsExepected ) )
      );
	}
   
   /**
    * @dataProvider providerTestFbTabs
    */
   public function testFbTabs ( $parseString , $expected ) {
        $ma = new MockApplication();
        $ma->client = new MockClient();
        
        $parser = new RingsideSocialDslParser( $ma );
        
        $results = $parser->parseString( $parseString );
        $length1 = sizeof( $expected );
        $length2 = sizeof( $results );
        $length = ( $length1 > $length2 ) ? $length2 : $length1;
        $newExpected = substr( $expected, 0, $length );
        $newActual = substr( $results, 0, $length );
        $this->assertEquals( $newExpected, $newActual, "$expected != $results" );
   }


   public static function buildCase( $items ) {
   	$expr = "<fb:tabs>";
   	foreach( $items as $item ) {
   		$expr .= "	<fb:tab_item href='".$item['href']."' title='".$item['title']."'";
   		if( isset( $item['align'] ) ) {
   		 	$expr .= " align='".$item['align']."'";
   		}
   		if( isset( $item['selected'] ) ) {
   		 	$expr .= " selected='".$item['selected']."'";
   		}
   		$expr .= "/>";
   	}
	$expr .= "</fb:tabs>";
	return $expr;
   }
   
   public static function makeExpected( $items ) {
      $exp = '<div class="tabs"><div class="tabs_left"><ul>';
         foreach ( $items as $item ) {
            if( strcasecmp( $item["align"], "left" ) == 0 ) {
         		$isselected = strcasecmp( $item["selected"], "true") == 0;
            	$exp .= '<li><a '.($isselected?' class="selected"':'').' href="'.$item['href'].'" >'.$item['title'].'</a></li>' ;
            }
         }
      $exp .= '</ul></div>';
      $exp .= '<div class="tabs_right"><ul>';
         foreach ( $items as $item ) {
            if( strcasecmp( $item["align"], "right" ) ==0 ) {
	         	$isselected = strcasecmp( $item["selected"], "true") == 0;
            	$exp .= '<li><a href="'.$item['href'].'"'.($isselected?' class="selected"':'').'>'.$item['title'].'</a></li>';
            }
         }
      $exp .= '</ul></div></div>';
	  $exp .= '<div id="tabs-subnav"><div id="tabs-subnav-content">&nbsp;</div></div>';
      return $exp;
   }

}
?>
