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

class fbTimeHandler {

   function doStartTag($application, $parentHandler, $args ) {
      if ( !isset( $args['t'] ) || empty($args['t']) ) {
         echo 'RUNTIME ERROR: fb:name: Required attribute "t" not found in node fb:time';
         return false;
      }

      $t = $args['t'];
      $tz = isset($args['tz'])?$args['tz']:null;
      $preposition = isset($args['preposition'])?($args['preposition']=='true'?true:false):false;

      $defaultTZ = date_default_timezone_get();

      if ( $tz != null ) { 
         $tzs = timezone_identifiers_list();
         if ( array_search($tz, $tzs ) ) 
            date_default_timezone_set( $tz );
      }
         
      echo date( "F j, Y g:ia", $t );
      
      if ( $tz != null )
         date_default_timezone_set( $defaultTZ );
      
      // TODO handle preposition display
      
      return false;
   }
   
   function isEmpty()
   {
   		return true;
   }
   
	function getType()
   	{
   		return 'inline';   	
   	}

}
?>
