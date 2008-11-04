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
class fbIframeHandler {

   function doStartTag($application, &$parentHandler, $args ) {
   	if ( ! isset($args['src']) ) {
   		echo "<!-- Required attribute 'src' not provided for fb:iframe tag -->";
   		return false;
   	}
   	
   	$scrolling = isset($args['scrolling'])?$args['scrolling']:'yes';
   	$width = isset($args['width'])?$args['width']:'646';
   	$height = isset($args['height'])?$args['height']:'800';
   	$name = isset($args['name'])?$args['name']:false;
   	$style = isset($args['style'])?$args['style']:false;
   	$frameborder = isset($args['frameborder'])?$args['frameborder']:1;
   	
   	if ( isset($args['smartsize']) ) {
   		echo "<!-- Unsupported attribute 'smartsize' used in fb:iframe -->";
   	}
   	if ( isset($args['resizable']) ) {
   		echo "<!-- Unsupported attribute 'resizable' used in fb:iframe -->";
   	}
   	
   	$iframe = "<iframe src='".$args['src']."' scrolling='$scrolling' width='$width' height='$height'".($name===false?'':" name='$name' id='$name'").($style===false?'':" style='$style'")." frameborder='".$frameborder."'></iframe>";

   	echo $iframe;
   	return false;
   }
   
   function doEndTag( $application, &$parentHandler, $args ) {
       
   }
   
   function isEmpty()
   {
   		return true;
   }
}
?>
