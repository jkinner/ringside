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
 * Create tabs
 *
 */
class fbTabsHandler {
    
   private $items = array();	
   private $leftItems = array();	
   private $rightItems = array();
    
   function addItem( $href, $title, $align, $selected="false" )  {

      if ( $href == null ) {
         echo "Action must specify a reference.";
         return;
      }
	  if( strcasecmp( $align, "left" ) == 0 ) {
	      $leftItem = array();
	      $leftItem['href']=$href;
	      $leftItem['title']=$title;
	      $leftItem['align']=$align;
	      $leftItem['selected']=$selected;
	      $this->leftItems[] = $leftItem;
	  }
	  if( strcasecmp( $align, "right" ) == 0 ) {
	      $rightItem = array();
	      $rightItem['href']=$href;
	      $rightItem['title']=$title;
	      $rightItem['align']=$align;
	      $rightItem['selected']=$selected;
	      $this->rightItems[] = $rightItem;
	  }
   }
    
   function doStartTag( $application, $parentHandler, $args ) {

      return array ( 'fb:tab_item', 'fb:tab-item' );

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

      echo $this->printTabs( $application );

   }

   /**
    * Output tabs using a semantic list. 
    *
    * @param unknown_type $application
    * @return unknown
    */
   private function printTabs($application) {

      $exp = '<div class="tabs"><div class="tabs_left"><ul>';
		 $leftTabsCount = 0;
		 $leftTabsSizeOfArray = count($this->leftItems);
		 $leftTabsClassAttr = '';
         foreach ( $this->leftItems as $leftItem ) {
            if( strcasecmp( $leftItem["align"], "left" ) == 0 ) {
         		$isselected = strcasecmp( $leftItem["selected"], "true") == 0;
				$leftTabsClassAttr = $isselected ? 'selected' : '';
				$leftTabsClassAttr .= ($leftTabsCount==0) ? ' first' : '';
				$leftTabsClassAttr .= ($leftTabsCount==count($this->leftItems)-1) ? ' last' : '';
            	$exp .= '<li><a href="'.$leftItem['href'].'" class="'.$leftTabsClassAttr.'">'.$leftItem['title'].'</a></li>' ;
            }
			$leftTabsCount++;
         }

      $exp .= '</ul></div>';
      $exp .= '<div class="tabs_right"><ul>';
		 $rightTabsCount = 0;
		 $rightTabsSizeOfArray = count($this->rightItems);
		 $rightTabsClassAttr = '';
         foreach ( $this->rightItems as $rightItem ) {
            if( strcasecmp( $rightItem["align"], "right" ) ==0 ) {
	         	$isselected = strcasecmp( $rightItem["selected"], "true") == 0;
				$rightTabsClassAttr = $isselected ? 'selected' : '';
				$rightTabsClassAttr .= ($rightTabsCount==0) ? ' first' : '';
				$rightTabsClassAttr .= ($rightTabsCount==count($this->rightItems)-1) ? ' last' : '';
            	$exp .= '<li><a href="'.$rightItem['href'].'" class="'.$rightTabsClassAttr.'">'.$rightItem['title'].'</a></li>';
            }
			$rightTabsCount++;
         }
      $exp .= '</ul></div></div>';
	  $exp .= '<div id="tabs-subnav"><div id="tabs-subnav-content">&nbsp;</div></div>';
      return $exp;
      
   }
   
	function getType()
   	{
   		return 'block';   	
   	}
}
?>
