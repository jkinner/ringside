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
 * Utility class for the parser object. 
 * TODO this should be killed and moved into HandlerHelper ;)
 */
class HandlerUtil {
   
   static function checkBoolArg($needle, $haystack, $default = false) {
      if ( isset($haystack[$needle]) ) {
         if ( strtolower($haystack[$needle]) == 'true' ) {
            return true;
         } else {
            return false;
         }
      } else {
         return $default;
      }
   }
   
   static function checkStringArg($needle, $haystack, $default = '') {
      if ( isset($haystack[$needle]) ) {
         return $haystack[$needle];
      }
      
      return $default;
   }
   
}
?>
