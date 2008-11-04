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

class RingsideAppsCommon {

   const CANVASTYPE_DSL = 1;
   const CANVASTYPE_IFRAME = 0;
   const CANVASTYPE_OS = 2;
   
   const APPTYPE_WEB  = 'WEB';
   const APPTYPE_DESKTOP  = 'DESKTOP';
   const APPTYPE_MOBILE  = 'MOBILE';

   public static function load( $package, $file, $defaultFile = null, $error = null ) {
      $lastLevel = error_reporting( E_ERROR );

      $package = str_replace( '.', '/', $package );
      $result = include ( $package . '/' . $file . '.php' );
      if ( $result === false ) {
         error_log( $package . '/' . $file . ".php not loaded " );
         if ( $defaultFile != null ) {
            $result = include ( $package . '/' . $defaultFile . '.php' );
            if ( $result === false ) {
               error_log( 'DefaultFile ' . $package . '/' . $defaultFile . ".php not loaded " );
            }
         }
      }

      error_reporting( $lastLevel );
      return $result;
   }
}
?>
