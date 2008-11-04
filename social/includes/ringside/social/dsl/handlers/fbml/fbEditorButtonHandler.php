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

class fbEditorButtonHandler {

   function doStartTag($application, &$parentHandler, $args ) {
      return 'body';
   }
   
   function doBody($application, &$parentHandler, $args, $body ) {
            
      $value = isset( $args['value'] ) ? $args['value'] : null;
      $name = isset( $args['name'] ) ? $args['name'] : null;
      $class = isset( $args['class'] ) ? $args['class'] : null;
      $id = isset( $args['id'] ) ? $args['id'] : null;
      $src = isset( $args['src'] ) ? $args['src'] : null;
      
      if ( $parentHandler != null && method_exists($parentHandler, 'addButton') ) {         
         return $parentHandler->addButton( $value, $name, $class, $id, $src);
      } 
   }
   
   function doEndTag( $application, &$parentHandler, $args ) {
       
   }
   
   function isEmpty()
   {
   		return true;
   }
     
	function getType()
   	{
   		return 'block';   	
   	}

}
?>
