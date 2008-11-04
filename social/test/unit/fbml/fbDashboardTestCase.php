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

class fbDashboardTestCase extends PHPUnit_Framework_TestCase {

   public static function providerTestFbDashboard() {
      
      $mockUid = 12345;
      $appname = 'CrazyApp';
      $appicon = htmlentities( 'http://localhost/images/apps/icon.jpg' );     
      $canvas_url = 'CrazyHorseApp';
//      echo "json(" . json_encode( array('application_name'=>$appname,'icon_url'=>$appicon,'canvas_url'=>$canvas_url) );
      $mockApp = array( "facebook.admin.getAppProperties"=>array('application_name'=>$appname,'icon_url'=>$appicon,'canvas_url'=>$canvas_url) );

      $expected = fbDashboardTestCase::makeExpectedResultsDiv(null,null,null,$appname,$appicon,$canvas_url );
      $test[] = array ( 12345,  $mockApp, '<fb:dashboard></fb:dashboard>', $expected );
      
      $actions = array();
      $actions[] = array( "href"=>"a1.php", "title"=>"alpha dog", "body"=>'alpha1' );
      $expected = fbDashboardTestCase::makeExpectedResultsDiv($actions,null,null,$appname,$appicon,$canvas_url) ;
      $test[] = array ( 12345, $mockApp, '<fb:dashboard><fb:action href="a1.php" title="alpha dog">alpha1</fb:action></fb:dashboard>', $expected);
      
      return $test;
   }
   
   /**
    * @dataProvider providerTestFbDashboard
    */   
   public function testFbDashboard ( $mockUid, $mockMethodResults, $parseString, $expected ) {
        $ma = new MockApplication();
        $ma->uid = $mockUid;
        $ma->client = new MockClient();
        $ma->client->method = $mockMethodResults;
        $ma->applicationId = '12345';
        
        $parser = new RingsideSocialDslParser( $ma );
        
        $results = $parser->parseString( $parseString );
        
        $this->assertEquals( $expected, trim($results), "$expected != $results" );
   }
    
   
   public static function makeExpectedResultsDiv ( $actions, $helps, $button, $appname, $appicon ) {

      $exp = '<div class="dashboard_header">';
      
      if ( !empty ( $actions )) {
         $exp .= ' 	<div class="dashboard_actions">';
         $pipe = '';
         foreach ( $actions as $action ) {            
            $href = isset( $action['href'] )? " href=\"{$action['href']}\"" : '';
            $title = isset( $action['title'] )? " title=\"{$action['title']}\"" : '';
            $onclick = isset( $action['onclick'] )? " onclick=\"{$action['onclick']}\"" : '';
            $exp .= '<a '.$href.$title.$onclick.'>'.$action['body'].'</a>';
            $pipe = '<span class="dashboard_pipe">|</span>';
         }
         $exp .= '	</div>';
      }

      if ( !empty ( $helps ))  {
         $exp .= ' 	<div class="dashboard_help">';
         $pipe = '';
         foreach ( $helps as $help ) {
            $href = isset( $help['href'] )? " href=\"{$help['href']}\"" : '';
            $title = isset( $help['title'] )? " title=\"{$help['title']}\"" : '';
            $exp .= '<a '.$href.$title.'>'.$help['body'].'</a>';
            $pipe = '<span class="dashboard_pipe">|</span>';
         }
         $exp .= '	</div>';
      }

      $exp .= '		<h2 style="background-image: url('.$appicon.')">'.$appname.'</h2>';
      
      if( !empty( $button) ) {
         $href = isset( $button['href'] )? " title=\"{$button['href']}\"" : '';
         $title = isset( $button['title'] )? " title=\"{$button['title']}\"" : '';
         $onclick = isset( $button['onclick'] )? " onclick=\"{$button['onclick']}\"" : '';
         $exp .= '	<div class="dashboard_button">';
         $exp .= '		<a '.$href.$title.$onclick.' class="dashboard_button_anchor"><span class="dashboard_button_text">'.$this->button['body'].'</span></a>';
         $exp .= '	</div>';
      }

      $exp .= '</div>';
      return $exp;
   }
      
   public static function makeExpectedResultsTable ( $actions, $helps, $button, $appname, $appicon, $canvas ) {

      $exp = '<table><tr class="dashboard_header">';
      
      if ( !empty ( $actions )) {
         $exp .= ' 	<td align="left" class="dashboard_actions">';
         $pipe = '';
         foreach ( $actions as $action ) {            
            $href = isset( $action['href'] )? " href=\"{$action['href']}\"" : '';
            $title = isset( $action['title'] )? " title=\"{$action['title']}\"" : '';
            $onclick = isset( $action['onclick'] )? " onclick=\"{$action['onclick']}\"" : '';
            $exp .= '<a '.$href.$title.$onclick.'>'.$action['body'].'</a>';
            $pipe = '&nbsp; | &nbsp;';
         }
         $exp .= '	</td>';
      }

      if ( !empty ( $helps ))  {
         $exp .= ' 	<td align="right" class="dashboard_help">';
         $pipe = '';
         foreach ( $helps as $help ) {
            $href = isset( $help['href'] )? " href=\"{$help['href']}\"" : '';
            $title = isset( $help['title'] )? " title=\"{$help['title']}\"" : '';
            $exp .= '<a '.$href.$title.'>'.$help['body'].'</a>';
            $pipe = '&nbsp; | &nbsp;';
         }
         $exp .= '	</td>';
      }
            
      $exp .= '</tr>';
      $exp .= '<tr>';

      $exp .= '	<td align="left" class="dashboard_title">';
      $exp .= '		<h2 style="background-image: url('.$appicon.')">'.$appname.'</h2>';
      $exp .= '	</td>';
      
      if( !empty( $button) ) {
         $href = isset( $button['href'] )? " title=\"{$button['href']}\"" : '';
         $title = isset( $button['title'] )? " title=\"{$button['title']}\"" : '';
         $onclick = isset( $button['onclick'] )? " onclick=\"{$button['onclick']}\"" : '';
         $exp .= '	<td align="right" class="dashboard_button">';
         $exp .= '		<a '.$href.$title.$onclick.' class="dashboard_button_anchor"><span class="dashboard_button_text">'.$this->button['body'].'</span></a>';
         $exp .= '	</td>';
      }
      
      $exp .= '</tr></table>';

      return $exp;
   }
}

?>
      
