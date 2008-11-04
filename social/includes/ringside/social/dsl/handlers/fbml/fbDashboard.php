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

/**
 * Dashboard tag is for drawing, you guessed it a dashboard.
 *
 */
class fbDashboard {
   
   private $button = null;
   private $actions = array();
   private $help = array();
   
   function setButton( $href, $title, $onclick, $body ) {
      if ( $this->button == null ) { 
         $this->button = array();
         $this->button['href']=$href;
         $this->button['title']=$title;
         $this->button['onclick']=$onclick;
         if ( $body == null ) {
            $body = '';
         }
         $this->button['body']=$body;
      }
   }
   
   function addAction( $href, $title, $onclick, $body )  {
      if ( $href == null ) {
         echo "Action must specify a reference.";
         return;
      }
      
      $action = array();
      $action['href']=$href;
      $action['title']=$title;
      $action['onclick']=$onclick;
      $action['body']=$body;
      $this->actions[] = $action;
   }
   
   function addHelp( $href, $title, $body ) {
      if ( $href == null ) {
         echo "Action must specify a reference.";
         return;
      }
      
      $help = array();
      $help['href']=$href;
      $help['title']=$title;
      if ( $body == null )
        $body='help';
      $help['body']=$body;
      $this->help[] = $help;
   }

   function doStartTag( $application, $parentHandler, $args ) {

      return array ( 'fb:help', 'fb:action', 'fb:create-button' );
                  
   }

   /**
    * At this point valid child tags are collected, so should be able
    * to just print the dashboard.
    *  
    *
    * @param unknown_type $application
    * @param unknown_type $parentHandler
    * @param unknown_type $args
    */
   function doEndTag( $application, $parentHandler, $args ) { 

      $this->printAsDiv( $application );
//      $this->printAsTable( $application );
      
   }

   private function printAsDiv($application) {
      $client = $application->getClient();
      $result = $client->admin_getAppProperties( "application_name,icon_url,canvas_url"  );
      $application_name = isset( $result['application_name'] )? $result['application_name'] : 'Unknown Application';
      $application_icon = isset( $result['icon_url'] ) ? $result['icon_url'] : ""; 
      $canvas_url = isset( $result['canvas_url'] ) ? $result['canvas_url'] : "";
      
      echo '<div class="dashboard_header">';
      
      if ( !empty ( $this->actions )) {
         echo ' 	<div class="dashboard_actions">';
         $pipe = '';
         foreach ( $this->actions as $action ) {
            $href = $this->makeAttribute( 'href', $action );
            $title = $this->makeAttribute( 'title', $action );
            $onclick = $this->makeAttribute( 'onclick', $action );
            echo $pipe . '<a '.$href.$title.$onclick.'>'.$action['body'].'</a>';
            $pipe = '<span class="dashboard_pipe">|</span>';
         }
         echo '	</div>';
      }

      if ( !empty ( $this->help ))  {
         echo ' 	<div class="dashboard_help">';
         $pipe = '';
         foreach ( $this->help as $help ) {
            $href = $this->makeAttribute( 'href', $help );
            $title = $this->makeAttribute( 'title', $help );
            echo $pipe . '<a '.$href.$title.'>'.$help['body'].'</a>';
            $pipe = '<span class="dashboard_pipe">|</span>';
         }
         echo '	</div>';
      }

//      echo '	<div class="dashboard_title">';
      echo '		<h2 '.($application_icon?'':'class="no_icon" ').'style="background-image: url('.$application_icon.')">'.$application_name.'</h2>';
//      echo '	</div>';
      
      if( !empty( $this->button) ) {
         $href = $this->makeAttribute( 'href', $this->button );
         $title = $this->makeAttribute( 'title', $this->button );
         $onclick = $this->makeAttribute( 'onclick', $this->button );
         echo '	<div class="dashboard_button">';
         echo '		<a '.$href.$title.$onclick.' class="dashboard_button_anchor">'.$this->button['body'].'</a>';
         echo '	</div>';
      }

      echo '</div>';
      
   }
   
   private function printAsTable($application) {
      $client = $application->getClient();
      $result = json_decode( $client->admin_getAppProperties( "application_name,icon_url,canvas_url" ), true );
      
      $application_name = isset( $result['application_name'] )? $result['application_name'] : '';
      $application_icon = isset( $result['icon_url'] ) ? $result['icon_url'] : ""; 
      $canvas = isset( $result['canvas_url'] ) ? $result['canvas_url'] : "";
      
      echo '<table><tr class="dashboard_header">';
      
      if ( !empty ( $this->actions )) {
         echo ' 	<td align="left" class="dashboard_actions">';
         $pipe = '';
         foreach ( $this->actions as $action ) {
            $href = $this->makeHREF( $action, $canvas );
            $title = $this->makeAttribute( 'title', $action );
            $onclick = $this->makeAttribute( 'onclick', $action );
            echo $pipe . '<a '.$href.$title.$onclick.'>'.$action['body'].'</a>';
            $pipe = '&nbsp; | &nbsp;';
         }
         echo '	</td>';
      }

      if ( !empty ( $this->help ))  {
         echo ' 	<td align="right" class="dashboard_help">';
         $pipe = '';
         foreach ( $this->help as $help ) {
            $href = $this->makeHREF( $help, $canvas );
            $title = $this->makeAttribute( 'title', $help );
            echo $pipe . '<a '.$href.$title.'>'.$help['body'].'</a>';
            $pipe = '&nbsp; | &nbsp;';
         }
         echo '	</td>';
      }
      
      echo '</tr>';
      echo '<tr>';

      echo '	<td align="left" class="dashboard_title">';
      echo '		<h2 style="background-image: url('.$application_icon.')">'.$application_name.'</h2>';
      echo '	</td>';
      
      if( !empty( $this->button) ) {
         $href = $this->makeHREF( $this->button, $canvas );
         $title = $this->makeAttribute( 'title', $this->button );
         $onclick = $this->makeAttribute( 'onclick', $this->button );
         echo '	<td align="right" class="dashboard_button">';
         echo '		<a '.$href.$title.$onclick.' class="dashboard_button_anchor"><span class="dashboard_button_text">'.$this->button['body'].'</span></a>';
         echo '	</td>';
      }

      echo '</tr></table>';
      
   }   

   private function makeAttribute( $attribute, $haystack ) {
         $response = '';
         if ( $haystack[$attribute] != null ) {
            $response = " $attribute=\"{$haystack[$attribute]}\"";
         }
         return $response;
   }
   
   /**
    * What should be the rules be for creating the HREF? Should this be centralized for an entire DSL handlers set?
    * In this case the rules are as follows
    * 1. If there is a scheme or host then spit out href='url' no change to url
    * 2. If there is no scheme/host but the url starts with '/' make it relative  href='url' no changes
    * 3. else prepend canvas url.
    *
    * @param unknown_type $haystack
    * @param unknown_type $canvas
    * @return unknown
    */
   private function makeHREF( $haystack, $canvas ) {

      $href = '';
      if ( $haystack['href'] != null ) {
         $href = $haystack['href'];
         
         $parts = parse_url( $href );
         if ( isset( $parts['scheme'] ) && !empty( $parts['scheme'] ) )  {
            ; // href is left alone
         } else if ( isset( $parts['host'] ) && !empty( $parts['host'] ) )  {
            ; // href is left alone
         } else if ( strpos( $href, '/' ) === false ) {
//            $append = '/';
//            if ( substr( $canvas, -1, 1 ) == '/' ) {
//               $append = '';
//            }
//            $href = $canvas . $append . $href;      
         } else  {
            ; // href is left alone and kept relative to server. 
         }
         
         $href = " href=\"$href\"";
      }
      
      return $href;
   }
   
	function getType()
   	{
   		return 'block';   	
   	}
   
}



?>
