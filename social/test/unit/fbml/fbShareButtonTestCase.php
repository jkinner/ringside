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

class fbShareButtonTestCase extends PHPUnit_Framework_TestCase {

   public static function providerTestFbShareButton() {

      $link = 'http://www.techcrunch.com';

      $metas = array( 
      	'medium'=>'blog', 
      	'title'=>'Leonidas in All of Us',
        'video_type'=>'application/x-shockwave-flash',
        'video_height'=>'345',
        'video_width'=>'473',
        'description'=>'That\'s the lesson 300 teaches us.'
      );

      $links = array( 
      	'image_src'=>'http://9.content.collegehumor.com/d1/ch6/f/6/collegehumor.b38e345f621621dfa9de5456094735a0.jpg', 
      	'video_src'=>'http://www.collegehumor.com/moogaloop/moogaloop.swf?clip_id=1757757&autoplay=true', 
      	'target_url'=>'http://www.collegehumor.com/video:1757757', 
      );
      
      return array(
         array( '<fb:share-button class="url" href="'.$link.'"/>' , fbShareButtonTestCase::makeExpectedLinkResults( $link ) ),
         array( fbShareButtonTestCase::buildCase( $metas, $links ) , fbShareButtonTestCase::makeExpectedMetaResults( $metas, $links ) )
      );
   }
   
   /**
    * @dataProvider providerTestFbShareButton
    */
   public function testFbShareButton ( $parseString, $expected ) {
        $ma = new MockApplication();        
        $parser = new RingsideSocialDslParser( $ma );
        
        $results = $parser->parseString( $parseString );
        
        $this->assertEquals( $expected, trim($results), "$expected != $results" );
   }

   public function buildCase( $metas, $links ) {
      $fbml = '<fb:share-button class="meta">';
      foreach( $metas as $name=>$content ) {
         $fbml .= '	<meta name="'.$name.'" content="'.$content.'"/>';
      }
      foreach( $links as $rel=>$href ) {
         $fbml .= '	<link rel="'.$rel.'" href="'.$href.'"/>';
      }
      $fbml .= '</fb:share-button>';
      return $fbml;
   }
   
   public function makeExpectedLinkResults( $link ) {
      $exp = '<form name="shareForm" action="shareForm.php" method="post">';
      $exp .= '	<input name="href" type="hidden" value="'.$link.'" />';
      $exp .= '	<input type="submit" name="Share" value="Share" />';
      $exp .= '</form>';
      return $exp;
   }
   
   public function makeExpectedMetaResults( $metas, $links ) {
      $exp = '<form name="shareForm" action="shareForm.php" method="post">';
      
      foreach( $metas as $name=>$content ) {
         $exp .= '  <input type="hidden" name="'.$name.'" value="'.$content.'" />';
      }
      
      foreach( $links as $rel=>$href ) {
         $exp .= '  <input type="hidden" name="'.$rel.'" value="'.$href.'" />';
      }
      
      $exp .= '	<input type="submit" name="Share" value="Share" />';
      $exp .= '</form>';
      return $exp;
   }
}
?>
