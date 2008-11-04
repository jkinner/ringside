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

require_once ( 'ringside/api/config/RingsideApiConfig.php' );

class RingsideApiDbDatabase {

   public static function db_connect() {

      if ( !mysql_connect( RingsideApiConfig::$db_server, RingsideApiConfig::$db_username, RingsideApiConfig::$db_password )) {
         error_log( "Database Connect Error (RingsideApiConfig::$db_server)");
         fail ( 2, 'The service is not available at this time : db connect failure' );
      } else if ( !mysql_select_db( RingsideApiConfig::$db_name ) ) {
         error_log( 'Database Select Error');
         fail ( 2, 'The service is not available at this time : db catalog failure' );
      }
   }

   public static function getConnection() { 
      
      $connect = mysql_connect( RingsideApiConfig::$db_server, RingsideApiConfig::$db_username, RingsideApiConfig::$db_password );
      if ( $connect === false ) {
         error_log ( "Database Connect Error " . mysql_error() );
         return false;
      }
      return $connect;
   }
   
   public static function closeConnection( $dbCon ) {
      return mysql_close( $dbCon );
   }
   
   public static function getDatabaseConnection() {

      $connect = mysql_connect( RingsideApiConfig::$db_server, RingsideApiConfig::$db_username, RingsideApiConfig::$db_password );
      if ( $connect === false ) {
         error_log ( "Database Connect Error " . mysql_error() );
         return false;
      }
       
      $db = mysql_select_db( RingsideApiConfig::$db_name, $connect );
      if ( $db === false ) {
         error_log ( "Database Select Error " . mysql_error() );
         return false;
      }

      return $connect;
   }

   /**
    * Takes a multi line sql, splits it by ; and the executes each line.
    * It will bomb out if any line is bad. 
    * There is no transactional behavior here. 
    *
    * @param unknown_type $sql
    * @param unknown_type $dbCon
    * @return true / false at the first line that fails.
    */
   public static function queryMultiLine( $sql, $dbCon ) {

      $queries = explode(';', $sql );
       
      foreach($queries as $query)
      {
         $query = trim($query);
         if( !empty($query) ) {
            if(!mysql_query($query.';', $dbCon))
            {
               $errmsg = "query ".$query." failed";
               error_log( "Failed (".mysql_error($dbCon).") query ($query) " . mysql_errno($dbCon) );
               return false;
            }
         }
      }
       
      return true;
       
   }

}
 
?>